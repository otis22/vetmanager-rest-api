# vetmanager-rest-api

## Installation

```bash
composer require otis22/vetmanager-rest-api
```
## Usage 
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$authHeaders = new Otis22\VetmanagerRestApi\Headers\WithAuth(
    new \Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey(
        new \Otis22\VetmanagerRestApi\Headers\Auth\ApiKey('test-key')
    )
);

$client->request('GET', '/rest/user/1', [
    'headers' => $authHeaders->asKeyValue()
]);
    
```