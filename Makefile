migrate:
	php  artisan.php make:migration

migrate-test:
	php artisan.php make:migration testdb

testdata:
	cd public/ && php ../src/Database/TestData.php

lint:
	composer run-script phpcs -- --standard=PSR2 src

fixlint:
	composer run-script phpcbf -- --standard=PSR2 src

test:
	composer run-script phpunit

setup:
	make migrate && make migrate-test && make make:seeder
