PWD=$(shell pwd)

default: up

.PHONY: up
up:: ##@Compose Start from docker-compose.yml
	$(shell_env) docker-compose --file docker-compose.yml up -d

.PHONY: down
down::
	$(shell_env) docker-compose --file docker-compose.yml stop

.PHONY: composer
composer::
	$(shell_env) composer up
#
# .PHONY: db
# db::
#     $(shell_env) bin/console orm:schema-tool:update --dump-sql --force