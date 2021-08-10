DOCKER_COMPOSE=docker-compose
DC=${DOCKER_COMPOSE}
DOCKER_COMPOSE_EXEC=${DC} exec
EXEC=${DOCKER_COMPOSE_EXEC}
PHP=${EXEC} php
ARTISAN=${PHP} php artisan

up:
	${DC} up -d --force-recreate --build

env-prepare:
	if [ ! -f .env ]; then cp .env.example .env; fi;

install-dependencies:
	composer install

set-git-hooks:
	git config --global core.hooksPath ./git-hooks

install: env-prepare set-jwt up install-dependencies set-git-hooks set-migrations-table

down:
	${DC} down

lint:
	./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes -v --using-cache=no

php-cli:
	${PHP} bash

test:
	${ARTISAN} test

queue:
	${ARTISAN} queue:work database

set-jwt:
	${ARTISAN} jwt:secret

.PHONY: install up down lint php-cli test
