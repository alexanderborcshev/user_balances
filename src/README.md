## User Balances — SPA (Vue 3) + Laravel

Краткая инструкция по запуску и проверке проекта (бекенд Laravel + SPA на Vue 3).

### Требования
- Docker + Docker Compose
- Node.js + npm (для фронтенда / Vite)
- PHP ≥ 8.2 (для локального запуска тестов)

### Быстрый старт (Docker)
1. Установить npm-зависимости (один раз):
   ```bash
   cd src
   npm install
   ```
2. Поднять контейнеры (Laravel + Postgres + Nginx + PHP-FPM):
   ```bash
   docker compose up -d
   ```
3. Выполнить миграции и сиды внутри php-контейнера:
   ```bash
   docker compose exec app php artisan migrate --seed
   ```
4. Запустить Vite dev server (HMR) для SPA:
   ```bash
   cd src
   npm run dev
   ```
5. Открыть приложение в браузере на домене/порту, настроенном в docker/nginx (по умолчанию http://localhost:8080 для Laravel через Nginx; Vite dev сервер — http://localhost:5173).

### Среда и переменные
- Основные переменные в `.env` (см. `.env.example`):
  - `FRONTEND_URLS=http://localhost:5173` — список origin для CORS/Sanctum (через запятую)
  - `SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173` — stateful-домены для cookie-based SPA auth
  - `SESSION_DOMAIN=localhost` (или ваш домен)
  - `SPA_POLL_INTERVAL_SEC=10` — интервал автообновления данных на SPA (попадает в `<meta name="spa-poll-interval">`)
- По умолчанию используется Postgres (см. `docker-compose.yml`), Laravel слушает через Nginx на `8080`.

### Тестовые пользователи (сиды)
- `alice@example.com / password`
- `bob@example.com / password`

### Проверка
1. API/бекенд: после `migrate --seed` можно выполнить фиче-тесты (нужен PHP ≥ 8.2):
   ```bash
   cd src
   ./vendor/bin/phpunit --testsuite=Feature
   ```
2. SPA: 
   - Перейти на `/login`, залогиниться тестовыми пользователями.
   - Проверить главную страницу (баланс, последние операции, автообновление с интервалом `SPA_POLL_INTERVAL_SEC`).
   - Проверить историю (`/history`): поиск по описанию, сортировка по дате, пагинация.

### Полезные команды
- Запуск Vite: `npm run dev`
- Сборка фронтенда: `npm run build`
- Предпросмотр сборки: `npm run preview`
- Типы: `npm run type-check`
