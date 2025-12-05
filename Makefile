default_php_version:=8.3
php_version:=$(PHP_VERSION)
ifndef PHP_VERSION
	php_version:=$(default_php_version)
endif
base_dir:=$(shell basename $(CURDIR))

DOCKER_BIN:=$(shell command -v docker 2>/dev/null)
DOCKER_AVAILABLE:=$(shell if [ -n "$(DOCKER_BIN)" ]; then docker info >/dev/null 2>&1 && echo 1 || echo 0; else echo 0; fi)
USE_DOCKER?=$(DOCKER_AVAILABLE)

ifeq ($(USE_DOCKER),1)
docker:=docker run --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version)
endif

.PHONY: build exec install install-no-dev style static-analyze unit integration integration_one all help update

build: ## Build Docker image for the project
ifeq ($(USE_DOCKER),1)
	docker build --build-arg VERSION=$(php_version) --tag $(base_dir):$(php_version) ./docker/
else
	@echo "Docker недоступен или отключён (USE_DOCKER=$(USE_DOCKER)). Пропускаю сборку образа."
endif

exec: ## Run interactive shell in the container
ifeq ($(USE_DOCKER),1)
	docker run --rm -ti -v $(CURDIR):/app:rw -w /app $(base_dir):$(php_version) sh
else
	@echo "Docker недоступен, поэтому shell в контейнере открыть нельзя."
endif

install: ## Install all project dependencies (including dev dependencies)
ifeq ($(USE_DOCKER),1)
	$(docker) composer install
else
	composer install
endif

install-no-dev: ## Install only production dependencies
ifeq ($(USE_DOCKER),1)
	$(docker) composer install --no-dev
else
	composer install --no-dev
endif

style: ## Check code style according to PSR-12 standard
ifeq ($(USE_DOCKER),1)
	$(docker) composer check-style
else
	composer check-style
endif

static-analyze: ## Run static code analysis
ifeq ($(USE_DOCKER),1)
	$(docker) composer check-static-analyze
else
	composer check-static-analyze
endif

unit: ## Run unit tests with code coverage check
ifeq ($(USE_DOCKER),1)
	$(docker) -dzend_extension=xdebug.so -dxdebug.mode=coverage vendor/bin/phpunit --testsuite main
	$(docker) vendor/bin/php-coverage-checker build/logs/clover.xml 90
else
	XDEBUG_MODE=coverage vendor/bin/phpunit --testsuite main
	vendor/bin/php-coverage-checker build/logs/clover.xml 90
endif

integration: ## Run integration tests
ifeq ($(USE_DOCKER),1)
	docker run  --env-file=.env --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version) composer integration
else
	composer integration
endif

integration_one: ## Run specific integration test (usage: make integration_one TestName)
	@if [ "$(filter-out $@,$(MAKECMDGOALS))" = "" ]; then \
		echo "Error: Please specify test name. Usage: make integration_one TestName"; \
		exit 1; \
	fi
ifeq ($(USE_DOCKER),1)
	docker run  --env-file=.env --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version) vendor/bin/phpunit --testsuite integration --filter $(filter-out $@,$(MAKECMDGOALS))
else
	vendor/bin/phpunit --testsuite integration --filter $(filter-out $@,$(MAKECMDGOALS))
endif

all: build install style static-analyze unit ## Run all checks and tests

update: ## Update project dependencies
ifeq ($(USE_DOCKER),1)
	$(docker) composer update
else
	composer update
endif

help: ## Show list of available commands
	@echo "Available commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo ""

%:
	@: