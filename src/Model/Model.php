<?php

namespace Zipofar\Model;

use Zipofar\Db;

class Model
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance();
    }
}
