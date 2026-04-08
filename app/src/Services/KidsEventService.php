<?php

namespace App\Services;

use App\Repositories\Interfaces\IKidsEventRepository;
use App\Models\KidsEventModel;
use App\Services\Interfaces\IKidsEventService;

class KidsEventService implements IKidsEventService
{
    private IKidsEventRepository $repository;

    public function __construct(IKidsEventRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    
    public function getEventById(int $id): ?KidsEventModel
    {
        return $this->repository->getById($id);
    }

   
    public function getEventBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel
    {
        return $this->repository->getIdBySchedule($day, $startTime, $endTime);
    }
    public function create(KidsEventModel $event): bool{
        return $this->repository->create($event);
    }
      public function update(KidsEventModel $event): bool
    {
        return $this->repository->update($event);
    }
    public function delete(int $id): bool
{
    return $this->repository->delete($id);
}
public function mapDayToDate(string $dayName): ?string
{
  //  $dayName = strtolower(trim($dayName));
    $daysMap = [
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
        'Sunday'    => 7,
    ];

    //if (!isset($daysMap[$dayName])) return null;

    $today = (int)date('N'); 
    $targetDay = $daysMap[$dayName];

    $diff = ($targetDay - $today + 7) % 7; 
    $diff = $diff === 0 ? 7 : $diff; 

    return date('Y-m-d', strtotime("+$diff days"));
}
public function decreaseCapacity(int $id, int $qty): void
{
    $event = $this->repository->getById($id);

    if (!$event) return;

    $newLimit = max(0, $event->getLimit() - $qty);
    $event->setLimit($newLimit);

    $this->repository->update($event);
}
}
