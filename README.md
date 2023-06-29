# vetmanager-rest-api

![GitHub CI](https://github.com/otis22/vetmanager-rest-api/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/otis22/vetmanager-rest-api/badge.svg?branch=main)](https://coveralls.io/github/otis22/vetmanager-rest-api?branch=main)

![Vetmanager Logo](https://vetmanager.ru/wp-content/themes/vetmanager/images/headerLogo.svg)

Vetmanager - veterinary CRM with REST API. vetmanager-rest-api is a library to help working with REST API.


[vetmanager.ru](https://vetmanager.ru/)

[vetmanager REST API Docs](https://help.vetmanager.cloud/article/3029)

[vetmanager REST API in Postman](https://www.postman.com/vetmanager/workspace/vetmanager-api/collection/23836400-17133b76-0f52-4bb4-8b38-28a64781074e)

## What for?

1. To get full url by providing only domain name (host might change)
1. To validate model name by function uri()
1. To simplify apiKey and token authorization
1. For Sorting, Filtering, etc. 
1. To get all sorted and filtered records from model

** Example: Get latest invoice for client id=1 or id=2

```php
use GuzzleHttp\Client;
use function Otis22\VetmanagerUrl\url;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerRestApi\byApiKey;
use Otis22\VetmanagerRestApi\Query\Builder;

$client = new Client([
  'base_uri' => url("myclinic")->asString()
]);

$top = (new Builder())
    ->where('client_id', 'in', [1, 2])
    ->orderBy('invoice_date', 'desc')
    ->top(1);

$response = $client->request(
    'GET',
    uri("invoice")->asString(),
    [
        'headers' => byApiKey("myapikey")->asKeyValue(),
        'query' => $top->asKeyValue()
    ]
);
```

**Warning!** function `url` requires "otis22/vetmanager-url" package. 

## Installation

```bash
composer require otis22/vetmanager-rest-api
```

## Usage 

* [For auth](#usage-for-auth)
    1. [Api key auth](#api-key-auth)
    2. [With custom timezone](#with-custom-timezone)
    3. [Token auth](#token-auth)
    4. [Service API key auth](#service-api-key-auth)
* [For create valid URI](#usage-for-create-valid-uri)
    1. [Only model](#only-model)
    1. [Model with particular id](#model-with-particular-id)
* [For filtering and sorting data](#usage-for-filtering-and-sorting)  
    1. [Filter example](#how-to-use-filters)
    2. [Full available filter list](#full-filter-list)
    3. [Sorts example](#how-to-use-sorts)
    4. [Both example](#how-to-use-both-sorts-and-filters)
* [How to get all records](#how-to-get-all-records)  
* [How to get top n records](#how-to-get-top-n-records)
* [Query Builder](#query-builder)

### Usage for auth
#### Api key auth

```php
use Otis22\VetmanagerRestApi\Headers;
use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$authHeaders = new Headers\WithAuth(
    new Headers\Auth\ByApiKey(
        new ApiKey('test-key')
    )
);

$client->request(
    'GET',
    '/rest/api/user/1',
    ['headers' => $authHeaders->asKeyValue()]
);    
```
or with function 
```php
$authHeaders = Otis22\VetmanagerRestApi\byApiKey('test-key');
# use this after ['headers' => $authHeaders->asKeyValue()]
```
#### With custom timezone
```php
use Otis22\VetmanagerRestApi\Headers;
use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;

$myHeaders = [
    'X-REST-TIME-ZONE' => '+02:00'
];
$allHeaders = new Headers\WithAuthAndParams(
    new Headers\Auth\ByApiKey(
        new ApiKey('test-key')
    ),
    $myHeaders  
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    ['headers' => $allHeaders->asKeyValue()]
);    
```
#### Token auth
```php
use Otis22\VetmanagerRestApi\Headers;
use Otis22\VetmanagerToken;

$authHeaders = new Headers\WithAuth(
    new Headers\Auth\ByToken(
        new Credentials\AppName("myapp"),
        new Token\Concrete("mytoken")
    )
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    ['headers' => $authHeaders->asKeyValue()]
); 
```

or with function
```php
$authHeaders = Otis22\VetmanagerRestApi\byToken('myapp', 'mytoken');
# use this after ['headers' => $authHeaders->asKeyValue()]
```

### Service API key auth

```php
use Otis22\VetmanagerRestApi\Headers;
use Otis22\VetmanagerRestApi\Headers\Auth\ServiceName;
use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;

$authHeaders = new Headers\WithAuth(
    new Headers\Auth\ByServiceApiKey(
        new ServiceName('name')
        new ApiKey('key')
    )
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    ['headers' => $authHeaders->asKeyValue()]
); 
```
or with function
```php
$authHeaders = Otis22\VetmanagerRestApi\byServiceApiKey('service', 'api key');
# use this after ['headers' => $authHeaders->asKeyValue()]
```



### Usage example to create valid URI
#### Only model
```php
use Otis22\VetmanagerRestApi\URI;
use Otis22\VetmanagerRestApi\Model;

$uri = new URI\OnlyModel(
    new Model('invoice')
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

// request to /rest/api/invoice
$client->request('GET', $uri->asString()); 
 ```
or with function 
```php
$uriString = \Otis22\VetmanagerRestApi\uri('invoice')->asString();
```

#### Model with particular id
```php
use Otis22\VetmanagerRestApi\URI;
use Otis22\VetmanagerRestApi\Model;

$uri = new URI\WithId(
    new Model('invoice'),
    5
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

// request to /rest/api/invoice/5
$client->request('GET', $uri->asString()); 
```

or with function
```php
$uriString = \Otis22\VetmanagerRestApi\uri('invoice', 5)->asString();
```

### Usage example for filtering and sorting
#### How to use Filters

```php
use Otis22\VetmanagerRestApi\Query\Filters;
use Otis22\VetmanagerRestApi\Query\Filter\EqualTo;
use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;

$filters = new Filters(
    new EqualTo(
        new Property('propertyName'),
        new StringValue('propertyValue')
    )
    # ... we can use mach more filters new Filters($filterOne, $filterTwo, ... );
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    [
        'headers' => $authHeaders->asKeyValue(),
        'query' => $filters->asKeyValue()
    ]
); 
```
#### Full filter list
* Otis22\VetmanagerRestApi\Query\Filter\EqualTo - where property is equal to value
* Otis22\VetmanagerRestApi\Query\Filter\InArray - where property is in list
* Otis22\VetmanagerRestApi\Query\Filter\LessOrEqualThan - where property is less or equal than value
* Otis22\VetmanagerRestApi\Query\Filter\LessThan - where property is less than value
* Otis22\VetmanagerRestApi\Query\Filter\Like - where property is like value(for using MySQL LIKE)
* Otis22\VetmanagerRestApi\Query\Filter\MoreOrEqualThan - where property is more or equal than value
* Otis22\VetmanagerRestApi\Query\Filter\MoreThan - where property more than value
* Otis22\VetmanagerRestApi\Query\Filter\NotEqualTo - where propery is not equal to value
* Otis22\VetmanagerRestApi\Query\Filter\NotInArray - where property is not in list

#### How to use Sorts
```php
use Otis22\VetmanagerRestApi\Query\Sorts;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use Otis22\VetmanagerRestApi\Query\Sort\DescBy;
use Otis22\VetmanagerRestApi\Model\Property;

$sorts = new Sorts(
    new AscBy(
        new Property('propertyName')
    ), 
    new DescBy(
        new Property('property2Name')
    )
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    [
        'headers' => $authHeaders->asKeyValue(),
        'query' => $sorts->asKeyValue()
    ]
); 
```
#### How to use both Sorts and Filters
```php
use Otis22\VetmanagerRestApi\Query\Query;
... 

$query = new Query(
    new Filters(...),
    new Sorts(...)
);

$client = new GuzzleHttp\Client(['base_uri' => 'http://some.vetmanager.ru']);

$client->request(
    'GET',
    '/rest/api/user/1',
    [
        'headers' => $authHeaders->asKeyValue(),
        'query' => $query->asKeyValue()
    ]
); 
```

### How to get all records 

```php
$paged =  PagedQuery::forGettingAll(
    new Query(
        // Sorts Required!
    )
);
$result = [];
do {
    $response = json_decode(
        strval(
            $client->request(
                'GET',
                uri('invoice')->asString(),
                [
                    'headers' => $headers->asKeyValue(),
                    'query' => $paged->asKeyValue()
                ]
            )->getBody()
        ),
        true
    );
    $paged = $paged->next();
    $result = array_merge(
        $response['data']['invoice'],
        $result
    );
} while (count($result) < $response['data']['totalCount']);

```

### How to get top n records

```php
$top1 =  PagedQuery::forGettingTop(
    new Query(
        // Sorts Required!
    ),
    1
);
$response = json_decode(
    strval(
        $client->request(
            'GET',
            uri('invoice')->asString(),
            [
                'headers' => $headers->asKeyValue(),
                'query' => $top1->asKeyValue()
            ]
        )->getBody()
    ),
    true
);
```

### Query Builder

top(n) - return PagedQuery instance for getting top n records by user filter and sort order.  
```php
$top = (new Builder())
    ->where('client_id', 'in', [1, 2])
    ->orderBy('id', 'desc')
    ->top(1);
```

paginateAll(): return PagedQuery instance for getting all records by user filter and sort order
```php
$paginateAll = (new Builder())
    ->where('client_id', 'in', [1, 2])
    ->orderBy('id', 'desc')
    ->paginateAll();
```

paginate(limit, offset): return PagedQuery instance for getting records with custom limit and offset by user filter and sort order
```php
$paginate = (new Builder())
    ->where('client_id', 'in', [1, 2])
    ->orderBy('id', 'desc')
    ->paginate(10, 20);
```

You can use [filter class](#full-filter-list) string operator instead.
```php
use Otis22\VetmanagerRestApi\Query\Filter;

(new Builder())
    ->where('client_id', Filter\InArray::class, [1, 2])
    ->where('pet_id', Filter\NotEqualTo::class, 5)
```


## Contributing

To run all tests
```shell
make all
```
To connect to terminal
```shell
make exec
```
*Default php version is 8.1*. Use PHP_VERSION= to use your version. Only versions 8.0 and 8.1 are available for now.
```shell
make all PHP_VERSION=8.0
# run both 
make all PHP_VERSION=8.0 && make all PHP_VERSION=8.1
```

*For integration tests copy .env.example to .env and fill with yours values*

all commands
```shell
# security check
make security
# composer install
make install
# composer install with --no-dev
make install-no-dev
# check code style
make style
# run static analyze tools
make static-analyze
# run unit tests
make unit
#  check coverage
make coverage
# check integration, .env required
make integration
```
