include .env

DOCKER_COMPOSE=docker-compose
DC=${DOCKER_COMPOSE}
DOCKER_COMPOSE_EXEC=${DC} exec
EXEC=${DOCKER_COMPOSE_EXEC}
ARTISAN=${EXEC} php php artisan
DB_DOCKER=docker exec db
PSQL=${DB_DOCKER} psql -d ${DB_DATABASE}

set-migrations-table:
	$(call PSQL) -tc  "SELECT 1 FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'migrations'" | \
	grep -q 1 || ${ARTISAN} migrate:install

remigrate:
	${ARTISAN} migrate:fresh

rollback:
	${ARTISAN} migrate:rollback
