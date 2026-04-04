# Typed Fog

Laravel 13 (PHP 8.5) + Vue 3 (Node 24). Auth via Auth0. Redis for cache and queue.

## Stack

| Service          | Tech                 | Port |
|------------------|----------------------|------|
| backend          | Laravel 13 / PHP 8.5 | 8000 |
| frontend (dev)   | Vue 3 + Vite HMR     | 5173 |
| frontend (prod)  | Vue 3 + nginx        | 80   |
| redis            | Redis 7              | —    |

Redis is not exposed outside the Docker network.

## Running with Docker

### 1. Copy env files

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env.local
```

Edit `frontend/.env.local` — fill in Auth0 credentials:

| Variable              | Description                        |
|-----------------------|------------------------------------|
| `VITE_AUTH0_DOMAIN`   | e.g. `your-tenant.auth0.com`       |
| `VITE_AUTH0_CLIENT_ID`| Auth0 SPA application Client ID    |
| `VITE_BACKEND_API_URL`| Backend URL, default `http://localhost:8000` |

### 2. Generate APP_KEY

```bash
docker compose run --rm --no-deps backend-prod php artisan key:generate --show
```

Copy the output value into `backend/.env` as `APP_KEY`.

### 3. Start

```bash
# Dev — Vite HMR + bind mounts (live reload for both frontend and backend)
docker compose --profile dev up --build

# Prod — nginx + built static + composer install baked in
docker compose --profile prod up --build
```

- Frontend dev: http://localhost:5173
- Frontend prod: http://localhost
- Backend: http://localhost:8000

> **After adding new npm packages** the `node_modules` volume needs to be rebuilt:
> ```bash
> docker compose --profile dev down -v && docker compose --profile dev up --build
> ```

### 4. Copy vendor to host (dev only, first time)

To get IDE autocompletion and source browsing locally:

```bash
docker compose --profile dev cp backend-dev:/var/www/backend/vendor ./backend/vendor
```

Only needed once after the first `--build`, or when `composer.json` changes.

## Useful commands

```bash
# Artisan (dev)
docker compose --profile dev exec backend-dev php artisan <command>

# Artisan (prod)
docker compose --profile prod exec backend-prod php artisan <command>

# Redis CLI
docker compose --profile dev exec redis redis-cli

# Clear all caches
docker compose --profile dev exec backend-dev php artisan optimize:clear

# Rebuild a single service
docker compose --profile dev up --build backend-dev

# Stop
docker compose --profile dev down

# Stop and wipe Redis data
docker compose --profile dev down -v
```

## Running locally (without Docker)

Requires PHP 8.5 with `redis` extension and a local Redis instance.

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan serve
```

```bash
cd frontend
npm install
npm run dev
```
