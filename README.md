# GreenWalk-API
[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=bellatalex_GreenWalk-API)](https://sonarcloud.io/dashboard?id=bellatalex_GreenWalk-API)

### Git
```bash
git@github.com:bellatalex/GreenWalk-API.git
```

### Docker

Run the following command to install the project containers : 
```bash
docker-compose up -d
```

### Dependencies
Installation of dependencies
```bash
docker exec greenwalk-api composer install
```

### Database
- Create database
    ```
    docker exec greenwalk-api php bin/console doctrine:database:create
    ```

- Create table
    ```
    docker exec greenwalk-api php bin/console doctrine:schema:update --force
    ```

- Create a dataset
    ```bash
    docker exec greenwalk-api php bin/console doctrine:fixtures:load
    ```

