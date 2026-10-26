# O4+O8b: подтверждённые read-only методы — 2026-10-26

## Цель и основание

Добавить opt-in обёртки только для трёх GET, дважды подтверждённых на `devtr6` в `PRD/pr28-readonly-followup.md`: `/rest/api/MedicalCards/AllDiagnoses`, `/rest/api/MedicalCards/MedcardsTextTemplates`, `/rest/api/user/anonymousList`. `Tasks.md` O4 и O8b описывают очередь. PR #28 и его `Methods\Clinics` уже в `main`; новый слой следует этому контракту.

## Публичный API — решение до реализации

Имена намеренно совпадают с сегментами серверного пути и не обещают модель ответа:

| GET | Публичный класс и метод | Поддержка в матрице |
| --- | --- | --- |
| `MedicalCards/AllDiagnoses` | `Otis22\VetmanagerRestApi\Methods\MedicalCardsAllDiagnoses::get()` | `medical_cards_all_diagnoses_get` |
| `MedicalCards/MedcardsTextTemplates` | `Otis22\VetmanagerRestApi\Methods\MedicalCardsMedcardsTextTemplates::get()` | `medical_cards_medcards_text_templates_get` |
| `user/anonymousList` | `Otis22\VetmanagerRestApi\Methods\UserAnonymousList::get()` | `user_anonymous_list_get` |

Каждый класс `final`; конструктор `__construct(ClientInterface $client)` и метод `get(Headers $headers, array $query = ['limit' => 1]): ResponseInterface`. Уже существующая функция `Otis22\VetmanagerRestApi\namedPath(string $template, array $parameters = []): URI` из `src/functions.php` получает строки **без** ведущего `/` и префикса: `MedicalCards/AllDiagnoses`, `MedicalCards/MedcardsTextTemplates`, `user/anonymousList`. Итоговые URI строго `/rest/api/MedicalCards/AllDiagnoses`, `/rest/api/MedicalCards/MedcardsTextTemplates`, `/rest/api/user/anonymousList`. Это подтверждается реализацией `src/URI/NamedPath.php` через `RestApiPrefix` и существующим `tests/unit/URI/NamedPathTest.php`; новые тесты дополнительно проверят каждую из трёх итоговых строк. Односегментный `uri()` здесь не строит эти пути. Вызывается `ClientInterface::request('GET', namedPath(...)->asString(), ['headers' => $headers->asKeyValue(), 'query' => $query])`. Возвращается исходный response. Пользовательский query передаётся без новой валидации или добавления `limit`; body не задаётся. Конфигурация Guzzle и его исключения сохраняются.

`PRD/pr28-readonly-followup.md` зафиксировал два успешных прогона всех пяти подтверждённых GET с `limit=1`, включая эти три пути (6 тестов / 24 assertion в каждом прогоне). Это доказательство безопасности отправки такого query на тестовом tenant, но не обещание серверной пагинации или усечения результата: сервер может игнорировать `limit` для отдельных маршрутов. Метод не интерпретирует ответ.

## Границы

Не меняются `uri()`, `Model`, `Builder`, прежние публичные сигнатуры, заголовки и сериализация. Все три GET-записи уже присутствуют в `operation-support.json` с `uri_support=named_path`, `http_method_support=none`, `schema_support=none`. Для них меняются только `http_method_support` в матрице и `SUPPORTED_METHODS` в контрактном скрипте. Методный слой — O4; синхронная матрица — O8b. `schema_support` и error support остаются `none`. Обновляются README с примером нового API и статусы O4/O8b в `Tasks.md` без объявления полного завершения этапов. O5–O7, DTO, валидация и другие методы не входят.

## Приёмка и доказательства

