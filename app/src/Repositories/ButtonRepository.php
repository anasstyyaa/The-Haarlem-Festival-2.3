<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ButtonModel;
use App\Repositories\Interfaces\IButtonRepository;
use PDO;

class ButtonRepository extends Repository implements IButtonRepository
{
    public function getById(int $id): ?ButtonModel
    {
        $sql = "SELECT * FROM button WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function mapToModel(array $row): ButtonModel
    {
        return new ButtonModel(
            (int)$row['id'],
            $row['path'] ?? '',
            $row['text'] ?? ''
        );
    }
    public function saveButtonChanges($id, $newText, $newPAth){
        $sql = "UPDATE button SET text = :text, path = :path WHERE id = :id";
        return $this->connection->prepare($sql)->execute([
            'id'   => $id,
            'text' => $newText,
            'path' => $newPAth
        ]);
    }
     public function create(string $text, string $path): int
    {
        $sql = "INSERT INTO button (text, path) VALUES (:text, :path)";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'text' => $text,
            'path' => $path
        ]);

        return (int)$this->connection->lastInsertId();
    }
    public function delete(int $id):bool
    {
        $sql = "DELETE FROM button WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}