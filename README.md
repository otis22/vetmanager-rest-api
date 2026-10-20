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


## Разработка

### Совместимость PHP и зависимости

`composer.json` объявляет `>=7.3 || ^8.0` (по смыслу `>=7.3`) без верхней
границы. Это ограничение для установки, а не обещание, что каждая будущая версия
PHP уже проверена. В текущей обязательной CI-матрице PR настроены задания для PHP 7.3, 7.4 и
8.0–8.3. Расширение проверок до 8.4 и 8.5 — цель технического долга; локальные
unit-прогоны на крайних версиях сами по себе не заменяют зелёную матрицу CI,
интеграцию и проверку потребителей. В текущей линии сохраняются синтаксис PHP
7.3 и публичные сигнатуры/сериализация библиотеки.

`composer.lock` сейчас игнорируется Git. Поэтому чистый `composer install`
разрешает зависимости под фактическую версию PHP и создаёт локальный lock;
повторный запуск уже использует его. Lock от PHP 8.x может содержать dev-пакеты,
которые нельзя установить на 7.3. Для проверки другой версии используйте
отдельную чистую копию без локальных `composer.lock`, `vendor/` и `build/`.
Текущая CI тоже разрешает зависимости заново при каждом запуске: результат
зависит от состояния репозиториев пакетов. [Composer рекомендует хранить lock
для разработки библиотеки](https://getcomposer.org/doc/02-libraries.md#lock-file)
и отдельно проверять новые и минимальные версии зависимостей. Выбор такой
схемы для проекта — отдельный этап; один lock нынешней PHP 8.x-среды нельзя
считать общим для 7.3 и 8.5.

Dockerfile устанавливает актуальный совместимый Composer 2.x, а CI сейчас
скачивает актуальный стабильный Composer без pin. Это меняющийся со временем
набор инструментов; обе среды и разрешённые пакеты нужно записывать вместе с
результатом проверки. Для каждой версии PHP в чистой копии выполните:

```shell
make all PHP_VERSION=7.3         # или 8.5; каждый раз отдельная чистая копия
docker run --rm vetmanager-rest-api:7.3 php -v
docker run --rm vetmanager-rest-api:7.3 composer --version
docker run --rm -v "$PWD":/app -w /app vetmanager-rest-api:7.3 composer show --locked
sha256sum composer.lock
```

Для 8.5 замените `7.3` на `8.5` во всех командах. Сохраните сами команды,
коды завершения и версии PHP/Composer в результате PR. `make all` выполняет
unit-проверки; интеграция на devtr6 запускается отдельно по правилам ниже.

`make all` собирает Docker-образ, устанавливает зависимости, проверяет стиль и
статический анализ, запускает unit-тесты и проверяет покрытие не ниже 90%.
Версия PHP по умолчанию в `Makefile` — 8.3. Для другой версии задайте
`PHP_VERSION`, например `make all PHP_VERSION=8.2`. В CI проверяются версии
PHP 7.3–8.3 командами Composer. При смене версии PHP используйте чистую
рабочую копию: локальный `composer.lock` не отслеживается Git и может
содержать зависимости, несовместимые с другой версией PHP.

```shell
make help                 # список целей Makefile
make build                # Docker-образ
make install              # зависимости, включая dev
make install-no-dev       # только production-зависимости
make style                # PHPCS, PSR-12
make static-analyze       # PHPStan, level 8
make unit                 # unit-тесты и покрытие
make exec                 # интерактивная оболочка контейнера
```

Интеграционные тесты запускаются отдельно: скопируйте `.env.example` в `.env`,
заполните тестовые учётные данные devtr6 и выполните `make integration`.
Если ключ хранится в `vetmanager-mcp/.env`, перенесите `TEST_API_KEY` локально,
а `TEST_DOMAIN` запишите здесь как `TEST_DOMAIN_NAME=devtr6`. Проверка
service API key пропускается, если `SERVICE_NAME` и `SERVICE_API_KEY` пусты.
Не коммитьте `.env`. Для одного теста: `make integration_one TestName`.
В GitHub Actions интеграция запускается отдельно через `workflow_dispatch`;
обычная матрица PR проверяет стиль, анализ, unit-тесты и покрытие.
Целей `security` и `coverage` в `Makefile` нет.
