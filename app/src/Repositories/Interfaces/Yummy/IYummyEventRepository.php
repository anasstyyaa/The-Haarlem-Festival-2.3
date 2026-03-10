<?php 

namespace App\Repositories\Interfaces\Yummy;

use App\Models\Yummy\YummyEventModel;

interface IYummyEventRepository {

    public function saveReservation(YummyEventModel $reservation): bool;
}