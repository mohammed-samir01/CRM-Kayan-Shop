# Production Deployment Guide

## 1. Environment Configuration

1.  **Copy Environment File**:
    ```bash
    cp .env.example .env
    ```
2.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```
3.  **Update Configuration**:
    Edit `.env` and set:
    *   `APP_ENV=production`
    *   `APP_DEBUG=false`
    *   `APP_URL=https://your-domain.com`
    *   Database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
    *   Mail server settings (`MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`)

## 2. Dependencies & Build

1.  **Install PHP Dependencies**:
    ```bash
    composer install --optimize-autoloader --no-dev
    ```
2.  **Install Node Dependencies & Build Assets**:
    ```bash
    npm ci
    npm run build
    ```

## 3. Database

1.  **Run Migrations**:
    ```bash
    php artisan migrate --force
    ```
    *Note: This includes performance indexes for leads and orders.*

## 4. Optimization Commands (Performance)

Run these commands to cache configuration and views for better performance:

```bash
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

*Important: If you change `.env` or any config file, you must run `php artisan config:clear` and `php artisan config:cache` again.*

## 5. Queue & Task Scheduling

1.  **Queue Worker**:
    Ensure a queue worker is running (e.g., using Supervisor):
    ```bash
    php artisan queue:work --tries=3 --timeout=90
    ```
2.  **Scheduler**:
    Add the following Cron entry to run the Laravel scheduler every minute:
    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

## 6. Permissions

Ensure the web server (e.g., `www-data`) has write access to:
*   `storage/`
*   `bootstrap/cache/`

## 7. Health Check

Verify the application status by visiting:
`https://your-domain.com/health`

Expected response:
```json
{
  "status": "ok",
  "timestamp": "2024-01-22T12:00:00+00:00",
  "environment": "production"
}
```
