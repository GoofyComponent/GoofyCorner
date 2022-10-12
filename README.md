# GoofyCorner

Ride wife, life good. Wife fight back! Kill wife! Wife gone. Think about wife. Regret…

## Installation

1. docker compose up -d
2. cd src
3. Verifier si vous avez le fichier env. Sinon le créer et recuperer le code sur Discord : [https://discord.com/channels/902096757050187816/1027942065423786054/1027949623026925641](https://discord.com/channels/902096757050187816/1027942065423786054/1027949623026925641)
4. composer install
5. npm install
6. Vider son dossier migration sauf le gitignore et faire une migration:

   - symfony console make:migration
   - symfony console doctrine:migrations:migrate
   - SI ERREUR
     - vider le dossier migration si pas vide
     - symfony console doctrine:database:drop --force
     - symfony console doctrine:database:create
     - symfony console make:migration
     - symfony console doctrine:migrations:migrate
     - Appeler à l'aide à Adrien si la réponse n'est pas sur Google

7. Load les fixtures : symfony console doctrine:fixtures:load
8. symfony serve:start -d
9. npm run watch
10. Aller localhost:8000 pour afficher votre site
