<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\PageElementModel;
use PDO;

class PageElementRepository extends Repository
{
    /**
     * @return PageElementModel[]
     */
    public function getByPageName(string $pageName): array
    {
        $sql = "SELECT * 
                FROM pageElement 
                WHERE pageName = :pageName
                ORDER BY section ASC, position ASC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['pageName' => $pageName]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $rows);
    }

    private function mapToModel(array $row): PageElementModel
    {
        return new PageElementModel(
            (int)$row['id'],
            (int)$row['subElementId'],
            $row['type'],
            $row['pageName'],
            (int)$row['section'],
            (int)$row['position'],
        );
    }
}