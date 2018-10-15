run:
	docker-compose up -d

run-dev:
	docker-compose -f docker-compose_dev.yml up -d

run-test:
	docker-compose -f docker-compose_test.yml up -d

kill:
	docker-compose kill

development-setup:
	docker-compose run php make dev-setup

production-setup:
	docker-compose run php make prod-setup

test:
	docker-compose run php make test

ansible-development-setup:
	mkdir -p tmp
	echo 'password' > tmp/ansible-vault-password
	ansible-playbook ansible/development.yml -i ansible/development -vv -K

ansible-production-setup:
	mkdir -p tmp
	echo '' >> tmp/ansible-vault-password
	ansible-playbook ansible/production.yml -i ansible/production -vv -K

ansible-vaults-encrypt:
	ansible-vault encrypt ansible/production/group_vars/all/vault.yml

ansible-vaults-decrypt:
	ansible-vault decrypt ansible/production/group_vars/all/vault.yml

update-autoload:
	docker-compose run php make update-autoload

build-dev:
	docker-compose -f docker-compose_dev.yml build

build-prod:
	docker-compose build
