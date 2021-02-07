default_php_version:=7.4
php_version:=$(PHP_VERSION)
ifndef PHP_VERSION
	php_version:=$(default_php_version)
endif
base_dir:=$(shell basename $(CURDIR))
docker:=docker run --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version)

build:
	docker build --build-arg VERSION=$(php_version) --tag $(base_dir):$(php_version) ./docker/

exec:
	docker run --rm -ti -v $(CURDIR):/app:rw -w /app $(base_dir):$(php_version) sh

install:
	$(docker) composer install

install-no-dev:
	$(docker) composer install --no-dev

style:
	$(docker) composer check-style

static-analyze:
	$(docker) composer check-static-analyze

unit:
	$(docker) -dzend_extension=xdebug.so -dxdebug.mode=coverage vendor/bin/phpunit --testsuite main
	$(docker) vendor/bin/php-coverage-checker build/logs/clover.xml 90

integration:
	docker run  --env-file=.env --rm -v $(CURDIR):/app -w /app $(base_dir):$(php_version) composer integration

all: build install style static-analyze unit

.PHONY: build