До кода — независимое ревью этого PRD с разбором замечаний. После кода — `tests/unit/Methods/ConfirmedReadOnlyTest.php`: для каждого класса точные `GET` и строки первого аргумента пути `/rest/api/MedicalCards/AllDiagnoses`, `/rest/api/MedicalCards/MedcardsTextTemplates`, `/rest/api/user/anonymousList`, массив опций ровно из `headers` и `query`, default `limit=1`, произвольный query без добавленного `limit`, отсутствие body и исходный response; отдельный случай проверяет передачу того же исключения. Guzzle `MockHandler` проверяет реальное поведение `http_errors=true/false` для 4xx. Затем независимое ревью diff. Изменение матрицы проверяется сравнением с `origin/main`: ровно три `http_method_support`, все прочие поля и операции неизменны; контрактный скрипт сверяет её с `SUPPORTED_METHODS`. `python3 -I scripts/check_openapi_contract.py` и `make all PHP_VERSION=8.3` должны завершиться успешно. `src/` и тесты используют синтаксис PHP 7.3; CI job 7.3 подтверждает совместимость, прогон 8.3 его не заменяет. Локальный `make integration` выполняется только на `devtr6`; счётчики passed/failed/skipped и таймауты записываются. После PR проверить все обязательные jobs из `.github/workflows/CI.yml` для итогового SHA: контрактный и матрица PHP 7.3–8.5 latest, minimum и production на 7.3/8.5 (сейчас 13 jobs); до их завершения приёмка CI не заявляется. Никакого merge или выпуска в этом этапе.

## Ревью и результаты

Первое независимое ревью Claude Sonnet выполнено до реализации по полному PRD и исходнику `Clinics`. Замечания: уточнить точные аргументы `namedPath()` и ожидаемые URI; сослаться на два прогона с `limit=1`; усилить unit и матричную приёмку; разделить O4/O8b и обновить README/Tasks; назвать PHP 7.3 и состав CI. Все приняты в текст выше. Рецензент не читал другие файлы репозитория, поэтому его сомнение в наличии операций в матрице проверено отдельно: все три записи есть, со значением `none`. Второе ревью уточнённого PRD указало на двусмысленное слово «относительный URI» и отсутствие у рецензента реализации `NamedPath`; приёмка теперь фиксирует три точные строки, а реализация и тест `NamedPath` прочитаны до кода. Третье независимое ревью по полному PRD, `NamedPath` и его тесту вернуло `[]` до реализации. После реализации независимое ревью Sonnet получило полный PRD, tracked diff и исходники всех новых файлов; результат `[]`, серьёзных ошибок нет. Рецензент отдельно указал, что не выполнял команды и видел `expected_matrix` только в diff: результаты проверок ниже — отдельные фактические прогоны исполнителя.

| Команда | Exit code и результат |
| --- | --- |
| `python3 -I scripts/check_openapi_contract.py --refresh` и затем `python3 -I scripts/check_openapi_contract.py` | 0 / 0; 104 пути, 137 операций, 19 шаблонов. Сравнение `operation-support.json` с `origin/main` показало ровно три изменения `http_method_support` из `none`, прочие поля и записи без изменений. |
| `make all PHP_VERSION=8.3` (первый прогон) | 2; PHPCS обнаружил форматирование нового теста. Исправлено без изменения логики. |
| `make all PHP_VERSION=8.3` (повтор) | 0; PHPCS и PHPStan без ошибок, 93 unit-теста / 124 assertion, покрытие 95,45%. Локальный игнорируемый `composer.lock` устарел относительно `composer.json`; `composer install` выдал предупреждение, проверка чистого разрешения зависит от CI. |
| `timeout 960 make integration PHP_VERSION=8.3` | 0; test tenant `devtr6` подтверждён настройкой `.env` и защитой `ClientFactory`; 65 тестов / 91 assertion / 1 skipped (service API key), 0 failed, 33,327 с. |
| `git diff --check` | 0 до фиксации результата. |

CI для итогового SHA и ссылка на PR фиксируются после публикации PR в рабочем логе и самом PR. Пока матричная совместимость PHP 7.3–8.5 не подтверждена.
