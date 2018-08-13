setup:
	docker-compose run php make setup

run:
	docker-compose up -d

test:
	docker-compose run php make test
