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

2. for store daily gold rate 

Query : "CREATE TABLE gold_rates (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type BIGINT DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            date DATE NOT NULL,
            created_by BIGINT NOT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        );"

3. Sales margin Row created on the Product table
Query : ALTER TABLE `products` ADD `sale_margin` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `updated_at`;




4. New Row added for variation table
Query : ALTER TABLE `variations` ADD `minimum_selling_price` DECIMAL(22,4) NULL DEFAULT NULL AFTER `combo_variations`;

5. For Store Serial 
    Query :    CREATE TABLE `product_serials` (
                            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                            `product_id` BIGINT(20) DEFAULT NULL,
                            `variation_id` BIGINT(20) DEFAULT NULL,
                            `purchase_line_id` BIGINT(20) DEFAULT NULL,
                            `transaction_id` BIGINT(20) DEFAULT NULL,
                            `serial_number` VARCHAR(255) DEFAULT NULL,
                            `status` ENUM('available','sold','returned') DEFAULT 'available',
                            `business_id` BIGINT(20) DEFAULT NULL,
                            `location_id` BIGINT(20) DEFAULT NULL,
                            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
                            `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `serial_number` (`serial_number`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

6. For Turn On Off Serial From Product page
    Query:  ALTER TABLE `products` ADD `enable_serial` TINYINT(1) NOT NULL DEFAULT '0' AFTER `enable_stock`;

7. cost_percent and sale_margin need to be changed from int to decimals
    Query: 
    ALTER TABLE `products` DROP `cost_percent`;
    ALTER TABLE `products` DROP `sale_margin`;
    ALTER TABLE `products` ADD `cost_percent` DECIMAL(6,3) NOT NULL DEFAULT '0.000' AFTER `updated_at`;
    ALTER TABLE `products` ADD `sale_margin` DECIMAL(6,3) NOT NULL DEFAULT '0.000' AFTER `cost_percent`;