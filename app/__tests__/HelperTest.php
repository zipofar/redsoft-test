<?php

use PHPUnit\Framework\TestCase;
use Zipofar\Misc\Helper;

class HelperTest extends TestCase
{
    private $ast = [
        0 => [
            'name' => 'Food',
            'level' => 0,
            'children' => [
                1 => [
                    'name' => 'Fruit',
                    'level' => 1,
                    'children' => [
                        2 => [
                            'name' => 'Green',
                            'level' => 2,
                            'children' => [
                                3 => ['name' => 'Apple', 'level' => 3],
                                4 => ['name' => 'Apple2', 'level' => 3],
                            ],
                        ],
                        5 => [
                            'name' => 'Yellow',
                            'level' => 2,
                            'children' => [
                                6 => ['name' => 'Banana', 'level' => 3],
                                7 => ['name' => 'Limon', 'level' => 3],
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
            ['name' => 'Food', 'level' => 0],
            ['name' => 'Fruit', 'level' => 1],
            ['name' => 'Green', 'level' => 2],
            ['name' => 'Apple', 'level' => 3],
            ['name' => 'Apple2', 'level' => 3],
            ['name' => 'Yellow', 'level' => 2],
            ['name' => 'Banana', 'level' => 3],
            ['name' => 'Limon', 'level' => 3],
        ];

        $exprctedAst = $this->ast;

        $this->assertEquals($exprctedAst, Helper::buildTree($flatTree));
    }

    public function testBuildListFromAst()
    {
        $expected = '<ul><li>Food<ul><li>Fruit<ul><li>Green<ul><li>Apple</li><li>Apple2</li></ul></li><li>Yellow<ul><li>Banana</li><li>Limon</li></ul></li></ul></li></ul></li></ul>';

        $this->assertEquals($expected, Helper::buildListFromAst($this->ast));
    }
}