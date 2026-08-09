# Parea API

Phase 1 Laravel API foundation

## Stack
- PHP 8.5
- Laravel 13
- Laravel Sanctum
- MySQL deployment target
- SQLite for local/test foundation

## Current Endpoints
- `GET /api/v1/health` - API health check

## Installation

1. Copy environment file:
   ```bash
   cp .env.example .env
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

## Development

Start local server:
```bash
php artisan serve
```

Run tests:
```bash
php artisan test
```

Validate composer:
```bash
composer validate
```

Audit composer:
```bash
composer audit
```

## API Response Conventions

All API responses follow a consistent JSON structure:
```json
{
  "data": {...}
}
```

Error responses:
```json
{
  "message": "Error description"
}
```

## Status

⚠️ Business-domain endpoints are not implemented yet

✅ Phase 1: Laravel API foundation complete
🔜 Phase 2: Authentication and user profile planned

## Security

- No real credentials are stored in the repository
- All sensitive configuration is in `.env` (excluded from Git)
- API follows secure practices for authentication (Laravel Sanctum)