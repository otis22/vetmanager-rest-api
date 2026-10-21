# PR #28: совместимое завершение read-only этапов

## Цель и границы

На основе `PRD/reviews/pr28-devtr6-analysis.md` завершить подтверждаемые O2/O3 и добавить отдельный узкий слой O4 для `GET /rest/api/clinics?limit=1`. Прежние `Model`, `uri()`, `openApiPath()`, `Builder`, заголовки и исключения сохраняются. Только тестовый tenant `devtr6`; запись на сервер запрещена.

## Контракт реализации

1. Пять подтверждённых GET (`ComboManualName`, `MedicalCards`, `MedicalCards/AllDiagnoses`, `MedicalCards/MedcardsTextTemplates`, `user/anonymousList`) входят в безопасный интеграционный набор: `limit=1`, connect timeout 5 с, total timeout 15 с; проверяются HTTP 200, JSON и `success=true` без сохранения тела. Два последовательных успешных запуска закрывают серверную приёмку этих read-only вариантов O2/O3. Остальные именованные шаблоны остаются URI-only до безопасных test records.
2. Новый опциональный `Methods\Clinics` принимает Guzzle `ClientInterface` с настроенным вызывающим кодом `base_uri`; метод `get(Headers $headers, array $query = ['limit' => 1]): ResponseInterface` выполняет GET по относительному `uri('clinics')`, передаёт единственную опцию `query` и `headers`, не передаёт body и возвращает исходный PSR response. Параметры query не валидируются. Для первого проверенного сценария — `limit=1`; выбор иного query остаётся ответственностью вызывающего кода. Ошибки Guzzle при `http_errors=true` проходят без перехвата; `http_errors=false` даёт исходный response. Guzzle становится прямой production-зависимостью с `^7.1`: уже существующие production-зависимости `vetmanager-token` 0.2.3 и `vetmanager-url` 0.3.2 требуют `^7.1`, поэтому Guzzle 6 не разрешается в текущем графе и новый диапазон его дополнительно не исключает.
3. O7: read-only probe для API-key 200, 401 без/с неверным ключом и 404 неизвестного пути; выводит только status, Content-Type, форму JSON (`keys`, `success`) и длительность. Остальные статусы и token/service auth — исследовательская очередь. Никакого нового mapper/валидатора.
4. O8b: матрица отмечает method support только у проверенного GET clinics; schema/error support остаётся `none`. Скрипт контракта сверяет это с кодом.

## Проверки и совместимость

Unit-контракт Guzzle MockHandler проверяет метод, относительный URI, отсутствие дубля query, headers, отсутствие body и неизменённый response/исключения при 401/404 (`http_errors` true/false). Абсолютный host получается из `base_uri` вызывающего кода; отдельный тест фиксирует результат разрешения URI. Полный старый unit-набор и интеграция devtr6 обязательны. До любого нового и старого интеграционного запроса проверяется `TEST_DOMAIN_NAME=devtr6` в `ClientFactory`, иначе fail-fast. `make integration` запускается с `COMPOSER_PROCESS_TIMEOUT=900` и внешним пределом; адресный набор идёт отдельно дважды. Для него отчёт содержит passed/failed/skipped и длительность каждого запуска; таймаут — incomplete, а не passed. `make all PHP_VERSION=8.3`, чистые проверки PHP 7.3/8.5, `composer validate --strict`, `python3 -I scripts/check_openapi_contract.py`, сверка 51 прежнего имени и публичных сигнатур; итоговый CI 13 jobs на новом SHA. Новые POST/PUT/DELETE, DTO и строгая валидация не входят в этот минорный PR.

## Исследовательские этапы

O5/O6 требуют безопасных обезличенных данных и подтверждения схем/обязательности/типов; O7 требует подтверждения остальных статусов и способов авторизации, затем upstream OpenAPI; O8b расширяется вслед за подтверждёнными возможностями. Изменение старой сериализации, имён, сигнатур и обязательная валидация — только после исследования major и решения владельца.

## Ревью и результаты

Ревью Claude Sonnet до кода обнаружило три блокера: неоговорённую production-зависимость Guzzle, двусмысленный контракт `Clinics` и отсутствие жёсткой защиты devtr6/точной семантики сетевого результата. Все приняты и исправлены в контракте выше. Попытка принудительно разрешить Guzzle 6 на PHP 7.3 дала Composer exit 2 из-за существующих зависимостей `vetmanager-token` и `vetmanager-url` на `^7.1` (а также security advisories); поэтому первоначальное предложение `^6.5 || ^7.0` заменено на фактическую нижнюю границу `^7.1`. Ревью diff, точные команды, коды выхода и CI SHA добавляются после реализации.

