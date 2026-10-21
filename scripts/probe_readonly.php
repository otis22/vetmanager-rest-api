<?php

declare(strict_types=1);

/**
 * Print sanitized read-only API status and JSON shape from the devtr6 tenant.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Otis22\VetmanagerRestApi\Support\ClientFactory;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\uri;

if (getenv('TEST_DOMAIN_NAME') !== 'devtr6' || !getenv('TEST_API_KEY')) {
    fwrite(STDERR, "TEST_DOMAIN_NAME=devtr6 and TEST_API_KEY are required\n");
    exit(2);
}

$client = ClientFactory::createClient();
$path = uri('clinics')->asString();
$cases = [
    'api_key_200' => [$path, byApiKey((string)getenv('TEST_API_KEY'))->asKeyValue()],
    'no_auth_401' => [$path, []],
    'wrong_key_401' => [$path, byApiKey('pr28-invalid-key')->asKeyValue()],
    'missing_path_404' => ['/rest/api/pr28-no-such-readonly-path', byApiKey((string)getenv('TEST_API_KEY'))->asKeyValue()],
];
$expected = ['api_key_200' => 200, 'no_auth_401' => 401, 'wrong_key_401' => 401, 'missing_path_404' => 404];
$failed = false;
foreach ($cases as $name => $case) {
    $start = microtime(true);
    try {
        $response = $client->request('GET', $case[0], [
            'headers' => $case[1],
            'query' => ['limit' => 1],
            'connect_timeout' => 5,
            'timeout' => 15,
            'http_errors' => false,
        ]);
        $json = json_decode((string)$response->getBody(), true);
        $shape = is_array($json) ? array_keys($json) : [];
        $status = $response->getStatusCode();
        $type = $response->getHeaderLine('Content-Type');
        $success = is_array($json) ? ($json['success'] ?? null) : null;
        $passed = $status === $expected[$name]
            && strpos($type, 'application/json') !== false
            && $success === ($expected[$name] === 200);
        $failed = $failed || !$passed;
        echo json_encode([
            'case' => $name,
            'status' => $status,
            'content_type' => $type,
            'json_keys' => $shape,
            'success' => $success,
            'seconds' => round(microtime(true) - $start, 3),
            'passed' => $passed,
        ], JSON_UNESCAPED_SLASHES) . PHP_EOL;
    } catch (\Throwable $error) {
        $failed = true;
        echo json_encode([
            'case' => $name,
            'error_type' => get_class($error),
            'seconds' => round(microtime(true) - $start, 3),
            'passed' => false,
        ]) . PHP_EOL;
    }
}
exit($failed ? 1 : 0);
