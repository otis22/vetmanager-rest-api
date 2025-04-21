<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

use function getenv;
use function Otis22\VetmanagerUrl\url;

/**
 * Фабрика для создания экземпляров Guzzle HTTP Client.
 * Кеширует URL для повторного использования.
 */
final class ClientFactory
{
    /**
     * Кешированный базовый URL.
     *
     * @var string|null
     */
    private static ?string $baseUrl = null;

    /**
     * Создает новый экземпляр Guzzle HTTP Client.
     *
     * @return ClientInterface
     */
    public static function createClient(): ClientInterface
    {
        return new Client(['base_uri' => self::getBaseUrl()]);
    }

    /**
     * Возвращает кешированный базовый URL.
     *
     * @return string
     */
    private static function getBaseUrl(): string
    {
        if (self::$baseUrl === null) {
            self::$baseUrl = url((string)getenv('TEST_DOMAIN_NAME'))->asString();
        }

        return self::$baseUrl;
    }
}
