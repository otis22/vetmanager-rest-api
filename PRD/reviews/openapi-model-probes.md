# Проверка имён моделей на devtr6 — 2026-10-21

Выполнены только GET к `/rest/api/{name}` с тестовым API key devtr6 на хосте, который возвращает `vetmanager-url` (`devtr6.vetmanager2.ru`). Зафиксированы статус, Content-Type и поле `success`; тело ответа и клиентские данные не сохранялись. Первая проба ошибочно обращалась к `devtr6.vetmanager.ru` и была полностью отброшена; таблица ниже составлена только по повторной пробе на правильном хосте. Проба не доказывает операции POST/PUT/DELETE и не проверяет вложенные пути.

| Группа | Имя | Результат | Решение для `Model` |
| --- | --- | --- | --- |
| Отсутствует | `anonymousClient` | 200 JSON, success=true | Добавить |
| Отсутствует | `fiscalTaxSystems` | 200 JSON, success=true | Добавить |
| Отсутствует | `goodTag` | 200 JSON, success=true | Добавить |
| Отсутствует | `messages` | 500 JSON | Не добавлять: базовый GET не описан |
| Отсутствует | `report-ai-job` | 404 JSON | Не добавлять: GET не подтвердил путь |
| Отсутствует | `VmLink` | TimeoutError (10 с) | Не добавлять: базовый GET не описан |
| Вариант регистра | `ComboManualItem` | 200 JSON, success=true | Добавить вместе со старым `comboManualItem` |
| Вариант регистра | `ComboManualName` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `GoodGroup` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `HospitalBlock` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `MedicalCards` | 200 JSON, success=true | Добавить вместе со старым `medicalCards` |
| Вариант регистра | `PartyAccount` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `PartyAccountDoc` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `StoreDocument` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `StoreDocumentOperation` | 200 JSON, success=true | Добавить со старым именем |
| Вариант регистра | `Suppliers` | 200 JSON, success=true | Добавить вместе со старым `suppliers` |
| Вариант регистра | `Unit` | 200 JSON, success=true | Добавить со старым именем |
| Только библиотека | `clientPhone` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `clinicsToClients` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `clinicsToDocuments` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `clinicsToUsers` | 404 JSON | Сохранить прежнюю поддержку |
| Только библиотека | `diagnoses` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `doctorsResponsible` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `failedHook` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `fiscalRegister` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `lastTime` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `servicePrice` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `timesheetTypes` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `userCalls` | 200 JSON, success=true | Сохранить прежнюю поддержку |
| Только библиотека | `userConfig` | 200 JSON, success=true | Сохранить прежнюю поддержку |

Полнота OpenAPI в отношении последних 13 имён не подтверждена. Их удаление или исправление регистра нарушило бы совместимость.
