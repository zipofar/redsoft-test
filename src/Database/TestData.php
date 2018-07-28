<?php

require_once "../vendor/autoload.php";

use Zipofar\Db;
use Dotenv\Dotenv;

$dotenv = new Dotenv(getcwd()."/../");
$dotenv->load();
$pdo = Db::getInstance();

$n = null;

$sections = [
	1 => ['Electronics', $n],
	2 => ['Tv & Video', 1],
	3 => ['DTV', 2],
	4 => ['Converters', 2],
	5 => ['Projectors', 2],
	6 => ['DLP', 5],
	7 => ['LCD', 5],
	8 => ['Home & Audio', 1],
	9 => ['Cell Phones', 1],
	10 => ['Headphones', 1],
	11 => ['Computers', $n],
	12 => ['Monitors', 11],
	13 => ['LCD', 12],
	14 => ['LED', 12],
	15 => ['OLED', 12],
];

$products = [
	1 => ['Monitor LCD', '1', '99.99', 'Benq'],
	2 => ['Monitor LED', '1', '100.50', 'Samsung'],
	3 => ['Monitor OLED', '1', '120.30', 'Sony'],
	4 => ['Cell phone 1', '1', '650.05', 'Apple'],
	5 => ['Cell phone 2', '1', '650.05', 'Motorola'],
	6 => ['Cinema 4000', '1', '450.00', 'Epson'],
	7 => ['Cinema 1000', '1', '150.00', 'Epson'],
	8 => ['Cinema 2000', '1', '170.00', 'ViewSonic'],
	9 => ['Converter 1', '1', '200.00', 'ViewSonic'],
	10 => ['Converter 2', '1', '300.00', 'Samsung'],
];

$productsection =  [
	1 => [1, 12],
	2 => [2, 12],
	3 => [3, 12],
	4 => [4, 9],
	5 => [5, 9],
	6 => [6, 6],
	7 => [7, 7],
	8 => [8, 7],
	9 => [9, 4],
	10 => [10, 4],
];

$stm1 = $pdo->prepare("INSERT INTO section (name, parent_id) VALUES (?, ?)");
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