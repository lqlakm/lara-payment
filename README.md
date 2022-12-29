# Paypal Payment Gateway with Laravel

### Requirements
- PHP version <= 7.4
- Paypal Sandbox Account

## Installation

Install the dependencies with composer

```sh
composer install
```

Copy and create .env file from .env.example and generate app key
```sh
php artisan key:generate
```

Update .env file for DB Connection and add paypal credentials
```
PAYPAL_SANDBOX_CLIENT_ID=
PAYPAL_SANDBOX_CLIENT_SECRET=
```

Run DB migrations and server with php
```sh
php artisan migrate
php artisan serve
```
