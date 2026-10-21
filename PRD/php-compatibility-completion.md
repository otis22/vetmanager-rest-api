# Завершение roadmap технического долга PHP

## Цель и границы

Продолжить PR #27 после C1: сохранить PHP 7.3 и публичный контракт `src/`, привести dev-зависимости и CI к проверяемому диапазону 7.3–8.5, закрепить крайние случаи unit-тестами и проверить интеграцию на devtr6 с обеих границ. Production не затрагивается. Major-этап начинается только при доказанном блокере совместимости.

## План и приёмка

1. Удалить неиспользуемые прямые dev-зависимости `phpunit/phpcov`, `php-coveralls`, `symfony/deprecation-contracts`. Последний остаётся транзитивным для Guzzle. `guzzlehttp/guzzle` и `otis22/vetmanager-url` нужны тестам. Убедиться через `composer why`, что удаление не влияет на runtime.
2. Оставить `composer.lock` игнорируемым: единый dev-lock на 7.3 и 8.5 не переносим, поскольку инструменты разрешаются разными версиями. Для каждого CI job разрешать зависимости в чистой среде без lock; явно фиксировать Composer 2.10.3. Отдельно проверять минимальные версии прямых и транзитивных зависимостей через `composer update --prefer-lowest` на 7.3 и 8.5 и последние версии через обычные matrix jobs. Production `--no-dev` проверять в чистых копиях на обеих границах. Не использовать `--ignore-platform-req`.
3. CI: матрица из восьми версий PHP 7.3, 7.4, 8.0–8.5 с validate, install, PHPCS, PHPStan, unit+coverage. Добавить отдельные minimum jobs (`composer update --prefer-lowest --prefer-stable` + те же проверки) и production jobs (`composer install --no-dev` + `check-platform-reqs --no-dev` + smoke автозагрузки) на 7.3 и 8.5. Все jobs — обязательные для приёмки итогового SHA. Обновить checkout. Сохранять PHP/Composer, разрешённые пакеты и результаты шагов в summary/artifact для каждого job; сбой совместимости должен делать job красным.
4. Добавить контрактные unit-тесты для неправильной арности `Builder::where`, комбинации фильтров/сортировки, перехода страниц, граничных значений URI/model и полного вывода helper-функций/заголовков. Исполняемый код и поведение пользователей не менять; новые спорные ожидания не навязывать.
5. Локально выполнить `make all`, `composer update --prefer-lowest --prefer-stable` с проверками и отдельный `--no-dev` на 7.3/8.5, `make integration` только на devtr6 с тестовыми учётными данными на обеих версиях. Указать passed/skipped/failures без секретов. Проверить зелёные matrix/minimum/production jobs для итогового SHA той же PR и обновить приёмку в `Roadmap.md`/`Tasks.md`.

## Риски и решения

- Composer 2.10.3 выбран как одна фактически проверенная версия на 7.3 и 8.5; pin требует отдельного обновления при обнаружении уязвимости или несовместимости.
- Минимальные dev-зависимости могут не работать на PHP 8.5. Если так, выяснить ограничивающий пакет и исправить constraints или инструменты до зелёной minimum-проверки; не скрывать падение job.
- Невалидный `composer.lock` из старой рабочей копии не должен участвовать в новых проверках.
- Service API key тест пропускается при отсутствии отдельной пары тестовых credentials; пропуск надо явно отразить.

## Гейты

До реализации — независимое ревью этого PRD. После реализации — независимое ревью diff и разбор замечаний. Затем локальные проверки, commit/push в текущую ветку PR и CI итогового SHA. Все команды и exit codes записать в этот документ.

## Ревью PRD до реализации

21.10.2026 независимый Claude Sonnet получил полный PRD, `composer.json` и CI workflow. Найден один блокер: minimum и production проверки не были явно закреплены как CI jobs и критерий итогового SHA. Замечание принято; пункт 3 и приёмка выше уточнены до реализации.

## Реализация и локальная приёмка 21.10.2026

