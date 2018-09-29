<?php

namespace Zipofar\Model;

use Zipofar\Database\ZPdo;
use Zipofar\QueryParams;
use Zipofar\Service\QueryBuilder;

class MProduct
{
    /**
     * @var array Options array
     */
    protected $options = [
        'max_limit' => 20,
    ];

    protected $fields =  [
        'page' => 1,
        'per_page' => 5,
        'name' => '',
        'availability' => '',
        'price' => '',
        'brand' => '',
    ];

    private $pdo;
    private $queryBuilder;
    protected $queryParams;


    public function __construct(ZPdo $pdo, QueryBuilder $queryBuilder, QueryParams $queryParams)
    {
        $this->pdo = $pdo->get();
        $this->queryBuilder = $queryBuilder;
        $this->queryParams = $queryParams;

        $this->queryParams->addFields($this->fields);
        $this->queryParams->setLimitField('per_page');
        $this->queryParams->setOffsetField('page');
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

    /**
     * Get product by ID
     *
     * @param $id Id product
     *
     * @return mixed
     */
    public function getById($id)
    {
        $sql = "SELECT p.id, p.name, p.availability, p.price, p.brand FROM product AS p WHERE p.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data !== false ? $data : [];
    }

    public function getProducts($params)
    {
        $this->queryParams->addQueryParams($params);

        $offset = $this->queryParams->getOffset();
        $limit = $this->queryParams->getLimit();
        $limit = $limit > $this->options['max_limit'] ? $this->options['max_limit'] : $limit;
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();


        if (empty($arrayWhere)) {
            $sql = $this->queryBuilder
                ->select('id', 'name', 'availability', 'price', 'brand')
                ->from('product')
                ->limit($limit, $offset)
                ->build();
        } else {
            $sql = $this->queryBuilder
                ->select('id', 'name', 'availability', 'price', 'brand')
                ->from('product')
                ->where($stringWhere)
                ->limit($limit, $offset)
                ->build();
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($arrayWhere);
        $res = $stmt->fetchAll();

        return $res;
    }

    /**
     * Get product by start part product name
     *
     * @param string $str
     * @param integer $offset For pagination
     *
     * @return array
     */
    public function getBySubStrName($str, $offset = 0)
    {
        $name = "%$str%";
        $sql = "SELECT p.id, p.name, p.availability, p.price, p.brand 
                FROM product AS p WHERE p.name LIKE :name ORDER BY p.name LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $this->options['limit'], \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $data;
    }

    /**
     * @param string $brandName
     * @param integer $offset For pagination
     *
     * @return array
     */
    public function getByBrand($brandName, $offset = 0)
    {
        $arrBrand = explode('+', $brandName);
        $placeHolder = implode(', ', array_fill(0, count($arrBrand), '?'));

        $sql = "SELECT p.id, p.name, p.availability, p.price, p.brand FROM product As p 
                WHERE p.brand IN ($placeHolder) ORDER BY p.name LIMIT ?, ?";

        $stmt = $this->pdo->prepare($sql);

        for ($i = 0; $i < count($arrBrand); $i++) {
            $stmt->bindValue($i + 1, $arrBrand[$i]);
        }
        $stmt->bindValue(count($arrBrand) + 1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(count($arrBrand) + 2, $this->options['limit'], \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $data;
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

    public function getHierarchy()
    {
        $sql = 'SELECT s1.id, s1.name, COUNT(s2.id) - 1 AS level FROM section AS s1, section AS s2
                WHERE s1.lft BETWEEN s2.lft AND s2.rgt GROUP BY s1.id ORDER BY s1.lft;';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $hierarchy = $stmt->fetchAll();

        return $hierarchy;
    }

    public function addProduct($product): void
    {
        $sql1 = 'INSERT INTO product (name, availability, price, brand) VALUES (:name, :availability, :price, :brand)';
        $sql2 = "INSERT INTO productsection (product_id, section_id) VALUES (:product_id, :section_id)";

        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($sql1);
            $stmt->bindValue(':name', $product['name'], \PDO::PARAM_STR);
            $stmt->bindValue(':availability', $product['availability'], \PDO::PARAM_INT);
            $stmt->bindValue(':price', $product['price'], \PDO::PARAM_STR);
            $stmt->bindValue(':brand', $product['brand'], \PDO::PARAM_STR);
            $stmt->execute();
            $lastId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare($sql2);
            $stmt->execute(['product_id' => $lastId, 'section_id' => $product['section_id']]);
            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \PDOException($e->getMessage());
        }
    }

    public function deleteProduct($id): void
    {
        $sql1 = 'DELETE FROM productsection WHERE product_id = :id';
        $sql2 = 'DELETE FROM product WHERE id = :id';

        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($sql1);
            $stmt->execute(['id' => $id]);
            $stmt = $this->pdo->prepare($sql2);
            $stmt->execute(['id' => $id]);
            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \PDOException($e->getMessage());
        }
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
