<?php

namespace Zipofar\Model;

use Zipofar\Database\ZPdo;
use Zipofar\Service\QueryParams;
use Zipofar\Service\QueryBuilder;
use Respect\Validation\Validator as v;

class BaseModel
{
    const LIMIT_FIELD = 'per_page';
    const OFFSET_FIELD = 'page';
    const PAGE = 1;
    const PER_PAGE = 5;

    protected $fields =  [];
    private $serviceFields = [
        'per_page',
        'page'
    ];

    protected $pdo;
    protected $queryBuilder;
    protected $queryParams;

    public function __construct(ZPdo $pdo, QueryBuilder $queryBuilder, QueryParams $queryParams)
    {
        $this->pdo = $pdo->getPDO();
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

    protected function getServiceFields()
    {
        return $this->serviceFields;
    }

}