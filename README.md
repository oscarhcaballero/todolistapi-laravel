# DOCTORALIA Test - TODO List API 

## Instructions for installation

git clone https://github.com/oscarhcaballero/todolistapi-laravel

cd todolistapi-laravel

make build-project

docker ps 
(wait until all the containers are healthy)

make install


## Instructions for operation

There is a makefile with several commands you can use.

make run-dev  (start the frontend at  http://localhost:8000/tasks)


## Where is the API documentation

Para la autenticación con API Token, necesitamos el API Token de uno de los usuarios, 
por ejemplo el usuario 1
Ejecutamos el endpoint Login y recuperamos el Api Token del usuario logado.
Ese Api Token lo tenemos que usar para todos los endpoints de la API



## Where is the API call examples (Postman)



## Where is the database schema

You can generate the schema: make schema

The schema will be at /database/schema/schema.sql