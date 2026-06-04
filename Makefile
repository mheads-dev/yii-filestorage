CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
$(eval $(sort $(subst :,\:,$(CLI_ARGS))):;@:)

COMPOSE=docker compose -f docker/docker-compose.yml $(if $(wildcard docker/docker-compose.override.yml),-f docker/docker-compose.override.yml)

help: ## Show the list of available commands with description.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
.DEFAULT_GOAL := help

build: ## Build PHP test image
	$(COMPOSE) build

up: ## Start PHP test container
	$(COMPOSE) up -d

ps: ## List running services
	$(COMPOSE) ps

stop: ## Stop running services
	$(COMPOSE) stop

down: ## Stop services and remove containers
	$(COMPOSE) down --remove-orphans

run: ## Run arbitrary command. Example: make run CMD="php -v"
	$(COMPOSE) run --rm php $(CMD)

shell: ## Open shell in PHP container
	$(COMPOSE) run --rm php bash

test: ## Run PHPUnit tests in Docker
	$(COMPOSE) run --rm php vendor/bin/phpunit $(RUN_ARGS)

psalm: ## Run static analysis using Psalm in Docker
	$(COMPOSE) run --rm php vendor/bin/psalm --no-cache

cs-fixer: ## Run code-style fixer in Docker
	$(COMPOSE) run --rm php vendor/bin/php-cs-fixer fix $(RUN_ARGS)

cs-check: ## Check code style without changing files in Docker
	$(COMPOSE) run --rm php vendor/bin/php-cs-fixer fix --dry-run --diff

composer: ## Run Composer.
	$(COMPOSE) run --rm php composer $(CLI_ARGS)
