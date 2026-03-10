<?php 

namespace App\Services\Interfaces\Yummy;

use App\Models\Yummy\YummyEventModel;

interface IYummyEventService {

    public function saveReservation(YummyEventModel $reservation): bool;

}