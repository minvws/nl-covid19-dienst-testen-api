# Installation & development

## Configuration

```sh
cp .env.example .env
```

Edit `.env` to fill out the required environment variables.

## Run with docker-compose

Local requirements: `docker`, `docker-compose`, `openssl`, `composer`.

Install the dependencies:

```sh
composer install
php artisan key:generate
```

Then run the application via sail:
```sh
vendor/bin/sail up
```

By default the application can be accessed at [http://localhost](http://localhost).

## Run another way (e.g. `php artisan serve`)

Local requirements: PHP 8 with `ext-json` and `ext-sodium`, `composer`.

Install the dependencies and build the frontend:

```sh
composer install
php artisan key:generate`
```

Then run the application however you normally run PHP application, or with artisan:

```
php artisan serve
```
