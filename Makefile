# StudHub developer make targets.
#
# Most targets work both inside the docker-compose stack and on a bare
# host with PHP 8.3 + Composer installed.

DC ?= docker compose

.PHONY: help up down restart logs sh ps install lint lint-fix analyse test ci fresh

help:
	@echo "StudHub Make targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	  | awk 'BEGIN {FS = ":.*?## "} {printf "  %-15s %s\n", $$1, $$2}'

up: ## Start the docker compose dev stack
	$(DC) up -d

down: ## Stop the docker compose dev stack
	$(DC) down

restart: ## Restart the dev stack
	$(DC) restart

logs: ## Tail logs
	$(DC) logs -f --tail=100

sh: ## Open a shell in the app container
	$(DC) exec app bash

ps: ## Show container status
	$(DC) ps

install: ## Install composer deps locally (host PHP)
	composer install

lint: ## Check formatting (CI mode)
	composer lint:check

lint-fix: ## Apply Pint fixes
	composer lint

analyse: ## Run PHPStan / Larastan
	composer analyse

test: ## Run Pest test suite
	composer test

ci: ## Run the full CI pipeline locally (lint + analyse + test)
	composer ci

fresh: ## Reset the local database (migrate:fresh --seed)
	php artisan migrate:fresh --seed
