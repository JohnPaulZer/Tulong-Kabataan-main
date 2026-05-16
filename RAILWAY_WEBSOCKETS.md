# Railway WebSocket Setup

This project uses Laravel Reverb for realtime browser updates.

## Recommended Railway Services

Use two Railway services that point to the same repository:

1. Main Laravel web service
   - Start command depends on the existing Railway/PHP setup.
   - It must have the same `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_APP_SECRET` as the Reverb service.

2. Reverb WebSocket service
   - Start command:

```bash
php artisan reverb:start --host=0.0.0.0 --port=$PORT
```

## Production Environment Values

Set these on the main Laravel service:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-shared-app-id
REVERB_APP_KEY=your-shared-app-key
REVERB_APP_SECRET=your-shared-app-secret
REVERB_HOST=your-reverb-service.up.railway.app
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_ALLOWED_ORIGINS=https://your-main-app.up.railway.app
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
SECURITY_CSP_EXTRA_CONNECT=wss://your-reverb-service.up.railway.app
```

Set these on the Reverb service:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-shared-app-id
REVERB_APP_KEY=your-shared-app-key
REVERB_APP_SECRET=your-shared-app-secret
REVERB_HOST=your-reverb-service.up.railway.app
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=$PORT
REVERB_ALLOWED_ORIGINS=https://your-main-app.up.railway.app
```

The `VITE_REVERB_*` values are compiled into the frontend bundle, so rebuild/redeploy the main Laravel service after changing them.


## Required Variables for Any Railway Service Behind HTTPS

Railway terminates TLS at its edge proxy and forwards plain HTTP to the
container. To stop the browser from blocking redirects, form posts, and fetch
requests with `Mixed Content` / `form-action 'self'` / `connect-src 'self'`
errors, set these on the main Laravel service:

```env
APP_ENV=staging        # or production
APP_DEBUG=false
APP_URL=https://tkb-staging.tulongkabataanbicol.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=tkb-staging.tulongkabataanbicol.com
TRUSTED_PROXIES=*
```

The application now:
- trusts forwarded headers from the Railway/Cloudflare proxy
  (`bootstrap/app.php` calls `trustProxies(at: '*')`), so `$request->isSecure()`
  returns true and Laravel generates `https://` URLs.
- forces `https://` whenever `APP_URL` starts with `https://` or the env is
  `staging` / `production` (`AppServiceProvider::boot`).
- allows the Cloudflare insights script (`static.cloudflareinsights.com`) and
  beacon endpoint in CSP, which Cloudflare injects automatically on proxied
  domains.

If the staging domain is also proxied through Cloudflare, also add Cloudflare
as a trusted proxy by leaving `TRUSTED_PROXIES=*`. After updating environment
variables, redeploy the service so `php artisan config:cache` picks up the new
values.
