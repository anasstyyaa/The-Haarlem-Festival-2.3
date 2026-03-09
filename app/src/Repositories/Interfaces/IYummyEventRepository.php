<?php 

namespace App\Repositories\Interfaces;

use App\Models\YummyEventModel;

interface IYummyEventRepository {

    public function saveReservation(YummyEventModel $reservation): bool;
}