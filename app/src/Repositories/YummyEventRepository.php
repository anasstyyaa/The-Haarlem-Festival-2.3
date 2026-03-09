<?php 

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\YummyEventModel;
use App\Repositories\Interfaces\IYummyEventRepository;
use PDO;

class YummyEventRepository extends Repository implements IYummyEventRepository {

    public function saveReservation(YummyEventModel $reservation): bool {
        $sql = "INSERT INTO YummyReservation (restaurant_id, [date], startTime, duration, price, comment) 
                VALUES (:rid, :date, :start, :dur, :price, :comm)";
                
        $stmt = $this->connection->prepare($sql);
        
        return $stmt->execute([
            'rid'   => $reservation->getRestaurantId(),
            'date'  => $reservation->getDate(),
            'start' => $reservation->getStartTime(),
            'dur'   => $reservation->getDuration(),
            'price' => $reservation->getPrice(),
            'comm'  => $reservation->getComment()
        ]);
    }
}