## Результат и проверки 2026-10-21

Реализован `Methods\Clinics::get()` с исходным `ResponseInterface`; добавлены unit и read-only integration tests, безопасный probe для формата ошибок, жёсткая защита `devtr6` в интеграционной фабрике и единственная запись `clinics_get` в матрице. `Model`, `uri()`, `openApiPath()` и старый `Builder` не менялись. У `limit` нет новой клиентской проверки; unit намеренно пропускает строковое значение.

| Команда | Exit и факт |
| --- | --- |
| `python3 -I scripts/check_openapi_contract.py` | 0; 104 пути, 137 операций, 19 именованных шаблонов, одна поддержанная GET-операция |
| `make all PHP_VERSION=8.3` в рабочей копии | 0; 83 unit-теста / 101 assertion, покрытие 95,16%; стиль и PHPStan без ошибок. Локальный игнорируемый `composer.lock` устарел и не является проверкой чистого разрешения. |
| `make all PHP_VERSION=7.3` и `8.5` в отдельных копиях без lock/vendor | 0 / 0; в каждой 83 unit-теста / 101 assertion, покрытие 95,16%; Guzzle 7.16.0 |
| `composer update --prefer-lowest --prefer-stable --no-interaction --prefer-dist` в тех же двух чистых копиях | 0 / 0; минимальная разрешённая Guzzle 7.15.2 на обеих версиях из-за security advisory blocking; никакого обхода advisory не делали. |
| `composer check-style`, `composer check-static-analyze`, `composer unit` после minimum update на PHP 7.3/8.5 | 0 / 0 / 0 для каждой версии; 83 теста / 101 assertion, 95,16% покрытие. |
| `composer validate --strict` в чистых копиях PHP 7.3/8.5 | 0 / 0; `composer.json is valid`. |
| `timeout 120 docker run --env-file=.env --rm -v "$PWD":/app -w /app vetmanager-rest-api:8.3 vendor/bin/phpunit --testsuite integration --filter OpenApiReadOnlyTest` дважды подряд | 0 / 0; по 6 тестов / 24 assertion, 0 failed, 0 skipped, длительности 3,853 и 3,817 с. Пять read-only GET O2/O3 и GET clinics дали 200/JSON/`success=true`. |
| `timeout 120 docker run --env-file=.env --rm -v "$PWD":/app -w /app vetmanager-rest-api:8.3 php scripts/probe_readonly.php` | 0; 200/401/401/404, JSON, `success=true/false/false/false`; только статус, Content-Type, ключи верхнего уровня и длительности. |
| `timeout 960 make integration PHP_VERSION=8.3` | 0; 62 теста / 79 assertion / 1 skipped (service API key), 34,157 с; внутри контейнера `COMPOSER_PROCESS_TIMEOUT=900`. |
| `git diff --check` и `git diff --exit-code HEAD -- src/Model.php src/functions.php src/URI` | 0 / 0; старые модели, функции и URI-реализации не изменены. |

`composer validate --strict` в рабочей копии дал exit 2 из-за существующего игнорируемого lock, устаревшего после изменения `composer.json`; чистые копии прошли. Первый unit-прогон дал exit 2 из-за неверно собранного `MockHandler` в новом тесте (`http_errors` middleware отсутствовал); исправлено в тесте, последующие проверки прошли. Повторное независимое ревью полного актуального diff и PRD выполнено Claude Sonnet после разрешения minimum-dependencies: ответ `[]`, серьёзных замечаний нет. Более раннее замечание о диапазоне Guzzle закрыто реальным результатом Composer и матрицей тестов; самостоятельную проверку исполнителя за ревью не выдаём.

Кодовый коммит `bfbb40f5dcdf31ad642b45540fa031801c4da90e` и документальный `66fe289cea0d8202bcb26700d4e44d2a34ecf966` отправлены в PR #28. На втором SHA [CI run 39593773534](https://github.com/otis22/vetmanager-rest-api/actions/runs/39593773534) завершился `success`, все 13 jobs (OpenAPI contract и PHP latest 7.3–8.5, minimum/production на краях) имеют `conclusion=success`; `gh run view 39593773534 --repo otis22/vetmanager-rest-api --json headSha,conclusion,status,url,jobs --jq '{headSha,conclusion,status,url,total:(.jobs|length),success:([.jobs[]|select(.conclusion=="success")]|length),pending:([.jobs[]|select(.status!="completed")|.name])}'` — exit 0, `total=13`, `success=13`, `pending=[]`. У предыдущего run 39593669653 сводный статус `success`, но API деталей продолжает показывать job PHP 7.3 minimum как `in_progress`; поэтому приёмка опирается на полностью завершённый run второго SHA.
