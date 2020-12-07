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

* [For auth](#usage-for-auth)
    1. [Api key auth](#api-key-auth)
    1. [With custom timezone](#with-custom-timezone)
    1. [Token auth](#token-auth)
* [For create valid URI](#usage-for-create-valid-uri)
    1. [Only model](#only-model)
    1. [Model with particular id](#model-with-particular-id)


### Usage for auth
#### Api key auth
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$authHeaders = new Otis22\VetmanagerRestApi\Headers\WithAuth(
    new \Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey(
        new \Otis22\VetmanagerRestApi\Headers\Auth\ApiKey('test-key')
    )
);

$client->request('GET', '/rest/api/user/1', [
    'headers' => $authHeaders->asKeyValue()
]);    
```
#### With custom timezone
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$myHeaders = [
    'X-REST-TIME-ZONE' => '+02:00'
];
$allHeaders = new Otis22\VetmanagerRestApi\Headers\WithAuthAndParams(
    new \Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey(
        new \Otis22\VetmanagerRestApi\Headers\Auth\ApiKey('test-key')
    ),
    $myHeaders  
);

$client->request('GET', '/rest/api/user/1', [
    'headers' => $allHeaders->asKeyValue()
]);    
```
#### Token auth
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$authHeaders = new Otis22\VetmanagerRestApi\Headers\WithAuth(
    new \Otis22\VetmanagerRestApi\Headers\Auth\ByToken(
        new \Otis22\VetmanagerToken\Credentials\AppName("myapp"),
        new \Otis22\VetmanagerToken\Token\Concrete("mytoken")
    )
);

$client->request('GET', '/rest/api/user/1', [
    'headers' => $authHeaders->asKeyValue()
]); 
```

### Usage for create valid URI
#### Only model
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$uri = new \Otis22\VetmanagerRestApi\URI\OnlyModel(
    new \Otis22\VetmanagerRestApi\Model('invoice')
);

// request to /rest/api/invoice
$client->request('GET', $uri->asString()); 
 ```

#### Model with particular id
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$uri = new \Otis22\VetmanagerRestApi\URI\WithId(
    new \Otis22\VetmanagerRestApi\Model('invoice'),
    5
);

// request to /rest/api/invoice/5
$client->request('GET', $uri->asString()); 
```

## Contributing


### Run docker container
```
cd docker
docker-compose up
```

now you can connect to container

```
docker exec -it vetmanager-rest-api /bin/bash
```

### Run tests

```
#run all tests

composer check-all
```
