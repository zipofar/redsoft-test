setup:
	docker-compose up -d & docker-compose run mariadb /wait-for-db.sh && docker-compose run php make setup

run:
	docker-compose up -d

test:
	docker-compose run php make test
