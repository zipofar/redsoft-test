<?php

namespace Zipofar\Model;

class MProduct extends BaseModel
{
    protected $fields =  [
        'id' => '',
        'name' => '',
        'availability' => '',
        'price' => '',
        'brand' => '',
    ];

    public function getById($id)
    {
        $this->queryParams->addRequestParams(['id' => $id]);

        $stringWhere = $this->queryParams->getStringWhere();
        $sql = $this->queryBuilder
            ->select('id', 'name', 'availability', 'price', 'brand')
            ->from('product')
            ->where($stringWhere)
            ->build();

        $stmt = $this->pdo->prepare($sql);
        $arrayWhere = $this->queryParams->getArrayWhere();
        $stmt->execute($arrayWhere);
        $data = $stmt->fetch();

        return $data !== false ? $data : [];
    }

    public function getProducts($params)
    {
        $this->queryParams->addRequestParams($params);

        $offset = $this->queryParams->getOffset();
        $limit = $this->queryParams->getLimit();
        $limit = $limit > $this->options['max_limit'] ? $this->options['max_limit'] : $limit;
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();

        $sql = $this->queryBuilder
            ->select('id', 'name', 'availability', 'price', 'brand')
            ->from('product');

        if (!empty($arrayWhere)) {
            $sql->where($stringWhere);
        }

        $sql = $sql->limit($limit, $offset)->build();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($arrayWhere);
        $res = $stmt->fetchAll();

        return $res;
    }

    public function getProductsInSection($section_id, $params)
    {
        $this->queryParams->addRequestParams($params);

        $offset = $this->queryParams->getOffset();
        $limit = $this->queryParams->getLimit();
        $limit = $limit > $this->options['max_limit'] ? $this->options['max_limit'] : $limit;
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();

        $sql = $this->queryBuilder
            ->select('p.id', 'p.name', 'p.availability', 'p.price', 'p.brand')
            ->from('product AS p JOIN productsection AS ps on (p.id = ps.product_id)');

        if (empty($arrayWhere)) {
            $sql->where('ps.section_id = :section_id');
        } else {
            $sql->where($stringWhere.' AND ps.section_id = :section_id');
        }

        $sql = $sql->limit($limit, $offset)->build();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($arrayWhere, ['section_id' => $section_id]));
        $res = $stmt->fetchAll();
        return $res;
    }

    public function showProductsInSectionSub($section_id, $params)
    {
        $this->queryParams->addRequestParams($params);

        $offset = $this->queryParams->getOffset();
        $limit = $this->queryParams->getLimit();
        $limit = $limit > $this->options['max_limit'] ? $this->options['max_limit'] : $limit;
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();

        $sqlIdSections = 'SELECT s1.id FROM section s1, section s2 
                          WHERE s1.lft >= s2.lft AND s1.rgt <= s2.rgt AND s2.id = :section_id';

        $sql = $this->queryBuilder
            ->select('p.id', 'p.name', 'p.availability', 'p.price', 'p.brand')
            ->from('product AS p JOIN productsection AS ps on (p.id = ps.product_id)');

        if (empty($arrayWhere)) {
            $sql->where("ps.section_id IN ({$sqlIdSections})");
        } else {
            $sql->where($stringWhere." AND ps.section_id IN ({$sqlIdSections})");
        }

        $sql = $sql->limit($limit, $offset)->build();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($arrayWhere, ['section_id' => $section_id]));
        $res = $stmt->fetchAll();
        return $res;
    }

    public function addProduct($product)
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

        return $lastId;
    }

    public function deleteProduct($id)
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

    public function putProduct($data)
    {
        $sql = 'UPDATE product SET 
                  name = :name, 
                  availability = :availability, 
                  price = :price, 
                  brand = :brand
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $data['id'];
    }
}
