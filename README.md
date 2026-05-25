# AIRO Travel Quotation API

Laravel implementation of the travel insurance quotation challenge. It exposes a JWT-protected JSON endpoint and a simple web form at `/` for submitting quotation requests.

## Requirements

- PHP 8.3 with `pdo_mysql`
- MySQL 8+
- Composer

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create the application and testing databases in MySQL:

```sql
CREATE DATABASE airo_coding_challenge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE airo_coding_challenge_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configure MySQL credentials in `.env`, generate a JWT secret, then migrate and seed the demo user:

```bash
php artisan jwt:secret
php artisan migrate --seed
```

Demo credentials:

- Email: `demo@example.com`
- Password: `password`

## API

`POST /quotation`

Required headers:

```http
Content-Type: application/json
Authorization: Bearer <JWT token>
```

Required JSON payload:

```json
{
    "age": "28,35",
    "currency_id": "EUR",
    "start_date": "2020-10-01",
    "end_date": "2020-10-30"
}
```

Successful response (`201 Created`):

```json
{
    "total": 117,
    "currency_id": "EUR",
    "quotation_id": 1
}
```

Traveller ages must be comma-separated integers from 18 through 70. Supported currencies are `EUR`, `GBP`, and `USD`. Trip days include both the start and end date.

## Authentication

Tokens are handled by `php-open-source-saver/jwt-auth`. Log in with the demo credentials to obtain a JWT:

```http
POST /login
Content-Type: application/json
Accept: application/json
```

```json
{
    "email": "demo@example.com",
    "password": "password"
}
```

Successful response:

```json
{
    "token": "<jwt token>"
}
```

Send the token in the `Authorization` header when calling `/quotation`:

```http
Authorization: Bearer <jwt token>
```

The browser form at `/` also has a simple login form. On successful login it stores the JWT in `localStorage` and uses it for quotation requests.

## Tests

Tests use the isolated `airo_coding_challenge_test` MySQL database configured in `phpunit.xml`. `RefreshDatabase` migrates this database during feature testing.

```bash
php artisan test
```
