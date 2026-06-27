# ─────────────────────────────────────────────────────────────────────────────
# Creative Trees Group — developer convenience commands
# Usage: `make <target>`   (run `make help` for the list)
# ─────────────────────────────────────────────────────────────────────────────
.DEFAULT_GOAL := help
DC := docker compose
APP := $(DC) exec app

.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'

## ── Lifecycle ───────────────────────────────────────────────────────────────
.PHONY: up
up: ## Start the full dev stack
	$(DC) up -d

.PHONY: down
down: ## Stop the dev stack
	$(DC) down

.PHONY: build
build: ## Build the app image
	$(DC) build

.PHONY: restart
restart: down up ## Restart the dev stack

.PHONY: logs
logs: ## Tail all container logs
	$(DC) logs -f --tail=100

.PHONY: ps
ps: ## Show container status
	$(DC) ps

## ── Application ──────────────────────────────────────────────────────────────
.PHONY: shell
shell: ## Open a shell in the app container
	$(APP) sh

.PHONY: install
install: ## Install PHP + JS dependencies
	$(APP) composer install
	$(APP) npm install

.PHONY: key
key: ## Generate the app key
	$(APP) php artisan key:generate

.PHONY: migrate
migrate: ## Run database migrations
	$(APP) php artisan migrate

.PHONY: fresh
fresh: ## Wipe DB and re-run migrations + seeders
	$(APP) php artisan migrate:fresh --seed

.PHONY: seed
seed: ## Run database seeders
	$(APP) php artisan db:seed

.PHONY: optimize
optimize: ## Cache config/routes/views (production warmup)
	$(APP) php artisan optimize

.PHONY: clear
clear: ## Clear all caches
	$(APP) php artisan optimize:clear

## ── Quality ──────────────────────────────────────────────────────────────────
.PHONY: test
test: ## Run the Pest test suite
	$(APP) php artisan test

.PHONY: pint
pint: ## Format code with Laravel Pint
	$(APP) ./vendor/bin/pint

.PHONY: assets
assets: ## Build production frontend assets
	$(APP) npm run build
