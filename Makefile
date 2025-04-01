# Variables
DOCKER_COMPOSE = docker-compose
PHP_SERVICE = api
PHP_SERVICE_TESTING = api_testing
USER_ID = $(shell id -u)
GROUP_ID = $(shell id -g)

include .env
export $(shell sed 's/=.*//' .env)


# Targets
help: ## show all available targets
	@echo 
	@echo "TODO List API Makefile"
	@echo "Óscar H Caballero :: Senior PHP developer"
	@echo 
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@echo '--------------------------------------------------------------------------------------------'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | sed 's/Makefile://g' | column -t -c 2 -s ':#'
	@echo 

build-project: ## Build project images & start up containers
	@echo "Building project images..."
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache --build-arg UID=$(USER_ID) --build-arg GID=$(GROUP_ID)
	@echo "Starting up containers..."
	$(DOCKER_COMPOSE) up -d --wait
	@echo "Clearing cache..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) composer clear-cache


install: ## Install dependencies
	@echo "Installing PHPunit in testing environment..."
	$(DOCKER_COMPOSE) exec --user root $(PHP_SERVICE_TESTING) composer require --dev phpunit/phpunit
	@echo "Installing dependencies..."
	$(DOCKER_COMPOSE) exec --user root $(PHP_SERVICE) npm install
	$(DOCKER_COMPOSE) exec --user root $(PHP_SERVICE_TESTING) npm install
	@echo "Building assets..."
	$(DOCKER_COMPOSE) exec --user root -d $(PHP_SERVICE) npm run dev



migrate: ## Run migrations for the development & testing environments 
	@echo "Running migrations for the development environment..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan migrate --seed
	@echo "Running migrations for the testing environment..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE_TESTING) php artisan migrate --seed




server: ## Run the artisan server
	@echo "Running the artisan server..."
	$(DOCKER_COMPOSE) exec --user root $(PHP_SERVICE) php artisan serve
	
run-dev: ## Run the application in development mode
	@echo "Running the application in development mode..."
	$(DOCKER_COMPOSE) exec --user root $(PHP_SERVICE) npm run dev

	




migrate-fresh: ## Creates the database, run all migrations from scratch, and seed the tables
	@echo "Creates the database, run all migrations from scratch, and seed the tables..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan migrate:fresh --seed

migrate-fresh-testing: ## Fresh migrations for the testing environment, and seed the tables
	@echo "Creates the testing database, run all migrations from scratch, and seed the tables......"
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE_TESTING) php artisan migrate:fresh --seed


schema: ## SQL script for creating the schema
	@echo "SQL script for creating the schema..."
	docker exec mysql_db mysqldump -u root -p$(DB_ROOT_PASSWORD) --no-data $(DB_DATABASE) > schema.sql  
	mkdir -p ./database/schema
	mv schema.sql ./database/schema/schema.sql

clear-cache: ## Clean the caches of the application
	@echo "Cleaning the caches of the application..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan config:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan cache:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan view:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:clear


route-list: ## Show the list of routes
	@echo "Showing the list of routes..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan config:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan cache:clear
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:cache
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan route:list


ssh: ## bash inside the container of the API
	@echo "Accessing the api container..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) -it ${PHP_SERVICE} bash
ssh-testing: ## bash inside the testing container of the API
	@echo "Accessing the testing api container..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) -it ${PHP_SERVICE_TESTING} bash


create-migration: ## Create a new migration
	@echo "Creating a new migration..."
	@read -p "Enter the name of the migration: " migration_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:migration $$migration_name

create-seeder: ## Create a new seeder
	@echo "Creating a new seeder..."
	@read -p "Enter the name of the seeder: " seeder_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:seeder $$seeder_name

create-api-resource: ## Create a new API resource (model, migration, controller)
	@echo "Creating a new API resource..."
	@read -p "Enter the name of the resource: " resource_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:model $$resource_name -m --resource
	
create-model: ## Create a new model
	@echo "Creating a new model..."
	@read -p "Enter the name of the model: " model_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:model $$model_name

create-controller: ## Create a new controller
	@echo "Creating a new controller..."
	@read -p "Enter the name of the controller: " controller_name; \
	read -p "Enter the name of the model: " model_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:controller $$controller_name --resource --model=$$model_name



test: ## Execute all tests in a refreshed database
	@echo "Executing tests. Be patient, it may take a while..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE_TESTING) php artisan test



