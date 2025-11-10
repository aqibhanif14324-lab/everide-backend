# Session & CSRF Configuration Guide

## Overview
This document explains the session and CSRF token setup for Laravel Sanctum SPA authentication in the CasseCasse marketplace application.

## Backend Configuration

### Environment Variables (.env)
```env
# Application URL
APP_URL=http://127.0.0.1:8000

# Frontend URL
FRONTEND_URL=http://localhost:5173

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=localhost
SESSION_SAME_SITE=lax

# Sanctum Stateful Domains
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
```

### Important Notes

1. **SESSION_DOMAIN**: 
   - Must match the domain used by the frontend
   - If frontend runs on `localhost:5173`, use `SESSION_DOMAIN=localhost`
   - If frontend runs on `127.0.0.1:5173`, use `SESSION_DOMAIN=127.0.0.1`
   - **Do not mix** `localhost` on frontend with `127.0.0.1` in SESSION_DOMAIN

2. **SANCTUM_STATEFUL_DOMAINS**:
   - Must include the frontend URL (with port if applicable)
   - Format: `domain:port` (e.g., `localhost:5173`)
   - Multiple domains separated by commas

3. **SESSION_DRIVER**:
   - Use `file` for local development (default)
   - Ensure `storage/framework/sessions` directory is writable
   - On Windows/WAMP, check folder permissions

### Route Middleware

#### Auth Routes (Login, Register, Logout)
- Use `api.session` middleware group
- Includes: `EnsureFrontendRequestsAreStateful`
- This enables session support for stateful requests
- Session is automatically started for requests from stateful domains

#### Protected API Routes
- Use `auth:sanctum` middleware
- Session is maintained via cookies for stateful requests
- No explicit session middleware needed (handled by Sanctum)

### Session Storage

1. **File Driver** (default for local dev):
   - Sessions stored in `storage/framework/sessions`
   - Ensure directory exists and is writable
   - Run: `php artisan storage:link` if needed

2. **Database Driver** (production):
   - Requires `sessions` table
   - Run: `php artisan session:table` and `php artisan migrate`

### CSRF Token Flow

1. **Frontend requests CSRF cookie**:
   - `GET /sanctum/csrf-cookie`
   - Backend sets CSRF cookie in response

2. **Frontend includes CSRF token in requests**:
   - Token is automatically included in cookies
   - Sanctum validates token for stateful requests

3. **CSRF Protection**:
   - Handled by `EnsureFrontendRequestsAreStateful` middleware
   - Only applies to requests from stateful domains
   - Token validated automatically for POST/PUT/DELETE requests

## Frontend Configuration

### Environment Variables (.env)
```env
VITE_API_BASE_URL=http://127.0.0.1:8000
```

### Request Configuration

1. **CSRF Cookie Setup** (on app boot):
   ```javascript
   await axios.get(`${API_URL}/sanctum/csrf-cookie`, {
     withCredentials: true,
     headers: {
       'X-Requested-With': 'XMLHttpRequest',
     },
   });
   ```

2. **All API Requests**:
   - Must include `withCredentials: true`
   - Must include `X-Requested-With: XMLHttpRequest` header
   - CSRF token is automatically included in cookies

### Axios Configuration
```javascript
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

## Common Issues & Solutions

### Issue: "Session store not set on request"
**Cause**: Auth routes don't have session middleware
**Solution**: 
- Ensure auth routes use `api.session` middleware group
- Check that `EnsureFrontendRequestsAreStateful` is applied
- Verify `SANCTUM_STATEFUL_DOMAINS` includes frontend URL

### Issue: CSRF Token Mismatch (419 error)
**Causes**:
1. Frontend domain not in `SANCTUM_STATEFUL_DOMAINS`
2. `SESSION_DOMAIN` doesn't match frontend domain
3. CSRF cookie not set before making POST requests
4. Cookies not being sent (missing `withCredentials: true`)

**Solutions**:
1. Add frontend URL to `SANCTUM_STATEFUL_DOMAINS`
2. Match `SESSION_DOMAIN` with frontend domain
3. Call `/sanctum/csrf-cookie` before POST requests
4. Ensure `withCredentials: true` in all requests

### Issue: Session not persisting
**Causes**:
1. `SESSION_DOMAIN` mismatch
2. Cookies blocked by browser
3. Session driver not working (file permissions)

**Solutions**:
1. Check `SESSION_DOMAIN` matches frontend domain
2. Check browser console for cookie errors
3. Verify `storage/framework/sessions` is writable
4. Clear browser cookies and try again

### Issue: 401 Unauthorized after login
**Causes**:
1. Session not started
2. Auth guard not using session
3. Cookies not being sent

**Solutions**:
1. Verify session middleware is applied to auth routes
2. Check that `auth:sanctum` uses `web` guard
3. Ensure `withCredentials: true` in requests
4. Check browser cookies - session cookie should be present

## Testing Checklist

1. **CSRF Cookie**:
   - [ ] `GET /sanctum/csrf-cookie` returns 200
   - [ ] CSRF cookie is set in browser
   - [ ] Cookie domain matches `SESSION_DOMAIN`

2. **Login**:
   - [ ] `POST /api/auth/login` returns 200 (no 500)
   - [ ] No "Session store not set" error
   - [ ] Session cookie is set
   - [ ] User data is returned

3. **Authenticated Request**:
   - [ ] `GET /api/me` returns user (no 401)
   - [ ] Session persists across requests
   - [ ] Cookies are sent with requests

4. **Logout**:
   - [ ] `POST /api/auth/logout` returns 200
   - [ ] Session is invalidated
   - [ ] Subsequent `/api/me` returns 401

## Production Considerations

1. **HTTPS**: Use HTTPS in production
2. **SESSION_SECURE_COOKIE**: Set to `true` with HTTPS
3. **SESSION_SAME_SITE**: Consider `lax` or `none` (with secure)
4. **Session Driver**: Use `database` or `redis` in production
5. **SANCTUM_STATEFUL_DOMAINS**: Include production frontend domain
6. **SESSION_DOMAIN**: Match production domain (e.g., `.yourdomain.com`)

## Windows/WAMP Specific Notes

1. **Session Storage**: Ensure `storage/framework/sessions` is writable
2. **Permissions**: Check folder permissions in Windows
3. **Path**: Use forward slashes in paths (Laravel handles this)
4. **Domain**: Use `localhost` consistently (not `127.0.0.1`)

## Quick Fix Commands

```bash
# Clear all caches
php artisan optimize:clear

# Clear session files
php artisan session:clear

# Check session storage permissions
chmod -R 775 storage/framework/sessions

# Restart server
php artisan serve
```

## Verification

After configuration, verify:
1. Session files are created in `storage/framework/sessions`
2. CSRF cookie is set when calling `/sanctum/csrf-cookie`
3. Login succeeds without "Session store not set" error
4. `/api/me` returns user after login
5. Session persists across page refreshes
