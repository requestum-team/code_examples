PHP_CONTAINER = php

.PHONY: php setup setup-database warmup start stop restart setup-test help test rebuild clean

check-symfony-dev: ## Fail if Symfony environment is not dev
	@ENV=$$(grep "^APP_ENV=" api/.env 2>/dev/null | cut -d '=' -f2 || echo "dev"); \
	if [ "$$ENV" != "dev" ]; then \
		echo "\033[31m[ERROR]\033[0m APP_ENV in api/.env must be 'dev', but got '$$ENV'"; \
		exit 1; \
	fi

help: ## Show help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
	awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-18s\033[0m %s\n", $$1, $$2}'

php: ## Open bash in the PHP container
	docker compose exec $(PHP_CONTAINER) bash

setup: check-symfony-dev ## Full reinstall: remove containers, volumes, rebuild everything from scratch
	docker compose down -v --remove-orphans
	docker system prune -af --volumes
	docker compose build --no-cache
	docker compose up -d
	sleep 10
	make setup-database
	make setup-test
	make jwt-keys
	make _post-install

setup-database: check-symfony-dev ## Drop, create, migrate and load fixtures
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:database:drop --force
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:database:create
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:fixtures:load --no-interaction

setup-test: check-symfony-dev ## Setup database for the test environment
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:database:drop --env=test --force || true
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:database:create --env=test
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:schema:create --env=test

jwt-keys: ## Generate JWT key pair for LexikJWTAuthenticationBundle
	docker compose exec $(PHP_CONTAINER) php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

warmup: ## Run migrations and fixtures, then restart
	docker compose exec -T php sh -c '\
    		if [ ! -d vendor ] || [ composer.lock -nt vendor ]; then \
    			echo "📦 (Re)installing Composer dependencies..."; \
    			composer install --no-scripts --no-progress --prefer-dist --no-dev && \
    			composer install --no-scripts --no-progress --prefer-dist; \
    		else \
    			echo "✅ Dependencies already up-to-date."; \
    		fi'
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec $(PHP_CONTAINER) bin/console doctrine:fixtures:load --no-interaction
	make setup-test
	make restart

start: ## Start all containers
	docker compose up -d
	make _post-install

stop: ## Stop all containers
	docker compose down --remove-orphans

restart: ## Clear Symfony cache and restart PHP container
	docker compose exec $(PHP_CONTAINER) bin/console cache:clear
	docker compose restart $(PHP_CONTAINER)

test: ## Run PHPUnit tests
	docker compose exec $(PHP_CONTAINER) php bin/phpunit

_post-install: ## (internal) Show success message after install
	@echo ""
	@echo "🎉 \033[32mProject has been successfully installed!\033[0m"
	@echo "👉 Visit: http://localhost/docs"
	@echo "🔐 Default credentials: user1@example.com / password1"
	@echo "🔐 Default credentials: user2@example.com / password2"
	@echo "🔐 Default credentials: user3@example.com / password3"
	@echo ""
	@echo "👉 Visit: http://localhost:8025 for emails"

