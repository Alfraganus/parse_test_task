init: down up install


up:
	docker-compose up -d

down:
	docker-compose down -t0

restart:
	docker-compose restart

composer:
	docker-composer

install:
	docker-compose exec php composer install

