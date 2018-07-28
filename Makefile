migrate:
	cd public/ && php ../src/Database/Migration.php

testdata:
	cd public/ && php ../src/Database/TestData.php