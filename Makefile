#loading Fixtures
fixtures load:
	@php bin/console doctrine:fixtures:load

#create DB
database create:
	@php bin/console doctrine:database:create

#to migrate Users Entity to users DB
migration:
	@php bin/console make:migration

#to launch your migration file users
migrate:
	@php bin/console doctrine:migrations:migrate

build:
	@docker compose up --build

rebuild:
	@docker compose down && docker compose up -d --build

start:
	@docker compose up -d

stop:
	@docker compose down

restart:
	@docker compose down && docker compose up -d

php:
	@docker compose exec php bash

logs_db:
	@docker compose logs db

logs_php:
	@docker compose logs php

phpunit:
	@docker compose exec php php vendor/bin/phpunit

composer_install:
	@docker compose exec php composer install

composer_update:
	@docker compose exec php composer update