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
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env.local
```

Auth0 credentials and CORS origins are pre-configured in `.env.example` files. Auth0 is set up for frontend ports 5173 (dev) and 80 (prod). If another port is needed, feel free to contact me or create your own Auth0 keys.

| Variable              | Description                        |
|-----------------------|------------------------------------|
| `VITE_AUTH0_DOMAIN`   | e.g. `your-tenant.auth0.com`       |
| `VITE_AUTH0_CLIENT_ID`| Auth0 SPA application Client ID    |
| `VITE_AUTH0_AUDIENCE` | Auth0 API audience identifier      |
| `VITE_BACKEND_API_URL`| Backend URL, default `http://localhost:8000` |

Edit `backend/.env` — set `FRONTEND_URL` to allowed CORS origins (comma-separated):

```
FRONTEND_URL=http://localhost:5173,http://localhost
```

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

Dependencies (`node_modules` and `vendor`) are installed automatically on first container start via entrypoint scripts. They live in the bind mount, so your IDE picks them up without extra steps. They are reinstalled when `package.json` or `composer.json` changes.

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
