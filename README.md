

### Git
```bash
git@github.com:bellatalex/GreenWalk-API.git
```

### Docker

Lancez la commande suivante pour installer les conteneurs du projet : 
```bash
docker-compose up -d
```

### Dépendances
Installation des dépendances
```bash
docker exec greenwalk-api composer install
```

### Base de données
- Créer la base de données
    ```
    docker exec greenwalk-api php bin/console doctrine:database:create
    ```

- Créer les tables
    ```
    docker exec greenwalk-api php bin/console doctrine:schema:update --force
    ```

- Créer un jeu de données
    ```bash
    docker exec greenwalk-api php bin/console doctrine:fixtures:load
    ```
