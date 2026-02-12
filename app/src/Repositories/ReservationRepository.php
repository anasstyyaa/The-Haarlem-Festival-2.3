<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ReservationModel;
use PDO;
use PDOException;
use RuntimeException;

class ReservationRepository extends Repository implements IReservationRepository
{
    public function create(ReservationModel $reservation): void
    {
        try {
            $sql = "
                INSERT INTO reservations (user_id, pc_id, start_time, end_time, status, total_price)
                VALUES (:user_id, :pc_id, :start_time, :end_time, :status, :total_price)
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':user_id'     => $reservation->user_id,
                ':pc_id'       => $reservation->pc_id,
                ':start_time'  => $reservation->start_time,
                ':end_time'    => $reservation->end_time,
                ':status'      => $reservation->status,
                ':total_price' => $reservation->total_price,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to create reservation.');
        }
    }

    public function findByUserId(int $userId): array
    {
        try {
            $sql = "
                SELECT r.*
                FROM reservations r
                WHERE r.user_id = :user_id
                ORDER BY r.start_time DESC
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':user_id' => $userId]);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'mapRowToReservation'], $rows);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load reservations for user.');
        }
    }
    public function findAll(): array
    {
        try {
            $sql = "
                SELECT
                    r.*,
                    u.name AS user_name
                FROM reservations r
                JOIN users u ON u.id = r.user_id
                ORDER BY r.start_time DESC
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'mapRowToReservation'], $rows);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load reservations.');
        }
    }

    public function hasOverlap(int $pcId, string $start, string $end): bool
    {
        try {
            $sql = "
                SELECT COUNT(*)
                FROM reservations
                WHERE pc_id = :pc_id
                  AND status = 'booked'
                  AND start_time < :end_time
                  AND end_time > :start_time
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':pc_id'      => $pcId,
                ':start_time' => $start,
                ':end_time'   => $end,
            ]);

            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to check reservation overlap.');
        }
    }

    public function findReservationById(int $id): ?ReservationModel
    {
        try {
            $sql = "
                SELECT
                    r.*,
                    u.name AS user_name
                FROM reservations r
                JOIN users u ON u.id = r.user_id
                WHERE r.id = :id
                LIMIT 1
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $id]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->mapRowToReservation($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load reservation.');
        }
    }

    public function updateReservationStatus(int $id, string $status): void
    {
        try {
            $sql = "UPDATE reservations SET status = :status WHERE id = :id";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':id'     => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Reservation not found.');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update reservation status.');
        }
    }

    private function mapRowToReservation(array $row): ReservationModel
    {
        $res = new ReservationModel();
        $res->id          = (int)($row['id'] ?? 0);
        $res->user_id     = (int)($row['user_id'] ?? 0);
        $res->pc_id       = (int)($row['pc_id'] ?? 0);
        $res->start_time  = (string)($row['start_time'] ?? '');
        $res->end_time    = (string)($row['end_time'] ?? '');
        $res->status      = (string)($row['status'] ?? '');
        $res->created_at  = $row['created_at'] ?? null;
        $res->total_price = (float)($row['total_price'] ?? 0);

        $res->user_name = $row['user_name'] ?? null;

        return $res;
    }
}
