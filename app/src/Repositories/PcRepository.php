<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\PcModel;
use PDO;

class PcRepository extends Repository implements IPcRepository
{
    public function getAll(): array
    {
        $query = "SELECT id, name, specs, price_per_hour, is_active
                  FROM pcs
                  WHERE is_active = 1";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pcs = [];
        foreach ($rows as $row) {
            $pcs[] = $this->mapRowToPc($row);
        }

        return $pcs;
    }

    public function findById(int $id): ?PcModel
    {
        $sql = "SELECT id, name, specs, price_per_hour, is_active
                FROM pcs
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToPc($row);
    }

    public function findAllIncludingInactive(): array
    {
        $sql = "SELECT id, name, specs, price_per_hour, is_active
                FROM pcs
                ORDER BY id ASC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pcs = [];
        foreach ($rows as $row) {
            $pcs[] = $this->mapRowToPc($row);
        }

        return $pcs;
    }

    public function insertPc(string $name, string $specs, ?float $pricePerHour, bool $isActive): void
    {
        $sql = "INSERT INTO pcs (name, specs, price_per_hour, is_active)
                VALUES (:name, :specs, :price, :active)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':name'   => $name,
            ':specs'  => $specs,
            ':price'  => $pricePerHour ?? 0,
            ':active' => $isActive ? 1 : 0,
        ]);
    }

    public function updatePc(int $id, string $name, string $specs, ?float $pricePerHour): void
    {
        $sql = "UPDATE pcs
                SET name = :name, specs = :specs, price_per_hour = :price
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id'    => $id,
            ':name'  => $name,
            ':specs' => $specs,
            ':price' => $pricePerHour ?? 0,
        ]);
    }

    public function toggleActive(int $id): void
    {
        $sql = "UPDATE pcs
                SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function deleteById(int $id): void
    {
        $sql = "DELETE FROM pcs WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    private function mapRowToPc(array $row): PcModel
    {
        $pc = new PcModel();
        $pc->id             = (int)$row['id'];
        $pc->name           = $row['name'];
        $pc->specs          = $row['specs'] ?? '';
        $pc->is_active      = ((int)($row['is_active'] ?? 0)) === 1; // safer cast
        $pc->price_per_hour = (float)($row['price_per_hour'] ?? 0);

        return $pc;
    }
}
