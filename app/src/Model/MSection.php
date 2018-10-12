<?php

namespace Zipofar\Model;

class MSection extends BaseModel
{
    protected $fields =  [
        'id' => '',
        'name' => '',
        'lft' => '',
        'rgt' => '',
    ];

    public function getById($id)
    {
        $this->queryParams->addRequestParams(['id' => $id]);
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();

        $sql = $this->queryBuilder
            ->select('id', 'name')
            ->from('section')
            ->where($stringWhere)
            ->build();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($arrayWhere);
        $data = $stmt->fetch();

        return $data !== false ? $data : [];
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

    public function addSection($params)
    {
        $parent_id = $params['parent_id'];
        $parentSection = $this->getByIdFull($parent_id);
        $parentLft = $parentSection['lft'];

        $sqlUpLft = 'UPDATE section SET lft = lft + 2 WHERE lft > :parentLft';
        $sqlUpRgt = 'UPDATE section SET rgt = rgt + 2 WHERE rgt > :parentLft';
        $sql = 'INSERT INTO section (name, lft, rgt) VALUES (:name, :lft, :rgt)';

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sqlUpLft);
            $stmt->execute(['parentLft' => $parentLft]);

            $stmt = $this->pdo->prepare($sqlUpRgt);
            $stmt->execute(['parentLft' => $parentLft]);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $params['name'],
                'lft' => $parentLft + 1,
                'rgt' => $parentLft + 2
            ]);

            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \PDOException($e->getMessage());
        }
    }

    public function updateSection($params)
    {
        $sql = 'UPDATE section SET name = :name WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    protected function getByIdFull($id)
    {
        $this->queryParams->addRequestParams(['id' => $id]);
        $stringWhere = $this->queryParams->getStringWhere();
        $arrayWhere = $this->queryParams->getArrayWhere();

        $sql = $this->queryBuilder
            ->select('id', 'name', 'lft', 'rgt')
            ->from('section')
            ->where($stringWhere)
            ->build();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($arrayWhere);
        $data = $stmt->fetch();

        return $data !== false ? $data : [];
    }
}
