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
	$(DOCKER_COMPOSE) build --no-cache
	@echo "Starting up containers..."
	$(DOCKER_COMPOSE) up -d --wait
	@echo "Clearing cache..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) composer clear-cache
	@echo "Installing dependencies..."
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) composer install
	
	

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


ssh: ## bash inside the container of the API
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) -it ${PHP_SERVICE} bash


create-migration: ## Create a new migration
	@echo "Creating a new migration..."
	@read -p "Enter the name of the migration: " migration_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:migration $$migration_name

create-seeder: ## Create a new seeder
	@echo "Creating a new seeder..."
	@read -p "Enter the name of the seeder: " seeder_name; \
	$(DOCKER_COMPOSE) exec --user $(USER_ID):$(GROUP_ID) $(PHP_SERVICE) php artisan make:seeder $$seeder_name

test: ## Execute tests
	@echo "Executing tests..."
	

 
