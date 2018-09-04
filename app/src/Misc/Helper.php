<?php

namespace Zipofar\Misc;


class Helper
{
    /**
     * Build tree from flat array
     *
     * @param $arr
     *
     * @return array
     */
    public static function buildTree($arr)
    {
        $hasChild = function ($arr, $i, $lastId, $level) {
            return $i < $lastId && (int) $arr[$i + 1]['level'] === $level + 1;
        };

        $buildAst = function ($arr, $level = 0, $startIndex = 0) use (&$buildAst, $hasChild) {
            $lastId = count($arr) - 1;
            $res = [];

            for ($i = $startIndex; $i <= $lastId; $i++) {

                if ((int) $arr[$i]['level'] === $level) {
                    $res[$i] = $arr[$i];
                } elseif ($arr[$i]['level'] < $level) {
                    break;
                } else {
                    continue;
                }

                if ($hasChild($arr, $i, $lastId, $level)) {
                    $res[$i]['children'] = $buildAst($arr, $level + 1, $i + 1);
                }
            }

            return $res;
        };

        return $buildAst($arr);
    }

    /**
     * Build html list from AST
     *
     * @param $ast
     *
     * @return string
     */
    public static function buildListFromAst($ast)
    {
        $iter = function ($rest, $acc) use (&$iter) {
            if (empty($rest)) {
                return $acc;
            }
            $el = array_slice($rest, 0, 1)[0];
            $tail = array_slice($rest, 1);

            if (isset($el['children'])) {
                $acc .= '<li>'.$el['name'];
                $newAcc = $acc.'<ul>'.$iter($el['children'], '').'</ul></li>';
            } else {
                $newAcc = "$acc<li>{$el['name']}</li>";
            }

            return $iter($tail, $newAcc);
        };
        return '<ul>'.$iter($ast, '').'</ul>';
    }
}
