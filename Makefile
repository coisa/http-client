SHELL := sh
.DEFAULT_GOAL := test

export UID=`id -u`
export GID=`id -g`

vendor: build
	docker-compose run --rm --user $(UID):$(GID) php74 install

.PHONY: install
install: vendor

.PHONY: build
build:
	docker-compose build

.PHONY: cs-fix
cs-fix: install
	docker-compose run --rm --user $(UID):$(GID) php74 cs-fix

.PHONY: tests
tests: cs-fix
	docker-compose run --rm --user $(UID):$(GID) php74 test
	docker-compose run --rm php80 test
	docker-compose run --rm php81 test
