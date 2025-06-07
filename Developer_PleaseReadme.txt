1. Please Make MYSQL database with below configs

    DB_CONNECTION=mysql
    DB_HOST="localhost"
    DB_PORT="3306"
    DB_DATABASE="jewelrydb"
    DB_USERNAME="jewellryadmin"
    DB_PASSWORD="VOIVue8S[Gp3y93p"

2. Import jewelrydb.sql to Database 

3. Extract Archive.zip 

4. Once extrated there will be a folder created called archive and then cut and paste those folder inside the main folder




Please Add Database Modification Queries Below with Comments

1.  Cost percentage Row created on the Product table 
Query : ALTER TABLE `products` ADD `cost_percent` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `updated_at`;


