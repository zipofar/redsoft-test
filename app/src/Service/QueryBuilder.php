<?php
/**
 * Created by PhpStorm.
 * User: ingprog
 * Date: 29.09.18
 * Time: 19:22
 */

namespace Zipofar\Service;

class QueryBuilder
{
    protected $fields = [];
    protected $table = '';
    protected $limit;
    protected $offset;
    protected $where;

    public function select(...$fields)
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function limit($limit, $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function build()
    {
        $fields = sizeof($this->fields) === 0 ? '*' : implode(', ', $this->fields);
        $query = "SELECT {$fields} FROM {$this->table}";

        if (isset($this->where)) {
            $query = $query." WHERE {$this->where}";
        }

        if (isset($this->limit)) {
            $query = $query." LIMIT {$this->limit}";
        }

        if (isset($this->offset)) {
            $query = $query." OFFSET {$this->offset}";
        }

        return $query;
    }
}
