<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TextModel;
use PDO;

class TextRepository extends Repository
{
    public function getById(int $id): ?TextModel
    {
        $sql = "SELECT * FROM text WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): TextModel
    {
        return new TextModel(
            (int)$row['id'],
            $row['content'] ?? ''
        );
    }
}