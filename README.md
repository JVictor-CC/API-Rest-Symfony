# Recipe REST API

This is a RESTful API built with Symfony that allows users to manage and share their recipes.

## Features

The API provides the following features:

   * **User authentication:** Users can create an account, log in, and log out of the system.
   * **Recipe management:** Users can create, read, update, and delete their own recipes.
   * **Recipe sharing:** Users can share their recipes with other users.

## Getting started

**Prerequisites**

To run this API, you need to have the following software installed:

    PHP 7.2 or later
    Composer
    MySQL
    Symfony

**Installation**

Clone the repository:

> bash

    git clone https://github.com/your-username/recipe-api.git

**Install the dependencies:**

> bash

    cd recipe-api
    composer install

**Set up the database:**

> bash

    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate

**Start the development server:**

> bash

    symfony server:start

The API should now be accessible at http://localhost:8000.

## Usage

**Authentication**

To use the API, you need to authenticate with a valid user account. You can create a new account by sending a `POST` request to `http://localhost:8000/api/v1/user/register` with the following JSON payload:

> json
```json
  {
    "email": "teste@email.com",
    "password": "teste123",
    "username": "usertest"
  }
```
To log in, send a `POST` request to `http://localhost:8000/api/v1/user/login` with your email and password. This will create a session that will be used to authenticate your requests.

>json
```json
{
  "email": "teste@email.com",
  "password": "teste123"
}
```

To log out, send a `POST` request to `http://localhost:8000/api/v1/user/logout` and your session will be ended.

To reset your password, send a `POST` request to `http://localhost:8001/api/v1/user/reset_password` with the following JSON payload:
> json
```json
{
  "old_password":"teste123",
  "new_password":"newteste123"
}
```

**Recipes**

To create a new recipe, send a POST request to `http://localhost:8001/api/v1/recipes/new_recipe` with the following JSON payload:

> json example
```json
{
  "recipe_name": "Bife Wellington2",
  "description": "O Bife Wellington é um prato que muitas pessoas sonham comer, e ele tem uma história incrível. Muito maravilhoso! A massa folhada traz crocância, que contrasta com a maciez do filé. A carne é temperada, selada e besuntada com mostarda de Dijon, depois é envolvida numa pasta cremosa de cogumelo e, por fim, recoberta com massa folhada. Sem dúvida, este é um prato especial, para momentos inesquecíveis.",
  "ingredients": {
    "ovo": "1",
    "cogumelo": "200g",
    "presunto": "500g",
    "massa folhada": "500g",
    "Filé Mignon": "1300g",
    "cebola": "1 colher de sopa"
  },
  "instructions": {
    "1": "Retirar cordão e aparas do Filé mignon  retirar o coração, e dois palmos da ponta do mignon, deixar só o lombo bem limpo. Secar bem com papel toalha. ",
    "2": "Temperar o mignom com sal, pimenta do reino e besuntar todo com mostarda.",
    "3": "Em uma forma retangular ou frigideira grande levar ao fogo médio e selar a carne, sem mexer muito e de todos os lados no fogo bem alto.",
    "4": "Reservar a carne em cima de uma grelha e deixar escorrer bem (pode usar uma grelha do forno ou de uma churrasqueira)",
    "5": "Em uma frigideira anti aderente colocar a manteiga,  cebola para refogar, acrescentar os cogumelos picados, temperar com sal e pimenta. Rapidamente, por cerca de 3min e desligar o fogo.",
    "6": "Esticar plástico filme na mesa. Colocar as fatias de presunto de Parma enfileiradas como na foto, colocar a douxeles de cogumelos sobre o presunto.",
    "7": "Colocar o file mignon sobre os cogumelos e enrolar bem firme, levar a geladeira por 1hora.",
    "8": "Retirar a massa folhada da embalagem e esticar na mesa sobre um pano limpo.",
    "9": "Retirar o plástico filme do file mignon e colocar a carne no meio da massa folhada, enrolar a massa bem firme.",
    "10": "Levar para uma forma retangular e bater o ovo com um garfo, passar sobre  massa o ovo batido usando um pincel.",
    "11": "Levar ao forno pré aquecido a 200 graus e deixar 45 minutos.",
    "12": "Retirar do forno e cortar em fatias. Regar azeite e salpicar cebolinha verde"
  },
  "preparation_time": null, //optional
  "meal_type": null,        //optional
  "cuisine_type": null      //optional
}
```

To update a recipe created by you, send a `PUT` request to `http://localhost:8001/api/v1/recipes/update_recipe/{id}` with the JSON payload with properties you want to change.


***I still have to improve the listing methods.***

To retrieve a list of recipes, send a GET request to `http://localhost:8000/api/v1/recipes/list?limit=5` and chose the limit (default limit = 10).

To retrieve a specific recipe, send a GET request to `http://localhost:8000/api/v1/recipes/list/{id}` where {id} is the ID of the recipe. ***(ToDo)***

To delete a recipe you created, send a DELETE request to /api/recipes/{id}. ***(ToDo)***


This project was created by João Victor. If you have any questions or comments, please contact me.
