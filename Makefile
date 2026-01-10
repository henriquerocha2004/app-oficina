USER_ID := $(shell id -u)
GROUP_ID := $(shell id -g)

start:
	USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose up -d
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app composer install
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app cp .env.example .env
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app php artisan key:generate
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app php artisan migrate
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app php artisan db:seed
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app npm install
	docker exec -u $(USER_ID):$(GROUP_ID) oficina-app npm run dev