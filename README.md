# DOCTORALIA Test - TODO List API 

## Instructions for installation
Open a terminal in your machine and clone te repository:

    git clone https://github.com/oscarhcaballero/todolistapi-laravel

Enter the directory of the proyect:

    cd todolistapi-laravel

There is a makefile with several commands you can use.
You can see all the commands by typing... 

    make

Build the containers:
    
    make build-project

Wait until all the containers are healthy:

    docker ps 

Then, install dependencies of the project:

    make install

Make all the tests of the project.
Tests are performed in separate application and database containers to ensure data persistence and prevent deletion or alteration.

    make test


## Instructions for operation
Run the frontend server

    make run-dev 
    
Go to the frontend at  http://localhost:8000/tasks

In this responsive frontend you can call all the API operations.



## Where is the API documentation (Postman)
You have the JSON file with all the Postman endpoints at your disposal. Just import it into Postman and start using them.

The Json file is in the root directory of the project and it is called:
    TODO List API.postman_collection.json



## API calls
Once you have imported the json into postman, you will be able to access all the API endpoints

For API Token authentication, we need the Token of one of the users, for example the 'admin' user.

Run the Login endpoint and retrieve the logged-in user's token.
You must use this token for all API endpoints, including it in the endpoint's Bearer Token Authentication.



## Where is the database schema
You can generate the schema: make schema

The schema will be at /database/schema/schema.sql