# Задачи

Источник очереди: `Roadmap.md`. Статусы: `todo`, `in_progress`, `done`, `blocked`. Для нетривиальных следующих этапов заведите отдельный PRD до изменения кода.

## Этап 1 — основа агентской разработки

| ID | Статус | Зависит от | Задача и приёмка |
| --- | --- | --- | --- |
| F1 | done | — | Проверены checkout, применимые инструкции и реальные команды в Makefile/CI; расхождения зафиксированы в PRD. |
| F2 | done | F1 | Созданы `AGENTS.md`, `Roadmap.md`, `Tasks.md` и PRD. Прежнее ревью PRD не подтверждено артефактом; новое независимое ревью и разбор замечаний зафиксированы в PRD. |
| F3 | done | F1 | Воспроизведён и исправлен блокер Docker `VERSION`; README соответствует подтверждённым командам и версии по умолчанию. |
| F4 | done | F2, F3 | Проверки и ограничения записаны в PRD; локальный commit с trailer был создан без push/PR. Прежнее ревью не имеет проверяемого артефакта; новое независимое ревью и его замечания записаны в PRD перед отдельной доставкой PR. |

## Следующая работа

| ID | Статус | Зависит от | Задача и приёмка |
| --- | --- | --- | --- |
| N1 | done | F4 | Docker PHP 7.3 и штатные проверки прошли в чистом checkout; интеграция devtr6: 55 проверок и 1 пропуск service API key. Команды, exit codes и CI-гейт — в `PRD/local-development-checks.md`. |

## Этап 3 — политика совместимости PHP

| ID | Статус | Зависит от | Задача и приёмка |
| --- | --- | --- | --- |
| C1 | done | N1 | В README разделены объявленное ограничение PHP, текущая CI-матрица и цель 8.5; объяснены игнорируемый lock и чистое разрешение по платформе. Независимые ревью PRD и diff, команды, exit codes, описи зависимостей и ограничения записаны в `PRD/php-compatibility-policy.md`. Публичный API и исполняемый код не менялись. |

## Завершение roadmap PHP 7.3–8.5

Постановка, ревью и точные локальные команды — в `PRD/php-compatibility-completion.md`.

