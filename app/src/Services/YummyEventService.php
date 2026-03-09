<?php 

namespace App\Services;

use App\Models\YummyEventModel;
use App\Repositories\Interfaces\IYummyEventRepository;
use App\Services\Interfaces\IYummyEventService;

class YummyEventService implements IYummyEventService {

    private IYummyEventRepository $yummyEventRepository;

    public function __construct(IYummyEventRepository $yummyEventRepository) {
        $this->yummyEventRepository = $yummyEventRepository;
    }

    public function saveReservation(YummyEventModel $reservation): bool {
        return $this->yummyEventRepository->saveReservation($reservation);
    }
}