- Прямые dev-зависимости `phpunit/phpcov`, `php-coveralls`, `symfony/deprecation-contracts` удалены. `composer why` подтвердил, что первые два были нужны только корневому пакету, а deprecation contracts продолжают требоваться Guzzle и его транзитивным пакетам. Coveralls в CI использует отдельную глобальную установку только при push в `main`.
- Minimum-разрешение выявило, что `elegant-bro/arrayee` 0.1.0 не содержит используемый `Aggregation\Reduced`; версия 0.1.1 содержит класс. Нижняя граница поднята до `^0.1.1`. PHPStan 1.9.0 даёт сбой на PHP 8.5; нижняя граница dev-инструмента поднята до `^1.12.34`. `composer prohibits phpstan/phpstan 2.0.0` на PHP 7.3 завершился exit 1 и показал требование PHP `^7.4|^8.0`; переход на 2.x не является решением для текущей линии. Аннотации `PagedQuery` приведены к фактическим `int|string`, без изменения сигнатуры или сериализации.
- CI расширен до восьми latest jobs и четырёх дополнительных minimum/production jobs на PHP 7.3/8.5. В Docker и CI закреплён Composer 2.10.3; workflow сохраняет toolchain, resolved packages, outcomes и exit codes. `composer.lock` остаётся игнорируемым: каждое CI задание разрешает зависимости заново на своей платформе.
- Добавлено девять unit-тестов для публичных helper-функций, модели/URI, заголовков, арности `Builder::where`, порядка фильтров/сортировок и перехода страницы. Новые тесты обнаружили, что `Builder::where` ожидает строковое значение при `strict_types`; тестовые данные приведены к существующему контракту, исполняемый код не менялся.

Проверки выполнялись в отдельных чистых `/tmp`-копиях без локального lock, vendor и build. Для каждой версии использован Docker-образ `vetmanager-rest-api:<version>`; PHP 7.3.33 / 8.5.11, Composer 2.10.3.

| Команда / проверка | PHP 7.3 | PHP 8.5 |
| --- | --- | --- |
| `make all PHP_VERSION=<version>` | exit 0; 56 tests, 64 assertions, coverage 94,63% | exit 0; 56 tests, 64 assertions, coverage 94,63% |
| `docker run --rm -e XDEBUG_MODE=coverage -v "$PWD":/app -w /app vetmanager-rest-api:<version> sh -c 'composer validate --strict && composer update --prefer-lowest --prefer-stable --no-interaction --prefer-dist && composer check-style && composer check-static-analyze && composer unit'` | exit 0; 56 tests, 64 assertions, coverage 94,63% | exit 0; 56 tests, 64 assertions, coverage 94,63% |
| В чистой копии `composer validate --strict && composer install --no-dev --no-interaction --prefer-dist && composer check-platform-reqs --no-dev`, затем smoke `uri('invoice')->asString()` | exit 0; `/rest/api/invoice` | exit 0; `/rest/api/invoice` |
| `make integration PHP_VERSION=<version>` с локальным `.env`, проверенным как `TEST_DOMAIN_NAME=devtr6` | exit 0; 56 tests, 55 assertions, 1 skipped | exit 0; 56 tests, 55 assertions, 1 skipped |
| `git diff --check` | exit 0 | общая проверка |

Один пропуск на каждой границе — service API key test: отдельная пара тестовых credentials не настроена. Production-учётные данные не использовались. Локальные проверки не заменяют GitHub CI. При ранних пробных прогонах minimum обнаружились указанные выше несовместимости; также `composer unit` без `XDEBUG_MODE=coverage` не создал Clover-файл. Последующие полные прогоны выполняются с покрытием.

## CI и независимое ревью diff

21.10.2026 Claude Sonnet получил полный staged diff всех десяти изменённых файлов, ограничения контракта и фактические локальные результаты. Независимое ревью вернуло `[]`: блокирующих замечаний нет. Ревьюер не повторял Docker-прогоны. CI итогового SHA ожидается перед закрытием C2–C4.