| ID | Статус | Зависит от | Задача и приёмка |
| --- | --- | --- | --- |
| C2 | done | C1 | Удалены неиспользуемые прямые dev-пакеты, minimum-разрешение обнаружило и исправило нижнюю границу `arrayee`; PHPStan оставлен в совместимой с 7.3 ветке. Чистые latest/minimum и production `--no-dev` проверки на 7.3/8.5 локально прошли; CI 12/12 SUCCESS. |
| C3 | done | C2 | CI содержит 8 latest jobs и по 2 minimum/production jobs на краях, Composer 2.10.3, опись пакетов и exit codes. [CI run 39555694078](https://github.com/otis22/vetmanager-rest-api/actions/runs/39555694078) для `cd9d69a0a640c6cd5dee9eda7bae7086e590586a`: все 12 jobs SUCCESS. |
| C4 | done | C2 | Добавлены 9 контрактных edge-case тестов без изменения публичного поведения. `make all PHP_VERSION=7.3` и `8.5`: exit 0, 56 tests / 64 assertions, покрытие 94,63%; CI 12/12 SUCCESS. |
| C5 | done | C4 | `make integration` только на devtr6: PHP 7.3 и 8.5 — exit 0, по 56 tests / 55 assertions / 1 skipped (service API key); 2026-10-21. |
| C6 | todo | C2, C3 | Major-этап условен; production `--no-dev` на PHP 7.3 проходит, доказанного повода менять минимум или публичный API нет. |

## Этап 10 — OpenAPI и контрактная очередь

Постановка O1: `PRD/openapi-integration.md`; совместимое расширение и результаты: `PRD/openapi-compatible-expansion.md`, `PRD/openapi-contract-research.md`.

| ID | Статус | Зависит от | Задача и проверяемая приёмка |
| --- | --- | --- | --- |
| O1 | done | — | Вендорены неизменённые YAML/JSON OpenAPI 1.3.1 и уведомление MIT из фиксированного коммита; README содержит ссылку и порядок обновления. SHA-256 обоих файлов совпадают с источником, upstream-валидатор прошёл; ревью PRD и diff, команды и exit codes зафиксированы в PRD; 2026-10-21. |
| O2 | in_progress | O1 | 14 подтверждённых имён добавлены без удаления старых. `ComboManualName` и `MedicalCards` дважды прошли read-only GET с `limit=1` на devtr6. `VmLink`/`messages`/`report-ai-job` не добавлены (500/500/404); остальные операции остаются исследованием. Результаты — `PRD/pr28-readonly-followup.md`. |
| O3 | in_progress | O2 | `namedPath()` строит 19 именованных путей без изменения `uri()`. Три подтверждённых read-only GET (`AllDiagnoses`, `MedcardsTextTemplates`, `anonymousList`) дважды прошли на devtr6. Прочие шаблоны, включая POST и пути с ID без безопасных записей, остаются исследованием. |
| O4 | in_progress | O2, O3 | Добавлен отдельный `Methods\Clinics::get()` для GET `clinics?limit=1`; method/query/headers/body и ошибки проверяются unit и devtr6. Прочие операции, особенно POST/PUT/DELETE, ждут payload, прав, статуса и очистки test data. |
| O5 | in_progress | O1 | Матрица 36 схем и 39 `requestBody` — `PRD/o5-schema-evidence-2026-10-26.md`. Минимальные JSON POST/PUT `Unit` дали 201; null/unknown — 406, 17-символьный title — 201. Технические записи удалены с проверкой API и Redash. Узкие opt-in методы — `PRD/o5-unit-post-method.md` и `PRD/o5-unit-put-method.md`; полная схема не доказана, `schema_support=none`. |
| O6 | todo | O4, O5 | Спроектировать opt-in валидацию имён полей, типов, фильтров, пагинации и request body только по проверенным схемам. Приёмка: допустимые/отклоняемые значения, unit и devtr6, неизменная сериализация старого `Builder`. |
| O7 | in_progress | O1 | Read-only probe проверяет API-key 200, 401 (нет/неверный key) и 404 без сохранения тела. Неверный `limit` ранее дал 200. Остальные ошибки, token/service и upstream-схемы остаются исследованием. |
| O8a | done | O2–O3 | CI проверяет хэши снимка, `operationId`, `$ref`, набор 19 именованных путей и матрицу URI/HTTP/схем. На SHA `66fe289cea0d8202bcb26700d4e44d2a34ecf966` все 13 jobs успешны: [run 39593773534](https://github.com/otis22/vetmanager-rest-api/actions/runs/39593773534). |
| O8b | in_progress | O4–O7 | Матрица и CI-гейт отмечают `clinics_get` и `unit_post`; `schema_support` остаётся `none`, схема ошибок не заявлена. Расширять карту только после подтверждения конкретной операции. |

## Исследование для major

| ID | Статус | Задача |
| --- | --- | --- |
| M1 | todo | Оценить несовместимые варианты из `PRD/openapi-contract-research.md`: удаление старых имён, смену регистра/сериализации/сигнатур, обязательную строгую валидацию и отказ от прежней авторизации. Не внедрять в минорный выпуск. |

## PR #25 — dev-зависимость URL

| ID | Статус | Зависит от | Задача и приёмка |
| --- | --- | --- | --- |
| R1 | done | C5 | Alias заменён на `^0.3 || ^1.1`; локальные `make all` на PHP 7.3/8.3/8.5 и интеграция devtr6 прошли, независимое ревью diff без блокирующих замечаний, CI `f69447b` 12/12 SUCCESS. Команды и ограничения — в `PRD/pr25-url-dependency.md`; CI финального документального commit — перед merge. |
