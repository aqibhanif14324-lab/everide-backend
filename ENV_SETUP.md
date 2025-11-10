# Environment Setup for Local Development

## Backend (.env)

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:... (generate with php artisan key:generate)
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

FRONTEND_URL=http://localhost:5173

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=localhost
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=everide
DB_USERNAME=root
DB_PASSWORD=
```

## Frontend (.env)

```env
VITE_API_BASE_URL=http://127.0.0.1:8000
```

## Important Notes

1. **SESSION_DOMAIN**: Must match the domain used by the frontend
   - If frontend runs on `localhost:5173`, use `SESSION_DOMAIN=localhost`
   - If frontend runs on `127.0.0.1:5173`, use `SESSION_DOMAIN=127.0.0.1`
   - **Do not mix** `localhost` on frontend with `127.0.0.1` in SESSION_DOMAIN

2. **SANCTUM_STATEFUL_DOMAINS**: Must include the frontend URL with port
   - Format: `domain:port` (e.g., `localhost:5173`)
   - Multiple domains separated by commas

3. **SESSION_DRIVER**: Use `file` for local development
   - Ensure `storage/framework/sessions` directory is writable
   - On Windows/WAMP, check folder permissions

## Setup Steps

1. Copy `.env.example` to `.env` (if not exists)
2. Update the values above in `.env`
3. Generate application key: `php artisan key:generate`
4. Ensure session storage is writable: `chmod -R 775 storage/framework/sessions`
5. Clear caches: `php artisan optimize:clear`
6. Restart server: `php artisan serve`

## Verification

After setup, verify:
1. Session files are created in `storage/framework/sessions`
2. CSRF cookie is set when calling `/sanctum/csrf-cookie`
3. Login succeeds without "Session store not set" error
4. `/api/me` returns user after login
