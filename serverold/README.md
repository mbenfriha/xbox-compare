# Xbox Store Compare API 


- Slim 3.
- HTML DOM Parser.
- Auto convert currency with google finance.

## Install the Application

Run this command from the directory in which you want to launch API.

    php -S localhost:8080 -t public public/index.php
    
## Routes

- GET /games : all games, order by name
- GET /games/last : last ten games, with the lowest price in Euro
- GET /game/{id} : A unique game with all the original prices and its converted prices
- POST /game {link: "string"}: get game on xbox store with id on link
- PUT /game/{id} : update all prices of game

