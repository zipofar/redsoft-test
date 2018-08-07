<?php

return [

    'section' => [
        1 => ['name' => 'Food', 'lft' => 1, 'rgt' => 30],
        2 => ['name' => 'Vegetable', 'lft' => 2, 'rgt' => 15],
        3 => ['name' => 'Red', 'lft' => 3, 'rgt' => 8],
        4 => ['name' => 'Sour', 'lft' => 4, 'rgt' => 5],
        5 => ['name' => 'Sweet', 'lft' => 6, 'rgt' => 7],
        6 => ['name' => 'Green', 'lft' => 9, 'rgt' => 14],
        7 => ['name' => 'Sour', 'lft' => 10, 'rgt' => 11],
        8 => ['name' => 'Sweet', 'lft' => 12, 'rgt' => 13],
        9 => ['name' => 'Fruit', 'lft' => 16, 'rgt' => 29],
        10 => ['name' => 'Red', 'lft' => 17, 'rgt' => 22],
        11 => ['name' => 'Sour', 'lft' => 18, 'rgt' => 19],
        12 => ['name' => 'Sweet', 'lft' => 20, 'rgt' => 21],
        13 => ['name' => 'Green', 'lft' => 23, 'rgt' => 28],
        14 => ['name' => 'Sour', 'lft' => 24, 'rgt' => 25],
        15 => ['name' => 'Sweet', 'lft' => 26, 'rgt' => 27],
    ],

    'product' => [
        1 => ['name' => 'FoodVegRedSour', 'availability' => '1', 'price' => '1.99', 'brand' => 'Russia Kolhoz'],
        2 => ['name' => 'FoodVegRedSweet', 'availability' => '1', 'price' => '1.50', 'brand' => 'Gruzin'],
        3 => ['name' => 'FoodVegGreenSour', 'availability' => '1', 'price' => '12.30', 'brand' => 'Russia Kolhoz'],
        4 => ['name' => 'FoodVegGreenSweet', 'availability' => '1', 'price' => '50.05', 'brand' => 'Country'],
        5 => ['name' => 'FoodFruitRedSour', 'availability' => '1', 'price' => '15.05', 'brand' => 'Dacha'],
        6 => ['name' => 'FoodFruitRedSweet', 'availability' => '1', 'price' => '5.00', 'brand' => 'Polsky'],
        7 => ['name' => 'FoodFruitGreenSour', 'availability' => '1', 'price' => '12.00', 'brand' => 'Turkish'],
        8 => ['name' => 'FoodFruitGreenSweet', 'availability' => '1', 'price' => '17.00', 'brand' => 'Petrovna'],
    ],

    'productsection' =>  [
        1 => ['product_id' => 1, 'section_id' => 4],
        2 => ['product_id' => 2, 'section_id' => 5],
        3 => ['product_id' => 3, 'section_id' => 7],
        4 => ['product_id' => 4, 'section_id' => 8],
        5 => ['product_id' => 5, 'section_id' => 11],
        6 => ['product_id' => 6, 'section_id' => 12],
        7 => ['product_id' => 7, 'section_id' => 14],
        8 => ['product_id' => 8, 'section_id' => 15],
    ]

];