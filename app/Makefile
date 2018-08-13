migrate:
	php artisan.php make:migration

migrate-test:
	php artisan.php make:migration testdb

seeder:
	php artisan.php make:seeder

test:
	composer run-script phpunit

setup: migrate migrate-test seeder
