# Parea API

Current status:

Phase 1 Laravel API foundation.

## Stack

- PHP 8.5
- Laravel 13
- Laravel Sanctum
- MySQL as the intended deployment database
- SQLite currently used for local/test foundation where configured

## Implemented

- Laravel API routing
- API prefix /api/v1
- GET /api/v1/health
- JSON responses for unknown API routes
- Sanctum foundation
- automated foundation tests

## Not yet implemented

- registration
- login/logout endpoints
- current-user profile endpoint
- categories
- events
- participations
- favorites
- chat
- reports
- deployment configuration

## Setup

1. Install dependencies:
   ```bash
   composer install
   ```

2. Copy environment file:
   ```bash
   cp .env.example .env
   ```

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

4. Create/configure a local database

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Start local server:
   ```bash
   php artisan serve
   ```

7. Run tests:
   ```bash
   php artisan test
   ```

## Validation

Validate composer:
```bash
composer validate
```

Audit composer:
```bash
composer audit
```

## Security

- No real credentials are stored in the repository
- All sensitive configuration is in `.env` (excluded from Git)
- API follows secure practices for authentication (Laravel Sanctum)