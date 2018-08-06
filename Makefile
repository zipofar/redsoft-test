migrate:
	php  artisan.php migrate

migrate-test:
	php artisan.php migrate testdb

testdata:
	cd public/ && php ../src/Database/TestData.php

lint:
	composer run-script phpcs -- --standard=PSR2 src

fixlint:
	composer run-script phpcbf -- --standard=PSR2 src

test:
	composer run-script phpunit

setup:
	make migrate && make migrate-test && make testdata
