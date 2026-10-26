# O5: доказательная матрица схем и request body — 2026-10-26

## Вопрос, границы и версия

Вопрос: достаточно ли фиксированного OpenAPI-снимка и безопасных наблюдений
`devtr6`, чтобы добавить DTO либо валидацию тел запроса. Ответ: **нет**.
Документ фиксирует наблюдаемое, а не разрешает write-запросы.

Проверка выполнена 2026-10-26 11:14–11:16 MSK на HEAD
`62c577df79e614d943543a3d9590ce21c11a216f` (PR #29). PR #29 открыт,
mergeable; его 13 CI checks успешны. Он добавляет только три подтверждённых
GET и не меняет схемы/тела. Снимок —
`docs/openapi/vetmanager_openapi_v6.json`, OpenAPI 1.3.1, pinned upstream
`e2769471bf46d13e1334b8d09252a7456a5af579`.

Границы: только GET с `limit=1`, только технические метаданные БД и
обезличенная форма ответа; не читались значения полей, персональные данные,
токены и credentials. POST/PUT/DELETE, SQL-записи, создание тестовых данных и
изменение Redash не выполнялись. Метаданные хранения не равны HTTP-контракту.

## Источники и методы

| Источник | Время/версия | Что проверено | Ограничение |
| --- | --- | --- | --- |
| Закреплённый JSON-снимок | upstream `e2769471`, HEAD `62c577d` | парсинг 36 схем, 39 `requestBody`, `required`, `nullable`, типы | декларация, не наблюдение сервера |
| `scripts/check_openapi_contract.py` | 2026-10-26, exit 0 | 104 пути, 137 операций, 19 named templates | не проверяет значения/запись |
| Redash DS 6, `information_schema` схемы `vm_devtr6` | 2026-10-26T08:14:32–08:14:58Z | наличие 285 таблиц; столбцы пяти безопасных справочников | физическая БД, не API; PII-фильтр отказал запросу с запрещённым именем, поэтому он не повторялся |
| devtr6 GET harness | 2026-10-26 11:15 MSK, exit 0 | 200 и форма одного элемента `Unit`, `city`, `cityType`, `GoodGroup` | один элемент на ресурс; только response, не request body |

Определение знаменателя: «проверенная схема» означает, что все свойства и
правила request body этой схемы подтверждены отдельным допустимым и
отклоняемым сценарием на devtr6. По этому определению подтверждено **0/36**;
форма одного GET-ответа наблюдалась для **4/36**. «Проверенная операция с
body» требует безопасного согласованного тестового record, допустимого тела,
обязательного поля и управляемого cleanup; подтверждено **0/39**.

## Матрица схем снимка

`props` — число объявленных свойств; `null` — число с `nullable: true`;
`body` — число операций из матрицы ниже, ссылающихся на схему. Пустой
`required` у всех 36 схем — факт снимка, а не вывод, что поля серверу не нужны.

| Схема | props | null | body | Статус доказательства |
| --- | ---: | ---: | ---: | --- |
| admission | 22 | 8 | 2 | нет |
| breed | 4 | 1 | 0 | нет |
| timesheet | 12 | 0 | 0 | нет |
| cassa | 16 | 2 | 0 | нет |
| cassaclose | 10 | 5 | 0 | нет |
| client | 32 | 7 | 2 | нет: потенциальные ПД не читались |
| anonymousClient | 7 | 2 | 0 | нет |
| city | 3 | 0 | 2 | GET-форма подтверждена; body нет |
| cityType | 2 | 1 | 2 | GET-форма подтверждена; body нет |
| clinics | 15 | 10 | 0 | нет |
| comboManualItem | 9 | 1 | 2 | нет |
| comboManualName | 5 | 0 | 0 | нет |
| good | 19 | 9 | 2 | нет |
| goodGroup | 6 | 2 | 2 | GET-форма подтверждена; body нет |
| partyAccount | 9 | 3 | 0 | нет |
| partyAccountDoc | 11 | 6 | 0 | нет |
| goodSaleParam | 16 | 7 | 1 | нет |
| hospital | 17 | 4 | 1 | нет |
| hospitalBlock | 8 | 0 | 0 | нет |
| invoice | 23 | 11 | 0 | нет |
| invoiceDocument | 23 | 10 | 0 | нет |
| medicalCards | 22 | 9 | 3 | нет |
| medicalcards | 3 | 0 | 0 | нет |
| diagnoses | 3 | 0 | 0 | нет |
| payment | 12 | 4 | 0 | нет |
| closingOfInvoices | 12 | 10 | 0 | нет |
| pet | 23 | 18 | 2 | нет |
| petType | 5 | 2 | 0 | нет |
| properties | 5 | 2 | 0 | нет |
| role | 3 | 0 | 0 | нет |
| storeDocument | 20 | 3 | 0 | нет |
| street | 5 | 1 | 2 | нет: метаданные недоступны через PII-фильтр |
| suppliers | 16 | 6 | 1 | нет |
| unit | 3 | 0 | 2 | GET-форма, минимальные POST/PUT наблюдались; полная схема body не подтверждена |
| user | 21 | 6 | 1 | нет: потенциальные ПД не читались |
| userPosition | 3 | 0 | 2 | нет |

## Матрица 39 операций с requestBody

Все content type — `application/json`. Лишь `POST invoice/createinvoice`
помечен в OpenAPI как `requestBody.required: true`; у остальных 38 — `false`.
`inline` означает, что body не ссылается на одну из 36 `components.schemas`.

| Метод | Путь | operationId | Схема | required | Доказательство |
| --- | --- | --- | --- | --- | --- |
| POST | `ComboManualItem` | `post_rest_api_ComboManualItem` | comboManualItem | нет | нет |
| PUT | `ComboManualItem/{ID}` | `put_rest_api_ComboManualItem_ID` | comboManualItem | нет | нет |
| POST | `GoodGroup` | `post_rest_api_GoodGroup` | goodGroup | нет | нет |
| PUT | `GoodGroup/{ID}` | `put_rest_api_GoodGroup_ID` | goodGroup | нет | нет |
| POST | `MedicalCards` | `post_rest_api_MedicalCards` | medicalCards | нет | нет |
| POST | `MedicalCards/AddVaccination` | `post_rest_api_MedicalCards_AddVaccination` | medicalCards | нет | нет |
| PUT | `MedicalCards/{ID}` | `put_rest_api_MedicalCards_ID` | medicalCards | нет | нет |
| POST | `Suppliers` | `post_rest_api_Suppliers` | suppliers | нет | нет |
| POST | `Unit` | `post_rest_api_Unit` | unit | нет | два минимальных JSON POST → 201; очистка подтверждена |
| PUT | `Unit/{ID}` | `put_rest_api_Unit_ID` | unit | нет | минимальный JSON → 201; null/unknown → 406; 16-символьный маркер → 201; 17 символов не проверены |
| POST | `admission` | `post_rest_api_admission` | admission | нет | нет |
| PUT | `admission/{ID}` | `put_rest_api_admission_ID` | admission | нет | нет |
| POST | `city` | `post_rest_api_city` | city | нет | нет |
| PUT | `city/{ID}` | `put_rest_api_city_ID` | city | нет | нет |
| POST | `cityType` | `post_rest_api_cityType` | cityType | нет | нет |
| PUT | `cityType/{ID}` | `put_rest_api_cityType_ID` | cityType | нет | нет |
| POST | `client/{ID}` | `post_rest_api_client_ID` | client | нет | нет |
| PUT | `client/{ID}` | `put_rest_api_client_ID` | client | нет | нет |
| POST | `good` | `post_rest_api_good` | good | нет | нет |
| PUT | `good/{ID}` | `put_rest_api_good_ID` | good | нет | нет |
| PUT | `goodSaleParam/{ID}` | `put_rest_api_goodSaleParam_ID` | goodSaleParam | нет | нет |
| POST | `goodTag` | `post_rest_api_goodTag` | inline | нет | нет |
| PUT | `goodTag/{id}` | `put_rest_api_goodTag_203` | inline | нет | нет |
| PUT | `hospital/{ID}` | `put_rest_api_hospital_ID` | hospital | нет | нет |
| POST | `invoice/createinvoice` | `post_rest_api_invoice_createinvoice` | inline | да | нет |
| POST | `messages/all` | `post_rest_api_messages_all` | inline | нет | нет |
| POST | `messages/roles` | `post_rest_api_messages_roles` | inline | нет | нет |
| POST | `messages/users` | `post_rest_api_messages_users` | inline | нет | нет |
| POST | `pet` | `post_rest_api_pet` | pet | нет | нет |
| PUT | `pet/{ID}` | `put_rest_api_pet_ID` | pet | нет | нет |
| POST | `report-ai-job` | `post_rest_api_report_ai_job` | inline | нет | нет |
| POST | `report-ai-job/{id}/confirm` | `post_rest_api_report_ai_job_id_confirm` | inline | нет | нет |
| POST | `report-ai-job/{id}/save` | `post_rest_api_report_ai_job_id_save` | inline | нет | нет |
| POST | `street` | `post_rest_api_street` | street | нет | нет |
| PUT | `street/{ID}` | `put_rest_api_street_ID` | street | нет | нет |
| PUT | `user/{ID}` | `put_rest_api_user_ID` | user | нет | нет |
| POST | `userPosition` | `post_rest_api_userPosition` | userPosition | нет | нет |
| PUT | `userPosition/{ID}` | `put_rest_api_userPosition_ID` | userPosition | нет | нет |
| POST | `/token_auth.php` | `post_token_auth_php` | inline | нет | нет |

## Наблюдения, расхождения и недоказанное

### Подтверждено

| Ресурс | HTTP response (один элемент) | DB metadata | Вывод |
| --- | --- | --- | --- |
| Unit | `id` integer, `title`/`status` string | `id` int non-null/AI; `title` varchar(16) non-null; `status` enum non-null | форма и типы совпадают со снимком; обязательность body не доказана |
| city | `id`/`type_id` integer, `title` string | все три non-null; `type_id` default 1 | форма и типы совпадают; DB non-null не доказывает API required |
| cityType | `id` integer, `title` string | `title` varchar(32), nullable | тип и nullable не противоречат снимку; один non-null value не доказывает обязательность |
| GoodGroup | `id`, `is_service`, `is_show_in_vaccines` integer; `title` string; `markup`, `price_id` null | см. ниже | форма полей совпадает; правила body не доказаны |

### Расхождения, требующие сначала upstream-проверки

1. Для `goodGroup.price_id` OpenAPI объявляет `string`, nullable; DB metadata
   `int`, nullable. Наблюдаемый API-элемент содержит `null`, поэтому это
   **расхождение декларации OpenAPI и хранения**, но ещё не доказанное
   расхождение wire-типа. Нужен обезличенный GET-элемент с non-null `price_id`
   либо серверный контракт до смены схемы.
2. `goodGroup.markup`: OpenAPI `string`, nullable; DB `decimal(25,10)`, nullable.
   Это потенциально совместимо (decimal часто сериализуют строкой), поэтому
   расхождением API не объявлено без non-null наблюдения.
3. `goodGroup` DB содержит `price_id`, тогда как порядок свойств OpenAPI иной;
   порядок JSON-полей не является контрактом и не требует изменения.

Остаётся без доказательств: все 39 правила сериализации body, server-side
required/nullable, допустимые enum и диапазоны, unknown fields, форматы dates,
фильтры/пагинация по ресурсу, все 32 непросмотренные схемы и точная связь
хранилища с API-трансформерами. Нельзя выводить валидатор из одного GET или
из `information_schema`.

## Минимальный совместимый следующий этап O5/O6

1. Получить от владельца/команды API явное разрешение и одноразовый технический
   test record без ПД для **одного** справочного ресурса `Unit` (не `client`,
   `user`, `pet`, admission или invoices), а также политику cleanup. До него
   выполнять только GET и метаданные.
2. Выполнить только согласованные POST/PUT-пробы на этом record: минимальное
   допустимое body, отсутствие каждого кандидата required, `null` для каждого
   nullable, неверный scalar type и неизвестное поле. Сохранять только статус,
   allowlisted headers, верхние ключи ответа и агрегированную форму; не тела
   и не значения. Вернуть/удалить test record только по отдельно согласованной
   процедуре.
3. Если серверный контракт отличается, сначала исправить `vetmanager-openapi`,
   зафиксировать upstream commit и SHA-256 YAML/JSON, затем обновить snapshot.
4. Лишь затем добавить отдельный opt-in `SchemaValidator` для **Unit**. Он
   принимает схему/array и не трогает `Builder`, `uri()`, `asKeyValue()` или
   существующую сериализацию. Новый transport-method не нужен на этом этапе.

Критерии приёмки следующего этапа: (a) для Unit доказаны и занесены источник,
дата, operationId, знаменатель и результаты всех пяти сценариев; (b) unit-тесты
проверяют допустимое/недопустимое отдельно от HTTP; (c) две последовательные
devtr6-пробы проходят без утечки body/ПД; (d) старый `Builder` выдаёт побайтно
прежние query-строки; (e) PHP 7.3 и 8.5 проходят статический анализ и unit;
(f) `schema_support` в матрице меняется только для подтверждённой операции,
остальные остаются `none`; (g) независимое ревью PRD до кода и diff после него.

## Итог

O5 переведён в `in_progress`: инвентаризация выполнена и четыре безопасные
response-формы проверены, но доказательств для DTO, request-body validator или
включения `schema_support` нет. Исполняемый API, сохранённые объекты Redash и
клиентские данные не изменялись.

## Дополнение после одноразовой POST-проверки Unit

После исходного исследования разрешённый минимальный JSON POST `Unit` получил
HTTP 201. Верхний ответ содержал `success`, `message`, `data`, а вложенный
`data` — `totalCount`, `unit`. Проверочный скрипт ожидал другую вложенность и
не извлёк ID, поэтому дальнейшие PUT и отрицательные сценарии не запускались.
Позднее временная запись ID 48 с уникальным маркером `o5u082726bb6a` была
идентифицирована и удалена. GET после DELETE вернул пустой `data.unit`, а
Redash DS 6 показал ID=48: 0 и маркер: 0. Источник и время — рабочий лог
`LiveHelperAgent/logs/vm/2026-10-26-vetmanager-rest-api-o5.md`, 11:27–11:34 MSK.

Это подтверждает ровно один успешный минимальный POST и вложенность ответа.
Не доказаны PUT, обязательность полей, nullable, неверные типы, enum,
неизвестные поля, повторяемость POST и полная схема `unit`. Поэтому
`schema_support` остаётся `none`; границы узкого opt-in метода описаны в
`PRD/o5-unit-post-method.md`. Предыдущие разделы сохраняют состояние на время
исходного read-only исследования.

## Дополнение после контролируемой POST/PUT-проверки Unit

2026-10-26 08:57–08:59 UTC, devtr6, технический маркер `o5u47a0b7a8256d`.
До POST Redash DS 6 и GET списка показали ноль совпадений. Второй минимальный
JSON POST вернул 201; точный ID 50 немедленно сохранён на хосте и подтверждён
GET по ID. PUT того же `title` вернул 201; `title:null` — 406; PUT с
16-символьным маркером `o5u47a0b7a8256dx` — 201, GET подтвердил его сохранение;
неизвестное поле — 406. Прежнее утверждение о 17 символах возникло из неверного
подсчёта длины маркера. Wire-поведение для 17 символов не проверялось. Безопасные
верхние ключи успешных ответов: `success`, `message`, `data`; при 406:
`success`, `error`, `message`. Содержимое сообщений не сохранялось.

После PUT с новым 16-символьным маркером автоматическая очистка остановилась из-за изменения
title. Redash подтвердил ID 50 с новым техническим title; повторный GET сверил
обе величины, затем DELETE вернул 200/success=true. GET по ID и поиск маркера
через API показали отсутствие. Redash в 08:59:15 UTC дал 0 по ID и маркеру.
Подробная хронология и контракт метода — `PRD/o5-unit-put-method.md`.

Два подтверждённых DELETE технических Unit (ID 48 и 50) легли в основу
опционального `Methods\Unit::delete()`; контракт и границы —
`PRD/o5-unit-delete-method.md`. Нового live DELETE при расширении библиотеки
не было; подтверждение очистки уже зафиксировано выше и в журнале O5.

Итоговый знаменатель **полностью подтверждённых схем остаётся 0/36 и body
операций 0/39**: проверены отдельные сценарии только для Unit, не вся схема.
`schema_support=none`. Исходник ExtJS `rest/protected/models/Unit.php` задаёт для
`title` правила `required` и `max=16`; OpenAPI декларирует `x-db-type:
varchar(16)`, но не содержит JSON-Schema `maxLength`. Наблюдение сохранённого
16-символьного значения этим источникам не противоречит, однако не доказывает
пограничное поведение сервера. Валидатор длины и изменение upstream OpenAPI не
обоснованы. Поля `status`, неверный scalar type, поведение пустого
тела и сохранность неуказанных полей после PUT остаются неподтверждёнными.
