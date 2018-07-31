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

		$sql = 'select s1.id, s1.name, s1.lft, s1.rgt, COUNT(s2.id) - 1 AS level FROM section AS s1, section AS s2
				WHERE s1.lft BETWEEN s2.lft AND s2.rgt GROUP BY s1.id ORDER BY s1.lft;';

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([]);
		$data = $stmt->fetchAll();
		$data = $this->getLastSubSectionIds($data, $arrSections);
		return $data;
	}

	private function getLastSubSectionIds($data, $searchHierarhy)
	{

		if (strtolower($data[0]['name']) !== $searchHierarhy[0]) {
			return null;
		}

		$tree[] = $data[0];
		$i = 1;

		foreach ($data as $item) {

			$dataName = strtolower($item['name']);

			if ($dataName === $searchHierarhy[$i]
				&& $item['lft'] > $tree[$i - 1]['lft']
				&& $item['rgt'] < $tree[$i - 1]['rgt']
				&& $item['level'] == $i
			) {
				$tree[$i] = $item;
				$i += 1;
			}

			if (count($searchHierarhy) === $i) {
				return $tree;
			}

		}

		return null;
	}
}