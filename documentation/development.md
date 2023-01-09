# Installation & development

## Configuration
The application needs environment variables and a result providers config file.



### Create .env file based of .env.example
```sh
cp .env.example .env
```

Edit `.env` to fill out the required environment variables.
If you want 

### Create result-providers.json file
If you want to start with an example file you could copy the example file with the following command.
```sh
cp result-providers.json.example result-providers.json
```

[Read more about the result providers file](result-providers.md)

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

By default, the application can be accessed at [http://localhost](http://localhost).

## Run another way (e.g. `php artisan serve`)

Local requirements: PHP 8.1 with `ext-json` and `ext-sodium`, `composer`.

Install the dependencies:

```sh
composer install
php artisan key:generate`
```

Then run the application however you normally run PHP application, or with artisan:

```
php artisan serve
```
