<?php

use PHPUnit\Framework\TestCase;
use Zipofar\Misc\Helper;

class HelperTest extends TestCase
{
    private $ast = [
        0 => [
            'id' => 1,
            'name' => 'Food',
            'level' => 0,
            'children' => [
                1 => [
                    'id' => 2,
                    'name' => 'Fruit',
                    'level' => 1,
                    'children' => [
                        2 => [
                            'id' => 3,
                            'name' => 'Green',
                            'level' => 2,
                            'children' => [
                                3 => ['id' => 4, 'name' => 'Apple', 'level' => 3],
                                4 => ['id' => 5, 'name' => 'Apple2', 'level' => 3],
                            ],
                        ],
                        5 => [
                            'id' => 6,
                            'name' => 'Yellow',
                            'level' => 2,
                            'children' => [
                                6 => ['id' => 7, 'name' => 'Banana', 'level' => 3],
                                7 => ['id' => 8, 'name' => 'Limon', 'level' => 3],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function testBuildTree()
    {
        $flatTree = [
            ['id' => 1, 'name' => 'Food', 'level' => 0],
            ['id' => 2, 'name' => 'Fruit', 'level' => 1],
            ['id' => 3, 'name' => 'Green', 'level' => 2],
            ['id' => 4, 'name' => 'Apple', 'level' => 3],
            ['id' => 5, 'name' => 'Apple2', 'level' => 3],
            ['id' => 6, 'name' => 'Yellow', 'level' => 2],
            ['id' => 7, 'name' => 'Banana', 'level' => 3],
            ['id' => 8, 'name' => 'Limon', 'level' => 3],
        ];

        $expectedAst = $this->ast;

        $this->assertEquals($expectedAst, Helper::buildTreeFromFlatNested($flatTree));
    }

    public function testBuildListFromAst()
    {
        $expected = '<ul><li>id [1] Food<ul><li>id [2] Fruit<ul><li>id [3] Green<ul><li>id [4] Apple</li><li>id [5] Apple2</li></ul></li><li>id [6] Yellow<ul><li>id [7] Banana</li><li>id [8] Limon</li></ul></li></ul></li></ul></li></ul>';

        $this->assertEquals($expected, Helper::buildListFromAst($this->ast));
    }
}