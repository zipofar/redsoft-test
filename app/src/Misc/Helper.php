<?php

namespace Zipofar\Function;


class Helper
{
    public static function ($arr, $level = 0, $startIndex = 0)
    {
        $lastId = count($arr) - 1;
        $res = [];

        for($i = $startIndex; $i <= $lastId; $i++) {

            if ($arr[$i]['level'] === $level) {
                $res[$i] = $arr[$i];
            } elseif ($arr[$i]['level'] < $level) {
                break;
            } else {
                continue;
            }

            if ($i < $lastId && $arr[$i + 1]['level'] === $level + 1) {
                $res[$i]['child'] = buildTree($arr, $level + 1, $i + 1);
            }
        }

        return $res;
    }

}
