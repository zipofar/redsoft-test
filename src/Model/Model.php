<?php
/**
 * Created by PhpStorm.
 * User: ingprog
 * Date: 28.07.18
 * Time: 0:09
 */

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
