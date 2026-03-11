<?php 

namespace App\Services;

use App\Models\Yummy\YummyEventModel;
use App\Repositories\Interfaces\Yummy\IYummyEventRepository;
use App\Services\Interfaces\Yummy\IYummyEventService;

class YummyEventService implements IYummyEventService {

    private IYummyEventRepository $yummyEventRepository;

    public function __construct(IYummyEventRepository $yummyEventRepository) {
        $this->yummyEventRepository = $yummyEventRepository;
    }

    public function saveReservation(YummyEventModel $reservation): bool {
        return $this->yummyEventRepository->saveReservation($reservation);
    }
}