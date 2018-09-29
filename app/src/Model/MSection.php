<?php

namespace Zipofar\Model;

use Zipofar\Database\ZPdo;

class MProduct
{
    /**
     * @var array Options array
     */
    protected $options = [
        'limit' => 20,
    ];

    private $pdo;

    public function __construct(ZPdo $pdo)
    {
        $this->pdo = $pdo->get();
    }

    /**
     * Set options for model Product
     *
     * @param $options Array of options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getHierarchy()
    {
        $sql = 'SELECT s1.id, s1.name, COUNT(s2.id) - 1 AS level FROM section AS s1, section AS s2
                WHERE s1.lft BETWEEN s2.lft AND s2.rgt GROUP BY s1.id ORDER BY s1.lft;';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $hierarchy = $stmt->fetchAll();

        return $hierarchy;
    }

    /**
     * @param string  $section Name of the section
     * @param integer $offset For pagination
     *
     * @return array
     */
    public function getBySection($section, $offset = 0)
    {
        $products = $this->getBySectionCol($section, 'name', $offset);

        return $products;
    }

    /**
     * @param string $sections
     * @param integer $offset For pagination
     *
     * @return array
     */
    public function getBySections($sections, $offset = 0)
    {
        $arrSections = explode('>>', $sections);

        // Get Section Hierarchy
        $sql = 'SELECT s1.id, s1.name, s1.lft, s1.rgt, COUNT(s2.id) - 1 AS level FROM section AS s1, section AS s2
                WHERE s1.lft BETWEEN s2.lft AND s2.rgt GROUP BY s1.id ORDER BY s1.lft;';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $hierarchy = $stmt->fetchAll();
        $idLastNode = $this->getLastSubSectionId($hierarchy, $arrSections);
        $products = $this->getBySectionCol($idLastNode, 'id', $offset);

        return $products;
    }

    /**
     * Find all products correspond 
     *
     * First sub query find id current section
     * Second sub query find all id leafs of current section
     * Major query find all product correspond finded sections id
     *
     * @param string $placeholder
     * @param string $columnName Name column at the table
     * @return array
     *
     */
    private function getBySectionCol($placeholder, $columnName, $offset = 0)
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
                        SELECT id FROM section WHERE $columnName = :placeholder
                    )
                )
                ORDER BY p.name LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':placeholder', $placeholder);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $this->options['limit'], \PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll();

        return $data;
    }


    /**
     * Root user tree must be equal root original tree
     * Get id last section
     * @param array $origTree
     * @param array $userTree
     *
     * @return null or finded ID
     */
    protected function getLastSubSectionId(array $origTree, array $userTree)
    {

        if (strtolower($origTree[0]['name']) !== $userTree[0]) {
            return null;
        }

        if (count($userTree) === 1) {
            return $origTree[0]['id'];
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