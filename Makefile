#loading Fixtures
make fixtures load:
	php bin/console doctrine:fixtures:load

#create DB
make database create:
	php bin/console doctrine:database:create

#to migrate Users Entity to users DB
make migration:
	php bin/console make:migration

#to launch your migration file users
make migrate:
	php bin/console doctrine:migrations:migrate