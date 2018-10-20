<?php

namespace Zipofar\Model;

use Zipofar\Database\ZPdo;
use Zipofar\Service\QueryParams;
use Zipofar\Service\QueryBuilder;
use Respect\Validation\Exceptions\NestedValidationException;

class BaseModel
{
    private $serviceFields = [
        'page',
        'per_page',
    ];

    private $options = [
        'page' => 1,
        'per_page' => 5,
    ];

    protected $pdo;
    protected $queryBuilder;
    protected $queryParams;

    public function __construct(ZPdo $pdo, QueryBuilder $queryBuilder, QueryParams $queryParams)
    {
        $this->pdo = $pdo->getPDO();
        $this->queryBuilder = $queryBuilder;
        $this->queryParams = $queryParams;
        $this->queryParams->setUnusedParams($this->serviceFields);
    }

    protected function getServiceFields()
    {
        return $this->serviceFields;
    }

    protected function getOffset($params)
    {
        $page = isset($params['page']) ? $params['page'] : $this->options['page'];
        $limit = $this->getLimit($params);
        $offset = $limit * $page - $limit;
        return $offset;
    }

    protected function getLimit($params)
    {
        return isset($params['per_page']) ? $params['per_page'] : $this->options['per_page'];
    }

    protected function validate(array $params)
    {
        $errors = [];

        $undefinedFields = $this->getUndefinedFields($params);
        if (sizeof($undefinedFields) > 0) {
            $errors['undefined'] = array_keys($undefinedFields);
            return $errors;
        }

        $rules = $this->validationRules();
        foreach ($params as $key => $param) {
            try {
                $rules[$key]->assert($param);
            } catch(NestedValidationException $exception) {
                return $errors = $exception->getMessages();
            }
        }

        return $errors;
    }

    protected function getUndefinedFields($params)
    {
        $definedFields = array_merge($this->fields, $this->getServiceFields());
        $undefinedFields = array_filter($params, function ($key) use ($definedFields) {
            return !in_array($key, $definedFields);
        }, ARRAY_FILTER_USE_KEY);
        return $undefinedFields;
    }
}