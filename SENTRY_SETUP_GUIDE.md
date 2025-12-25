# Sentry Setup Guide

## Overview

Sentry is configured for error tracking and monitoring in the group.one Centralized License Service.

---

## 1. Installation

✅ **Already Completed**

```bash
composer require sentry/sentry-laravel
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

---

## 2. Configuration

### Step 1: Get Sentry DSN

1. Go to [sentry.io](https://sentry.io)
2. Create a new project or use existing one
3. Copy the DSN from Project Settings → Client Keys (DSN)

### Step 2: Add to Environment File

Add to `.env`:

```env
SENTRY_LARAVEL_DSN=https://your-dsn@sentry.io/your-project-id
SENTRY_TRACES_SAMPLE_RATE=1.0
SENTRY_PROFILES_SAMPLE_RATE=1.0
```

Add to `.env.example`:

```env
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=1.0
SENTRY_PROFILES_SAMPLE_RATE=1.0
```

### Step 3: Update Sentry Config

Edit `config/sentry.php`:

```php
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    // Capture release version
    'release' => env('APP_VERSION', '1.0.0'),

    // Environment
    'environment' => env('APP_ENV', 'production'),

    // Sample rate for error events (0.0 to 1.0)
    'sample_rate' => 1.0,

    // Sample rate for performance monitoring (0.0 to 1.0)
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.0),

    // Sample rate for profiling (0.0 to 1.0)
    'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0.0),

    // Send default PII (Personally Identifiable Information)
    'send_default_pii' => false,

    // Breadcrumbs
    'breadcrumbs' => [
        // Capture SQL queries
        'sql_queries' => true,
        
        // Capture SQL bindings
        'sql_bindings' => true,
        
        // Capture queue job information
        'queue_info' => true,
        
        // Capture command information
        'command_info' => true,
    ],

    // Performance monitoring
    'tracing' => [
        // Trace queue jobs
        'queue_job_transactions' => env('SENTRY_TRACE_QUEUE_ENABLED', false),
        
        // Trace queue jobs from these queues
        'queue_jobs' => true,
        
        // Trace SQL queries
        'sql_queries' => true,
        
        // Trace Redis commands
        'redis_commands' => env('SENTRY_TRACE_REDIS_COMMANDS', false),
        
        // Trace HTTP client requests
        'http_client_requests' => true,
    ],
];
```

---

## 3. Integration with Controllers

### Using Try-Catch with Sentry

Sentry automatically captures exceptions, but you can add context:

```php
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response;

class LicenseController extends Controller
{
    use ApiResponse;

    public function store(StoreLicenseRequest $request): JsonResponse
    {
        try {
            $dto = $request->createLicenseDTO();
            $license = $this->licenseService->createLicense($dto);
            return $this->created(__('messages.license-created'), new LicenseResource($license));
        } catch (Exception $e) {
            // Sentry automatically captures this exception
            // But you can add custom context:
            \Sentry\captureException($e);
            
            // Or add context before capturing:
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($request) {
                $scope->setContext('request_data', [
                    'brand_id' => $request->brand_id,
                    'customer_email' => $request->customer_email,
                ]);
            });
            
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
```

### Adding User Context

```php
// In a middleware or controller
\Sentry\configureScope(function (\Sentry\State\Scope $scope) {
    $scope->setUser([
        'id' => auth()->id(),
        'email' => auth()->user()->email,
    ]);
});
```

### Adding Tags

```php
\Sentry\configureScope(function (\Sentry\State\Scope $scope) {
    $scope->setTag('license_type', 'subscription');
    $scope->setTag('brand_id', $brandId);
});
```

---

## 4. Testing Sentry

### Create a Test Route

Add to `routes/web.php`:

```php
Route::get('/sentry-test', function () {
    throw new Exception('Sentry test exception');
});
```

### Test the Integration

1. Visit `/sentry-test` in your browser
2. Check Sentry dashboard for the error
3. Verify error details, stack trace, and context

### Remove Test Route

After testing, remove the test route from `routes/web.php`.

---

## 5. Sentry in Production

### Best Practices

1. **Don't Send PII** - Set `send_default_pii` to `false`
2. **Sample Rates** - Adjust sample rates based on traffic:
   - High traffic: 0.1 (10%)
   - Medium traffic: 0.5 (50%)
   - Low traffic: 1.0 (100%)
3. **Release Tracking** - Set `APP_VERSION` in `.env`
4. **Environment** - Ensure `APP_ENV` is set correctly

### Performance Monitoring

Enable performance monitoring for production:

```env
SENTRY_TRACES_SAMPLE_RATE=0.2  # 20% of transactions
SENTRY_PROFILES_SAMPLE_RATE=0.2  # 20% of transactions
```

### Ignoring Exceptions

Edit `config/sentry.php`:

```php
'ignore_exceptions' => [
    Illuminate\Auth\AuthenticationException::class,
    Illuminate\Validation\ValidationException::class,
],
```

---

## 6. Sentry CLI (Optional)

### Install Sentry CLI

```bash
npm install -g @sentry/cli
```

### Configure

Create `.sentryclirc`:

```ini
[auth]
token=your-auth-token

[defaults]
url=https://sentry.io/
org=your-org
project=your-project
```

### Upload Source Maps (for frontend)

```bash
sentry-cli releases new $VERSION
sentry-cli releases files $VERSION upload-sourcemaps ./public/build
sentry-cli releases finalize $VERSION
```

---

## 7. Monitoring

### Key Metrics to Monitor

1. **Error Rate** - Track error frequency
2. **Response Time** - Monitor API performance
3. **User Impact** - See how many users affected
4. **Release Health** - Track errors by release version

### Alerts

Set up alerts in Sentry:

1. Go to Alerts → Create Alert Rule
2. Set conditions (e.g., error count > 10 in 5 minutes)
3. Configure notifications (email, Slack, etc.)

---

## 8. Integration with CI/CD

### GitHub Actions

Add to `.github/workflows/deploy.yml`:

```yaml
- name: Create Sentry Release
  env:
    SENTRY_AUTH_TOKEN: ${{ secrets.SENTRY_AUTH_TOKEN }}
    SENTRY_ORG: your-org
    SENTRY_PROJECT: your-project
  run: |
    curl -sL https://sentry.io/get-cli/ | bash
    sentry-cli releases new "${{ github.sha }}"
    sentry-cli releases set-commits "${{ github.sha }}" --auto
    sentry-cli releases finalize "${{ github.sha }}"
```

---

## 9. Troubleshooting

### Sentry Not Capturing Errors

1. Check DSN is set correctly in `.env`
2. Verify `APP_ENV` is not `local` (Sentry disabled in local by default)
3. Check `config/sentry.php` configuration
4. Clear config cache: `php artisan config:clear`

### Too Many Events

1. Reduce sample rates
2. Add exceptions to ignore list
3. Filter by environment

### Missing Context

1. Add breadcrumbs
2. Set user context
3. Add custom tags and context

---

## 10. Resources

- [Sentry Laravel Documentation](https://docs.sentry.io/platforms/php/guides/laravel/)
- [Sentry PHP SDK](https://docs.sentry.io/platforms/php/)
- [Performance Monitoring](https://docs.sentry.io/product/performance/)
- [Release Health](https://docs.sentry.io/product/releases/health/)


