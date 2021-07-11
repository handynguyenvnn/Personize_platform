all:

deploy:
	@cp .env-example .env
	@cp src/.env.example src/.env
	@sudo chmod -R 777 logs
	@echo "===== docker-compose up -d --build  ====="
	@docker-compose up -d --build
	@docker-compose exec showroom chown -R www-data:www-data ./
	@docker-compose exec showroom chmod -R g+w ./storage
	@echo "===== composer install  ====="
	@docker-compose exec showroom composer install
	@echo "===== key, jwt, storage, migration, database seed ====="
	@docker-compose exec showroom php artisan key:generate
	@docker-compose exec showroom php artisan jwt:secret
	@docker-compose exec showroom php artisan storage:link
	@docker-compose exec showroom php artisan migrate
	@docker-compose exec showroom php artisan db:seed
	@docker-compose exec showroom php artisan config:cache

