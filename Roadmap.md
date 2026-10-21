# Очередь разработки

Этот файл — единая очередь работ. Подробная приёмка и зависимости — в `Tasks.md`; постановка текущего этапа — в `PRD/agent-development-foundation.md`. Новую работу добавляйте сюда до начала реализации.

| № | Этап | Статус | Задачи |
| --- | --- | --- | --- |
| 1 | Основа агентской разработки и воспроизводимых проверок | done | `Tasks.md` → F1–F4; 2026-10-20 |
| 2 | Локальный запуск и проверки на devtr6 | done | `Tasks.md` → N1; `PRD/local-development-checks.md`; 2026-10-20 |
| 3 | Политика совместимости PHP и разрешения зависимостей | done | `Tasks.md` → C1; `PRD/php-compatibility-policy.md`; 2026-10-20 |
| 4 | Dev-toolchain и стратегия зависимостей | done | `Tasks.md` → C2; `PRD/php-compatibility-completion.md`; 2026-10-21 |
| 5 | CI PHP 7.3–8.5 и minimum/latest/production | done | `Tasks.md` → C3; 12/12 jobs SUCCESS в CI PR #27; 2026-10-21 |
| 6 | Контрактные edge-case тесты | done | `Tasks.md` → C4; 56 unit-тестов и CI 12/12; 2026-10-21 |
| 7 | Интеграция devtr6 на 7.3 и 8.5 | done | `Tasks.md` → C5; 2026-10-21 |
| 8 | Условный план major | todo | `Tasks.md` → C6; только при доказанной необходимости и решении владельца |

Статусы: `todo`, `in_progress`, `done`, `blocked`. При завершении этапа укажите дату, а в `Tasks.md` — результаты приёмки. Commit определяется по истории Git.
