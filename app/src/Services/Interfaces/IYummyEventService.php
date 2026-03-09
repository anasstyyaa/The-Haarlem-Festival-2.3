<?php 

namespace App\Services\Interfaces;

use App\Models\YummyEventModel;

interface IYummyEventService {

    public function saveReservation(YummyEventModel $reservation): bool;

}