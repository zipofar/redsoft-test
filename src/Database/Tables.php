<?php

return [
    'productsection' => "CREATE TABLE productsection(
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        section_id INT NOT NULL
    )",

    'section' => "CREATE TABLE section(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        lft INT NOT NULL,
        rgt INT NOT NULL
    ) ENGINE=InnoDB CHARACTER SET=utf8",

    'product' => "CREATE TABLE product(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        availability TINYINT(1) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        brand VARCHAR(190) NOT NULL
    ) ENGINE=InnoDB CHARACTER SET=utf8",

    'add_foreign_keys' => "ALTER TABLE productsection 
        ADD CONSTRAINT FK_product FOREIGN KEY (product_id) REFERENCES product(id),
        ADD CONSTRAINT FK_section FOREIGN KEY (section_id) REFERENCES section(id)",
];