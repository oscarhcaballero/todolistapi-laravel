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

Para la autenticación con API Token, necesitamos el Token de uno de los usuarios, por ejemplo el usuario 'admin'

Ejecutamos el endpoint Login y recuperamos el Token del usuario logado.
Ese Token lo tenemos que usar para todos los endpoints de la API incluyéndolo en la Auenticación Bearer Token de los endpoints.



## Where is the API call examples (Postman)



## Where is the database schema

You can generate the schema: make schema

The schema will be at /database/schema/schema.sql