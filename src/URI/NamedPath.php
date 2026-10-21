<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use Otis22\VetmanagerRestApi\URI;

/**
 * Builds a URI for a named server route.
 */
final class NamedPath implements URI
{
    public const TEMPLATES = [
        'MedicalCards/AddVaccination',
        'MedicalCards/AllDiagnoses',
        'MedicalCards/MedcardsTextTemplates',
        'MedicalCards/MedicalcardsDataByClient',
        'MedicalCards/Vaccinations',
        'VmLink/personalAccountLinkByClientId/{clientId}',
        'VmLink/personalAccountLinkByPhone/{phone}',
        'invoice/createinvoice',
        'messages/all',
        'messages/reports',
        'messages/roles',
        'messages/users',
        'report-ai-job/{id}/confirm',
        'report-ai-job/{id}/data',
        'report-ai-job/{id}/save',
        'report/StartReport',
        'report/reportFile',
        'stores/RestOfGoodInWarehouse',
        'user/anonymousList',
    ];

    /** @var string */
    private $template;

    /** @var array<string, string|int> */
    private $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(string $template, array $parameters = [])
    {
        if (!in_array($template, self::TEMPLATES, true)) {
            throw new \InvalidArgumentException('Unknown named path template');
        }

        preg_match_all('/\{([A-Za-z][A-Za-z0-9]*)\}/', $template, $matches);
        $names = $matches[1];
        $provided = array_keys($parameters);
        sort($names);
        sort($provided);
        if ($names !== $provided) {
            throw new \InvalidArgumentException('Path parameters do not match the template');
        }
        /** @var array<string, string|int> $validated */
        $validated = [];
        foreach ($parameters as $name => $value) {
            if (!is_string($value) && !is_int($value)) {
                throw new \InvalidArgumentException('Path parameter must be a string or integer');
            }
            if ((string)$value === '' || (string)$value === '.' || (string)$value === '..') {
                throw new \InvalidArgumentException('Path parameter is empty or a dot segment');
            }
            $validated[$name] = $value;
        }
        $this->template = $template;
        $this->parameters = $validated;
    }

    public function asString(): string
    {
        $path = preg_replace_callback(
            '/\{([A-Za-z][A-Za-z0-9]*)\}/',
            function (array $match): string {
                return rawurlencode((string)$this->parameters[$match[1]]);
            },
            $this->template
        );
        return (new RestApiPrefix())->asString() . $path;
    }
}
