<?php

namespace Zipofar\Service;

class QueryParams
{
    private $preparedParams = [];
    private $unusedParams = [];

    public function setUnusedParams(array $params)
    {
        $this->unusedParams = array_merge($this->unusedParams, $params);
    }

    public function addRequestParams(array $params)
    {
        $filtered = $this->filterUnusedParams($params);
        $parsed = $this->parseParams($filtered);
        $this->preparedParams = array_merge($this->preparedParams, $parsed);
    }

    public function getStringWhere()
    {
        $stringWhere = [];

        foreach ($this->preparedParams as $key => $param) {
            if (is_array($param)) {
                $stringWhere[] = $this->buildINClause($key, $param);
            } elseif (stripos($param, '%') !== false) {
                $stringWhere[] = "{$key} LIKE :{$key}";
            } else {
                $stringWhere[] = "{$key} = :{$key}";
            }
        }

        $result = implode(' AND ', $stringWhere);
        return $result;
    }

    public function getArrayWhere()
    {
        $arrayWhere = [];

        foreach ($this->preparedParams as $key => $param) {
            if (is_array($param)) {
                $arrayWhere = array_merge($arrayWhere, $param);
            } else {
                $arrayWhere[$key] = $param;
            }
        }

        return $arrayWhere;
    }

    private function filterUnusedParams($params)
    {
        return array_filter($params, function ($item) {
            return !in_array($item, $this->unusedParams);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function buildINClause($fieldName, array $params)
    {

        $inBracketsValues = array_map(function ($item) {
            return ":{$item}";
        }, array_keys($params));

        $inBracketsValuesStr = implode(', ', $inBracketsValues);

        // $result = 'brand IN (:brand0, :brand1)'
        $result = "$fieldName IN ({$inBracketsValuesStr})";
        return $result;
    }

    /*
     * Parse multiple params like a 'param1|param2|param3'
     *
     */
    private function parseParams(array $params)
    {
        $res = [];

        foreach ($params as $key => $value) {
            if (strripos($value, '|') !== false) {
                $res = array_merge($res, $this->makeArrayParamsFromString($key, $value));
            } else {
                $res[$key] = $value;
            }
        }

        return $res;
    }

    private function makeArrayParamsFromString($key, $value)
    {
        $values = explode('|', $value);
        $i = 0;
        $res = [];

        foreach ($values as $value) {
            $res[$key.$i] = $value;
            $i += 1;
        }

        return [$key => $res];
    }
}
