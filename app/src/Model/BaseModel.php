<?php

namespace Zipofar\Model;

use Zipofar\Database\ZPdo;
use Zipofar\QueryParams;
use Zipofar\Service\QueryBuilder;

class BaseModel
{
    const LIMIT_FIELD = 'per_page';
    const OFFSET_FIELD = 'page';
    const PAGE = 1;
    const PER_PAGE = 5;

    protected $options = [
        'max_limit' => 20,
    ];

    protected $pdo;
    protected $queryBuilder;
    protected $queryParams;

    public function __construct(ZPdo $pdo, QueryBuilder $queryBuilder, QueryParams $queryParams)
    {
        $this->pdo = $pdo->get();
        $this->queryBuilder = $queryBuilder;
        $this->queryParams = $queryParams;

        $this->queryParams->addFields($this->fields);
        $this->queryParams->addFields([
            self::LIMIT_FIELD => self::PER_PAGE,
            self::OFFSET_FIELD => self::PAGE,
            ]);
        $this->queryParams->setLimitField(self::LIMIT_FIELD);
        $this->queryParams->setOffsetField(self::OFFSET_FIELD);
    }

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }
}