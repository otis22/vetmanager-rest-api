default_php_version:=8.3
php_version:=$(PHP_VERSION)
ifndef PHP_VERSION
	php_version:=$(default_php_version)
endif
base_dir:=$(shell basename $(CURDIR))
docker:=docker run --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version)

.PHONY: build exec install install-no-dev style static-analyze unit integration integration_one all help update

build: ## Build Docker image for the project
	docker build --build-arg VERSION=$(php_version) --tag $(base_dir):$(php_version) ./docker/

exec: ## Run interactive shell in the container
	docker run --rm -ti -v $(CURDIR):/app:rw -w /app $(base_dir):$(php_version) sh

install: ## Install all project dependencies (including dev dependencies)
	$(docker) composer install

install-no-dev: ## Install only production dependencies
	$(docker) composer install --no-dev

style: ## Check code style according to PSR-12 standard
	$(docker) composer check-style

static-analyze: ## Run static code analysis
	$(docker) composer check-static-analyze

unit: ## Run unit tests with code coverage check
	$(docker) -dzend_extension=xdebug.so -dxdebug.mode=coverage vendor/bin/phpunit --testsuite main
	$(docker) vendor/bin/php-coverage-checker build/logs/clover.xml 90

integration: ## Run integration tests
	docker run  --env-file=.env --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version) composer integration

integration_one: ## Run specific integration test (usage: make integration_one TestName)
	@if [ "$(filter-out $@,$(MAKECMDGOALS))" = "" ]; then \
		echo "Error: Please specify test name. Usage: make integration_one TestName"; \
		exit 1; \
	fi
	docker run  --env-file=.env --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version) vendor/bin/phpunit --testsuite integration --filter $(filter-out $@,$(MAKECMDGOALS))

all: build install style static-analyze unit ## Run all checks and tests

update: ## Update project dependencies
	$(docker) composer update

help: ## Show list of available commands
	@echo "Available commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ""

%:
	@: