<?php

namespace Zipofar\Model;

use Zipofar\Db;
use Zipofar\Model\Model;

class Product extends Model
{
    private $options = [
        'limit' => 20,
    ];
    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        $sql = 'SELECT * FROM product WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data !== false ? $data : [];
    }

    /**
     * @param string $str
     * @return array
     */
    public function getBySubStrName($str)
    {
        $name = "%$str%";
        $sql = 'SELECT * FROM product WHERE name LIKE ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name]);
        $data = $stmt->fetchAll();

        return $data;
    }

    /**
     * @param string $brandName
     * @return array
     */
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

    /**
     * @param $section
     * @return array
     */
    public function getBySection($section)
    {
        $products = $this->getBySectionCol($section, 'name');
        return $products;
    }

    /**
     * @param string $sections
     * @return array
     */
    public function getBySections($sections)
    {
        $arrSections = explode('>>', $sections);

        $sql = 'select s1.id, s1.name, s1.lft, s1.rgt, COUNT(s2.id) - 1 AS level FROM section AS s1, section AS s2
                WHERE s1.lft BETWEEN s2.lft AND s2.rgt GROUP BY s1.id ORDER BY s1.lft;';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $data = $stmt->fetchAll();

        $idLastNode = $this->getLastSubSectionId($data, $arrSections);
        $products = $this->getBySectionCol($idLastNode, 'id');

        return $products;
    }

    /**
     * @param string $placeholder
     * @param string $columnName Name column at the table
     * @return array
     *
     * First sub query find id current  section
     * Second sub query find all id leafs of current section
     * Major query find all product correspond finded sections id
     */
    private function getBySectionCol($placeholder, $columnName)
    {
        $sql = "SELECT p.id, p.name, p.availability, p.price, p.brand 
                FROM productsection AS ps JOIN product AS p ON ps.product_id = p.id
                WHERE section_id IN 
                (
                    SELECT s1.id FROM section AS s1, section AS s2
                    WHERE s1.lft BETWEEN s2.lft AND s2.rgt
                       AND s1.lft + 1 = s1.rgt
                       AND s2.id IN 
                    (
                        SELECT id FROM section WHERE $columnName = ?
                    )
                )
                ORDER BY p.name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$placeholder]);
        $data = $stmt->fetchAll();

        return $data;
    }


    /**
     * @param array $origTree
     * @param array $userTree
     * @return null || finded ID
     */
    private function getLastSubSectionId(array $origTree, array $userTree)
    {

        if (strtolower($origTree[0]['name']) !== $userTree[0]) {
            return null;
        }

        $resultTree[] = $origTree[0];
        $i = 1;

        foreach ($origTree as $origNode) {
            $nameNodeOrigTree = strtolower($origNode['name']);

            if ($nameNodeOrigTree === $userTree[$i]
                && $origNode['lft'] > $resultTree[$i - 1]['lft']
                && $origNode['rgt'] < $resultTree[$i - 1]['rgt']
                && $origNode['level'] == $i
            ) {
                $resultTree[$i] = $origNode;
                $i += 1;
            }

            if (count($userTree) === $i) {
                return end($resultTree)['id'];
            }
        }

        return null;
    }
}
