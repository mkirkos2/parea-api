# Parea API 🚀
> RESTful Backend API powering the Parea Event Discovery & Social Community Platform.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
[![Authentication](https://img.shields.io/badge/Sanctum-Token_Auth-red?style=for-the-badge)](https://laravel.com/docs/sanctum)
[![Database](https://img.shields.io/badge/SQLite%2FMySQL-Supported-blue?style=for-the-badge&logo=sqlite&logoColor=white)]()

## 📌 Architecture & Features
- **Token Authentication:** Secure multi-device login & bearer token verification via Laravel Sanctum.
- **Event Lifecycle Management:** Full CRUD endpoints for creating, filtering, joining, and hosting community events.
- **Real-Time Participation Tracking:** Attendance verification, host authorization guards, and participation status handling.
- **Reporting & Moderation System:** Endpoints for user/content reports with status enforcement.
- **API Resources & Normalization:** Clean JSON responses transformed via API Resource layer.

## 🛠️ Tech Stack
- **Framework:** Laravel 10.x
- **Language:** PHP 8.2+
- **Database:** SQLite (Development) / MySQL (Production)
- **Security:** Sanctum Bearer Token Auth, CORS Guard Rules, Input Validation Requests

## 🚀 Quick Start
```bash
# Clone the repository
git clone https://github.com/mkirkos2/parea-api.git
cd parea-api

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database migration & seed
php artisan migrate

# Run local development server
php artisan serve --host=0.0.0.0 --port=8000
```