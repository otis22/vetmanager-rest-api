# vetmanager-rest-api

![GitHub CI](https://github.com/otis22/vetmanager-rest-api/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-rest-api/badge.svg?branch=main)](https://coveralls.io/github/otis22/vetmanager-rest-api?branch=main)


Vetmanager - CRM for veterinary with REST API. vetmanager-rest-api is library will help work with them.


[vetmanager.ru](https://vetmanager.ru/)

[vetmanager REST API Docs](https://vetmanager.ru/knowledgebase/rest-api-osnovnaya-informatsia)

[vetmanager REST API in Postman](https://god.postman.co/run-collection/64d692ca1ea129218ccb)

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