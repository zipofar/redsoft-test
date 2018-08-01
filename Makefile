migrate:
	cd public/ && php ../src/Database/Migration.php

testdata:
	cd public/ && php ../src/Database/TestData.php
lint:
		composer run-script phpcs -- --standard=PSR2 src
fixlint:
		composer run-script phpcbf -- --standard=PSR2 src