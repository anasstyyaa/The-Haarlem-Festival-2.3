<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ImageModel;
use PDO;

class ImageRepository extends Repository
{
    public function getById(int $id): ?ImageModel
    {
        $sql = "SELECT * FROM image WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): ImageModel
    {
        return new ImageModel(
            (int)$row['id'],
            $row['imgURL'] ?? '',
            $row['altText'] ?? ''
        );
    }
    public function updateImage(int $id, string $imgURL, string $altText): bool
{
    $sql = "UPDATE image 
            SET imgURL = :imgURL, altText = :altText 
            WHERE id = :id";

    $stmt = $this->connection->prepare($sql);

    return $stmt->execute([
        'id' => $id,
        'imgURL' => $imgURL,
        'altText' => $altText
    ]);
}
}