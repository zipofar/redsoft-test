setup:
	docker-compose run mariadb && docker-compose run php make setup

run:
	docker-compose up -d
