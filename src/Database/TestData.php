<?php

require_once __DIR__."/../../vendor/autoload.php";

use Zipofar\Database\Db;
use Dotenv\Dotenv;

$dotenv = new Dotenv(getcwd()."/../");
$dotenv->load();
$pdo = Db::getInstance();

$sections = [
    1 => ['Food', 1, 30],
    2 => ['Vegetable', 2, 15],
    3 => ['Red', 3, 8],
    4 => ['Sour', 4, 5],
    5 => ['Sweet', 6, 7],
    6 => ['Green', 9, 14],
    7 => ['Sour', 10, 11],
    8 => ['Sweet', 12, 13],
    9 => ['Fruit', 16, 29],
    10 => ['Red', 17, 22],
    11 => ['Sour', 18, 19],
    12 => ['Sweet', 20, 21],
    13 => ['Green', 23, 28],
    14 => ['Sour', 24, 25],
    15 => ['Sweet', 26, 27],

];

$products = [
    1 => ['FoodVegRedSour', '1', '1.99', 'Russia Kolhoz'],
    2 => ['FoodVegRedSweet', '1', '1.50', 'Gruzin'],
    3 => ['FoodVegGreenSour', '1', '12.30', 'Russia Kolhoz'],
    4 => ['FoodVegGreenSweet', '1', '50.05', 'Country'],
    5 => ['FoodFruitRedSour', '1', '15.05', 'Dacha'],
    6 => ['FoodFruitRedSweet', '1', '5.00', 'Polsky'],
    7 => ['FoodFruitGreenSour', '1', '12.00', 'Turkish'],
    8 => ['FoodFruitGreenSweet', '1', '17.00', 'Petrovna'],
];

$productsection =  [
    1 => [1, 4],
    2 => [2, 5],
    3 => [3, 7],
    4 => [4, 8],
    5 => [5, 11],
    6 => [6, 12],
    7 => [7, 14],
    8 => [8, 15],
];

$stm1 = $pdo->prepare("INSERT INTO section (name, lft, rgt) VALUES (?, ?, ?)");
foreach ($sections as $item) {
    $stm1->execute($item);
}

$stm2 = $pdo->prepare("INSERT INTO product (name, availability, price, brand) VALUES (?, ?, ?, ?)");
foreach ($products as $item) {
    $stm2->execute($item);
}

$stm3 = $pdo->prepare("INSERT INTO productsection (product_id, section_id) VALUES (?, ?)");
foreach ($productsection as $item) {
    $stm3->execute($item);
}
