migrate:
	php artisan.php make:migration

seeder:
	php artisan.php make:seeder

test-unit:
	vendor/bin/phpunit

test-behat:
	vendor/bin/behat

test-all: test-unit test-behat

dev-vendor:
	composer install && composer update

prod-vendor:
	composer install --no-dev && composer update --no-dev

dev-setup: dev-vendor migrate seeder

prod-setup: prod-vendor migrate seeder

update-autoload:
	composer dump-autoload

lint:
	composer phpcs

lint-fix:
	composer phpcbf
