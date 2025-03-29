# Variables
DOCKER_COMPOSE = docker-compose
PHP_SERVICE = api
USER_ID = $(shell id -u)
GROUP_ID = $(shell id -g)

include .env
export $(shell sed 's/=.*//' .env)


# Targets
help: ## show all available targets
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@echo '--------------------------------------------'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
	@echo '--------------------------------------------'

build-project: ## Build project images & start up containers
	@echo "Building project images..."
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache --build-arg UID=$(USER_ID) --build-arg GID=$(GROUP_ID)
	@echo "Starting up containers..."
	$(DOCKER_COMPOSE) up -d --wait
	@echo "Clearing cache..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) composer clear-cache
	
	

migrate: ## Update the database schema
	@echo "Updating the database schema..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan migrate  

migrate-fresh: ## Create the database and run all migrations from scratch. Warning, this will delete all data in the database.
	@echo "Creating the database and running all migrations from scratch..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan migrate:fresh  

schema: ## SQL script for creating the schema
	@echo "SQL script for creating the schema..."
	docker exec mysql_db mysqldump -u root -p$(DB_ROOT_PASSWORD) --no-data $(DB_DATABASE) > schema.sql  
	mkdir -p ./database/schema
	mv schema.sql ./database/schema/schema.sql


clear-cache: ## Clean the caches of the application
	@echo "Cleaning the caches of the application..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan cache:clear

route-list: ## Show the list of routes
	@echo "Showing the list of routes..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan config:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan cache:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:cache
	$(DOCKER_COMPOSE) restart $(PHP_SERVICE)
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:list


ssh: ## bash inside the container of the API
	@echo "Accessing the api container..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) -it ${PHP_SERVICE} bash


create-migration: ## Create a new migration
	@echo "Creating a new migration..."
	@read -p "Enter the name of the migration: " migration_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:migration $$migration_name

create-seeder: ## Create a new seeder
	@echo "Creating a new seeder..."
	@read -p "Enter the name of the seeder: " seeder_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:seeder $$seeder_name


create-model: ## Create a new model
	@echo "Creating a new model..."
	@read -p "Enter the name of the model: " model_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:model $$model_name


create-controller: ## Create a new controller
	@echo "Creating a new controller..."
	@read -p "Enter the name of the controller: " controller_name; \
	read -p "Enter the name of the model: " model_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:controller $$controller_name --resource --model=$$model_name


test: ## Execute tests like this, make test --filter=TestName
	@echo "Executing tests..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan test $(filter-out $@,$(MAKECMDGOALS))

	

 
