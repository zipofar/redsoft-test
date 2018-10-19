<?php

namespace Zipofar\Service;


class QueryParams
{
    private $fields = [];
    private $queryParams = [];
    private $limitField;
    private $offsetField;
    private $preparedParams;

    public function addFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    public function addRequestParams(array $params)
    {
        $this->queryParams = array_merge($this->queryParams, $params);
        $this->preparedParams = $this->prepareParams($this->queryParams, $this->fields);
    }

    public function setLimitField($limitField)
    {
        $this->limitField = $limitField;
    }

    public function setOffsetField($offsetField)
    {
        $this->offsetField = $offsetField;
    }

    public function getOffset()
    {
        $offset = (int) $this->preparedParams[$this->offsetField];
        $limit = $this->getLimit();
        $calcOffset = $limit * $offset - $limit;
        return $calcOffset;
    }

    public function getLimit()
    {
        $limit = (int) $this->preparedParams[$this->limitField];
        return $limit;
    }

    public function getStringWhere()
    {
        $params = $this->getWhereParams();
        $stringWhere = [];

        foreach ($params as $key => $param) {
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
        $params = $this->getWhereParams();
        $arrayWhere = [];

        foreach ($params as $key => $param) {
            if (is_array($param)) {
                $arrayWhere = array_merge($arrayWhere, $param);
            } else {
                $arrayWhere[$key] = $param;
            }
        }

        return $arrayWhere;
    }

    private function prepareParams($params, $defParams)
    {
        $filteredUndefinedParams = array_filter($params, function ($key) use ($defParams) {
            return isset($defParams[$key]);
        }, ARRAY_FILTER_USE_KEY);
        $compiledParams = array_merge($defParams, $filteredUndefinedParams);

        $filteredEmptyParams = array_filter($compiledParams, function ($item) {
            return $item !== '';
        });

        $params = $this->parseParams($filteredEmptyParams);

        return $params;
    }

    private function getWhereParams()
    {
        $filteredLimitOffsetFields = array_filter($this->preparedParams, function ($item) {
            if ($item === $this->limitField || $item === $this->offsetField) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);

        return $filteredLimitOffsetFields;
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
        $arrValues = explode('|', $value);
        $i = 0;
        $res = [];

        foreach ($arrValues as $value) {
            $res[$key.$i] = $value;
            $i += 1;
        }

        return [$key => $res];
    }
}