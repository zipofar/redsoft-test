migrate:
	php artisan.php make:migration

migrate-test:
	php artisan.php make:migration testdb

seeder:
	php artisan.php make:seeder

test:
	composer run-script phpunit

vendor:
	composer install

setup: vendor migrate migrate-test seeder
