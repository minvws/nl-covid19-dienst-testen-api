check:
	./vendor/bin/phpcs
	./vendor/bin/psalm
	./vendor/bin/phpstan

test:
	./vendor/bin/pest

sail:
	# You should have copied and modified `.env` first!
	# cp .env.example .env
	composer install
	php artisan key:generate
	vendor/bin/sail up

serve:
	# You should have copied and modified `.env` first!
	# cp .env.example .env
	composer install
	php artisan key:generate
	php artisan serve
