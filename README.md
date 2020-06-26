

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
composer install
```

### Base de données
- Créer la base de données
    ```
    docker-compose exec web php bin/console doctrine:database:create
    ```

- Créer les tables
    ```
    docker-compose exec web php bin/console doctrine:schema:update --force
    ```

- Créer un jeu de données
    ```bash
    docker-compose exec web php bin/console doctrine:fixtures:load
    ```
