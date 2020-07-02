# GreenWalk-API

This api is used to be the back end of the application : https://github.com/Alexg78bis/GreenWalk.  
The technology, which is used in this api is Symfony 4.  

The purpose of this project is to be able to organise Clean Walk efficiently. Clean Walk is basically walking in a delimited area and taking out trash off the street. This application is really useful because our city and our countryside are being polluted due to plastic waste like plastic bag or even straw.

Please help us to develop our project !  

Go see our Trello to see what will be added next or even do it yourself by contributing : https://trello.com/b/gY8Uhjmg/greenwalk  

You can even be part of this project by coming to our discord and talk directly to us we would love that : https://discord.gg/EyFfywY  


Thanks in advance :heart:  

[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=bellatalex_GreenWalk-API)](https://sonarcloud.io/dashboard?id=bellatalex_GreenWalk-API)

  
  

### To install this API you need to do the following :
Git :
- ```bash
    git@github.com:bellatalex/GreenWalk-API.git
    ```

Docker :
- Run the following command to install the project containers : 
    ```bash
    docker-compose up -d
    ```

Dependencies :
- Installation of dependencies
    ```bash
    docker exec greenwalk-api composer install
    ```

Database :
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

