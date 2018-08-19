migrate:
	php artisan.php make:migration

migrate-test:
	php artisan.php make:migration testdb

seeder:
	php artisan.php make:seeder

test:
	composer run-script phpunit

dev-vendor:
	composer install

prod-vendor:
	composer install --no-dev

dev-setup: dev-vendor migrate migrate-test seeder

prod-setup: prod-vendor migrate seeder

update-autoload:
	composer dump-autoload

