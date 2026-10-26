#!/usr/bin/env python3
"""Check the pinned OpenAPI snapshot and explicit library capability matrix."""

import hashlib
import json
import re
import sys
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
SNAPSHOT = ROOT / "docs/openapi/vetmanager_openapi_v6.json"
YAML = ROOT / "docs/openapi/vetmanager_openapi_v6.yaml"
MATRIX = ROOT / "docs/openapi/operation-support.json"
EXPECTED_HASHES = {
    SNAPSHOT: "b2ff6c1b289e074710e3552a369f4da916d215ea4bdb670958066981244169c8",
    YAML: "88dc8f2ef089eaabaf074127d40414be9ae9d15c8f730650f11520cb2893d13b",
}
METHODS = {"get", "post", "put", "delete", "patch", "head", "options", "trace"}
SUPPORTED_METHODS = {
    ("GET", "/rest/api/clinics"): "clinics_get",
    ("GET", "/rest/api/MedicalCards/AllDiagnoses"): "medical_cards_all_diagnoses_get",
    ("GET", "/rest/api/MedicalCards/MedcardsTextTemplates"): "medical_cards_medcards_text_templates_get",
    ("GET", "/rest/api/user/anonymousList"): "user_anonymous_list_get",
    ("POST", "/rest/api/Unit"): "unit_post",
    ("PUT", "/rest/api/Unit/{ID}"): "unit_put",
}


def verify_hashes():
    for path, expected in EXPECTED_HASHES.items():
        actual = hashlib.sha256(path.read_bytes()).hexdigest()
        if actual != expected:
            raise ValueError("snapshot hash mismatch: " + path.name)


def verify_refs(node, document):
    if isinstance(node, dict):
        for key, value in node.items():
            if key == "$ref":
                if not isinstance(value, str) or not value.startswith("#/"):
                    raise ValueError("nonlocal or invalid $ref: " + str(value))
                target = document
                for part in value[2:].split("/"):
                    part = part.replace("~1", "/").replace("~0", "~")
                    if not isinstance(target, dict) or part not in target:
                        raise ValueError("unresolved $ref: " + value)
                    target = target[part]
            else:
                verify_refs(value, document)
    elif isinstance(node, list):
        for value in node:
            verify_refs(value, document)


def model_names():
    source = (ROOT / "src/Model.php").read_text()
    match = re.search(r"private \$validModels = \[(.*?)\];", source, re.S)
    if match is None:
        raise ValueError("Model allowlist not found")
    return set(re.findall(r"'([^']+)'", match.group(1)))


def named_templates():
    source = (ROOT / "src/URI/NamedPath.php").read_text()
    match = re.search(r"public const TEMPLATES = \[(.*?)\];", source, re.S)
    if match is None:
        raise ValueError("named templates not found")
    return set(re.findall(r"'([^']+)'", match.group(1)))


def expected_matrix(document):
    clinics_method = (ROOT / "src/Methods/Clinics.php").read_text()
    if "uri('clinics')->asString()" not in clinics_method or "'GET'" not in clinics_method:
        raise ValueError("clinics method support differs from source")
    unit_method = (ROOT / "src/Methods/Unit.php").read_text()
    if ("uri('Unit')->asString()" not in unit_method or "'POST'" not in unit_method
            or "uri('Unit', $id)->asString()" not in unit_method or "'PUT'" not in unit_method):
        raise ValueError("unit POST/PUT support differs from source")
    for source_file, template in (
        ("MedicalCardsAllDiagnoses.php", "MedicalCards/AllDiagnoses"),
        ("MedicalCardsMedcardsTextTemplates.php", "MedicalCards/MedcardsTextTemplates"),
        ("UserAnonymousList.php", "user/anonymousList"),
    ):
        source = (ROOT / "src/Methods" / source_file).read_text()
        if "namedPath('" + template + "')->asString()" not in source or "'GET'" not in source:
            raise ValueError("method support differs from source: " + source_file)
    names = model_names()
    named = named_templates()
    paths = document["paths"]
    if len(paths) != 104:
        raise ValueError("unexpected path count")
    unusual = set()
    result = []
    seen_ids = set()
    for path in sorted(paths):
        if path.startswith("/rest/api/"):
            route = path[len("/rest/api/"):]
            parts = route.split("/")
            standard = len(parts) == 1 or (
                len(parts) == 2 and parts[1] in ("{ID}", "{id}")
            )
            if not standard:
                unusual.add(route)
            uri_support = "model_uri" if standard and parts[0] in names else (
                "named_path" if route in named else "none"
            )
        else:
            uri_support = "none"
        for method, operation in sorted(paths[path].items()):
            if method not in METHODS:
                continue
            operation_id = operation.get("operationId")
            if not operation_id or operation_id in seen_ids:
                raise ValueError("missing or duplicate operationId: " + str(operation_id))
            seen_ids.add(operation_id)
            result.append({
                "method": method.upper(),
                "path": path,
                "operationId": operation_id,
                "uri_support": uri_support,
                "http_method_support": SUPPORTED_METHODS.get((method.upper(), path), "none"),
                "schema_support": "none",
            })
    if unusual != named:
        raise ValueError("named route templates differ from OpenAPI snapshot")
    if len(result) != 137:
        raise ValueError("unexpected operation count")
    return result


def main():
    verify_hashes()
    document = json.loads(SNAPSHOT.read_text())
    verify_refs(document, document)
    expected = expected_matrix(document)
    output = json.dumps(expected, ensure_ascii=False, indent=2) + "\n"
    if len(sys.argv) == 2 and sys.argv[1] == "--refresh":
        MATRIX.write_text(output)
    elif len(sys.argv) != 1 or not MATRIX.exists() or MATRIX.read_text() != output:
        raise ValueError("operation support matrix is stale; run --refresh and review the diff")
    print("OpenAPI contract valid: 104 paths, 137 operations, 19 named templates")


if __name__ == "__main__":
    try:
        main()
    except (KeyError, ValueError) as error:
        print("OpenAPI contract check failed: " + str(error), file=sys.stderr)
        sys.exit(1)
