# PR #28: анализ devtr6 и план следующих совместимых шагов — 2026-10-21

## Вопрос и границы

Цель — отделить таймауты O2/O3 от фактического поведения test tenant `devtr6`
и сузить проверяемую очередь O4–O8b. Выполнены только GET с тестовым API key
либо без него. POST, PUT, DELETE и создание/изменение сущностей не выполнялись.
Ответы с данными не сохранялись и не выводились: только HTTP-статус,
`Content-Type`, верхние ключи JSON, `success` и длительность.

Контекст: [PR #28](https://github.com/otis22/vetmanager-rest-api/pull/28),
HEAD `9c5549baf8da7eda07f57c35eee89760fa976418`; 13 CI checks успешны в
[run 39590892909](https://github.com/otis22/vetmanager-rest-api/actions/runs/39590892909).
Локальные unit-проверки и CI данного SHA не повторялись: они уже зафиксированы
в `PRD/openapi-compatible-expansion.md`.

## Сначала: причина и масштаб таймаутов

Ранее полный `make integration` завершился exit 2 по внутреннему лимиту Composer
300 секунд; временный тест дважды получил `cURL error 28` через 20 с на
`ComboManualName?limit=1` и `MedicalCards?limit=1`. Поэтому серверная приёмка
O2/O3 не пройдена.

В 2026-10-21T14:22–14:24+03:00 `TEST_DOMAIN_NAME=devtr6` через
`vetmanager-url` разрешён в `devtr6.vetmanager2.ru`. Команда

```sh
curl -sS -o /dev/null -D - --connect-timeout 5 --max-time 15 \
  -w 'curl_exit=%{exitcode} http=%{http_code} remote_ip=%{remote_ip} connect=%{time_connect} starttransfer=%{time_starttransfer} total=%{time_total} content_type=%{content_type}\n' \
  'https://devtr6.vetmanager2.ru/rest/api/clinics?limit=1'
```

дала shell exit 0 / `curl_exit=0`, HTTP 401 JSON, connect 0,119 с, TTFB 0,259 с,
total 0,259 с. Существующий безопасный тест
`timeout 75 make integration_one testApiKey PHP_VERSION=8.3` дал exit 0:
1 test / 1 assertion за 0,973 с.

Значит, DNS/TLS/API и действующий test API key доступны в это окно. Это **не**
доказывает корень старых таймаутов: серверные/балансировочные логи недоступны.
Но сбой не воспроизведён как общий отказ хоста или на двух прежде зависших GET;
достоверная формулировка — временный сетевой или серверный сбой прежних запусков.

## Адресные результаты

Все запросы ниже исполнялись в Docker с `.env`, `connect_timeout=5`,
`timeout=15`, `http_errors=false`. Внутренний read-only PHP harness печатал
только указанные метаданные; командная оболочка завершилась exit 0:

```sh
timeout 120 docker run --env-file=.env --rm -v "$PWD":/app -w /app \
  vetmanager-rest-api:8.3 php -r '<read-only GET harness; 5 s connect, 15 s total>'
```

| Область | Путь и auth | Наблюдение | Достоверный вывод |
| --- | --- | --- | --- |
| O2 | `ComboManualName?limit=1`, API key | 200 JSON, `success=true`, 0,416 с | Вариант регистра доступен сейчас; другие методы не подтверждены. |
| O2 | `MedicalCards?limit=1`, API key | 200 JSON, `success=true`, 0,150 с | Имя доступно сейчас; это не заменяет серверную приёмку всего O2. |
| O2 | `VmLink?limit=1` → 500 HTML (0,160 с); `messages?limit=1` → 500 JSON (0,180 с); `report-ai-job?limit=1` → 404 JSON (0,120 с) | Базовый GET не даёт основания включать эти имена в `Model`. Наблюдённые 500 не провоцировались намеренно. |
| O3 | `MedicalCards/AllDiagnoses?limit=1` → 200 (0,230 с); `MedicalCards/MedcardsTextTemplates?limit=1` → 200 (0,569 с); `user/anonymousList?limit=1` → 200 (0,181 с) | Три точных GET-шаблона доступны. Не подтверждены остальные 16 шаблонов и серверное кодирование параметров. |
| O3 | `VmLink/personalAccountLinkByClientId/0?limit=1` → 404 (0,130 с); `report-ai-job/pr28-no-such-job/data` → 404 (0,109 с) | Маршрутизация достигла API и безопасно обработала отсутствующие ID; успешный бизнес-сценарий не проверен. |
| O4 | `clinics?limit=1`, API key → 200 JSON, 0,408 с | Это единственный безопасный стартовый кандидат для method-layer GET. |
| O7, 401 | без auth и с неверным `X-REST-API-KEY` на `clinics?limit=1` | оба 401 JSON, ключи `success,error,message`, `success=false`, 0,114/0,115 с | Подтверждён только API-key 401-контракт. |
| O7, 404 | API key, отсутствующий read-only путь | 404 JSON, ключи `success,message,data`, `success=false`, 0,121 с | Подтверждён один 404-контракт. |
| O7, 400 | API key, `clinics?limit=not-an-integer` | 200 JSON, `success=true`, 0,130 с | Это не пример 400 и не основание валидировать `limit` на клиенте. |

Не проверены 400, 403, 409, 422, 429 и типовые 5xx; их намеренное получение не
нужно для задачи. Локальный `.env` содержит только `TEST_API_KEY`, поэтому
token и service API key не проверены: отсутствие credentials не означает
отсутствие серверной поддержки.

## Что можно и нельзя подтвердить

Снимок 1.3.1: 137 операций (87 GET, 23 POST, 16 PUT, 11 DELETE), 36 схем,
39 `requestBody` (лишь один `required`). Из 19 `OpenApiPath` шаблонов 10 GET,
9 POST. POST-операции, включая `MedicalCards/AddVaccination`, `invoice/createinvoice`,
`messages/*` и `report-ai-job/*` запись, нельзя принимать без согласованных
payload, прав и политики очистки test data.

O5/O6/O8b не могут быть доказаны по ответам без клиентских данных: у схем нет
`required`, 150 свойств объявлены `nullable`, но это не доказательство точных
серверных типов/обязательности. `http_method_support: none` и
`schema_support: none` в текущей матрице остаются корректной границей библиотеки.

## Приоритизированный план для разработчика

### P0 — только после решения владельца, совместимо с PR #28

1. Не менять уже добавленные `Model` и `openApiPath()`. O2/O3 оставить
`in_progress`. При согласовании сетевого гейта добавить ровно пять GET:
`ComboManualName`, `MedicalCards`, `MedicalCards/AllDiagnoses`,
`MedicalCards/MedcardsTextTemplates`, `user/anonymousList` с `limit=1`, timeout
5/15 с; проверять HTTP 200, JSON, `success=true`, не сохранять body. Приёмка:
два последовательных запуска devtr6 без timeout, таблица passed/failed/skipped
и длительность. Таймаут оставляет статус незавершённым, не ломает unit/CI.
2. Не добавлять `VmLink`, `messages`, `report-ai-job` в `Model`, пока не будет
документированного базового GET 2xx или upstream-контракта model-помощника.

### P1 — отдельный совместимый follow-up

3. O4: спроектировать опциональный транспорт лишь для
`GET /rest/api/clinics?limit=1`; не менять `uri`, `Builder`, заголовки и старые
исключения. Unit передаёт method/path/query/headers в `ClientInterface`, body
для GET отсутствует. Интеграция проверяет 200/JSON/`success=true`. POST/PUT/DELETE
требуют per-operation payload, статус, права и очистку.
4. O7: выделить read-only probe runner, выводящий только status, allowlisted
headers и JSON shape. Приёмка: API-key 200, 401 (нет/неверный key), 404 (путь);
token/service — `skipped` до test credentials. Не провоцировать 403/409/422/429/5xx.
После подтверждения сначала обновить upstream OpenAPI, затем snapshot, затем
обсуждать error mapper.

### P2 — исследование; не минорный код

5. O5/O6/O8b: запросить безопасный источник метаданных или анонимизированные
ответы и отдельные test records для одного write-сценария. Для каждой схемы
фиксировать источник, дату, operationId, поля, типы, nullable, обязательность
и знаменатель. До этого нет ни DTO, ни глобального валидатора.
6. Major-only: удаление legacy-имён, нормализация регистра, изменение
`uri()`/`Model::asString()`, строгая валидация `Builder`, смена `asKeyValue()`
или отказ от token/service API key. Нужны инвентаризация потребителей,
migration API, депрекация и решение владельца.

## Ограничения и статус

Это один test tenant и окно 2026-10-21, не production. 200 подтверждает лишь
конкретный GET в это время, не схему/тело и не запись. O2/O3 остаются
`in_progress`, O4–O8b — `todo`; серверную приёмку PR #28 по этому отчёту не
объявлять пройденной.
