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

		return $data;
	}

	public function getByBrand($brandName)
	{
		$arrBrand = explode('+', $brandName);
		$placeHolder = implode(', ', array_fill(0, count($arrBrand), '?'));

		$sql = "SELECT * FROM product WHERE brand IN ($placeHolder)";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($arrBrand);
		$data = $stmt->fetchAll();

		return $data;
	}

	public function getBySection($section)
	{
		// First sub query find id current  section
		// Second sub query find all id leafs of current section
		// Major query find all product correspond finded sections id

		$sql = 'SELECT p.id, p.name, p.availability, p.price, p.brand 
				FROM productsection AS ps JOIN product AS p ON ps.product_id = p.id
				WHERE section_id IN 
				(
					SELECT s1.id FROM section AS s1, section AS s2
                    WHERE s1.lft BETWEEN s2.lft AND s2.rgt
                       AND s1.lft + 1 = s1.rgt
                       AND s2.id IN 
                    (
                    	SELECT id FROM section WHERE name = ?
                    )
                )
                ORDER BY p.name';

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$section]);
		$data = $stmt->fetchAll();

		return $data;
	}

	public function getBySections($sections)
	{
		$arrSections = explode('>>', $sections);

		$sql = 'SELECT DISTINCT s2.id, s2.name, s2.lft, s2.rgt FROM section AS s1, section AS s2
  				WHERE s1.lft BETWEEN s2.lft AND s2.rgt 
  				AND s1.name = ? ORDER BY s2.lft';

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$arrSections[count($arrSections) - 1]]);
		$data = $stmt->fetchAll();
		$this->getLastSubSectionIds($data, $arrSections);
		return $data;
	}

	private function getLastSubSectionIds($data, $needle)
	{
		function tree($data, $acc) {
			if (count($data) === 0) {
				return $acc;
			}
			$firstEl = array_shift($data);

			if (count($acc) === 0) {
				$acc[] = $firstEl;
			} elseif ($firstEl['lft'] > $acc[count($acc) - 1]['lft'] & $firstEl['lft'] > $acc[count($acc) - 1]['rgt']) {

			}
			$acc = tree($data, $acc);
			return $acc;
		}

		$res = tree($data, []);
		var_dump($res);
	}
}