<?php

require_once "../vendor/autoload.php";

use Zipofar\Db;
use Dotenv\Dotenv;

$dotenv = new Dotenv(getcwd()."/../");
$dotenv->load();
$pdo = Db::getInstance();

$tables = [
    'productsection' => "CREATE TABLE productsection(
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        section_id INT NOT NULL,
        FOREIGN KEY (product_id) REFERENCES product(id),
        FOREIGN KEY (section_id) REFERENCES section(id)
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
];

foreach ($tables as $tableName => $query) {
    $pdo->exec("SET foreign_key_checks = 0");
    $pdo->exec("DROP TABLE $tableName");
    $pdo->exec("SET foreign_key_checks = 1");
    $pdo->exec($query);
}
