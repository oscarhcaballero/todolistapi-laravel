# DOCTORALIA Test - TODO List API 

## Instructions for installation
Open a Ubuntu terminal in your machine and clone the repository:

    git clone https://github.com/oscarhcaballero/todolistapi-laravel

Enter the directory of the proyect:

    cd todolistapi-laravel

There is a makefile with several commands you can use.
You can see all the commands by typing... 

    make

Build the containers:
    
    make build-project

Wait until all the containers are healthy. 


Install dependencies of the project:

    make install

Check the containers:
 
    docker ps

If some unhealthy message appears after containers creation finish,
then launch migrations from scratch. Otherwise, do not make this action:

    make migrate-fresh   



Make all the tests of the project.
Tests are performed in separate application and database containers to ensure data persistence and prevent deletion or alteration.

    make test


## Instructions for operation
    
Go to the frontend at  http://localhost:8000/tasks

In this responsive frontend you can call all the API operations.


You can go to phpMyAdmin at http://localhost:8080
in order to check de databases. There are two database servers. mysql and mysql_testing

User: oscar 
Password: doctoralia_test


## Where is the API documentation (Postman)
The Json file is in the root directory of the project and it is called:
    TODO_List_API.postman_collection.json

You have the JSON file with all the Postman endpoints at your disposal. 

For more simplicity, Install Postman VSCode extension and then import the JSON file.



## API calls
Once you have imported the json into Postman VSCode extension, you will be able to access all the API endpoints

For API Token authentication, we need the Token of one of the users, for example the 'admin' user.

Run the Login endpoint and retrieve the logged-in user's token.

You must use this token for all API endpoints, including it in the endpoint's Authorization section. 
Type: Bearer Token



## Where is the database schema
You can generate the schema: 
    
    make schema

The schema will be at /database/schema/schema.sql