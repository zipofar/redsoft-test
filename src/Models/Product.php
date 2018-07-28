<?php

namespace Zipofar\Model;

use Zipofar\Db;
use Zipofar\Model\Model;

class Product extends Model
{
	public function getById($id)
	{
		$sql = 'SELECT * FROM product WHERE id = ?';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$id]);
		$data = $stmt->fetchAll()[0];

		return $data;
	}

	public function getBySubStrName($str)
	{
		$name = "%$str%";
		$sql = 'SELECT * FROM product WHERE name LIKE ?';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$name]);
		$data = $stmt->fetchAll();
		var_dump($data);
		return $data;
	}
}