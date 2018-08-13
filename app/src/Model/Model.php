<?php

namespace Zipofar\Model;

use Zipofar\Database\Db;

class Model
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance();
    }
}
