<?php

namespace App\Services;

use App\Repositories\KidsEventRepository;
use App\Models\KidsEventModel;
use App\Services\Interfaces\IKidsEventService;

class KidsEventService implements IKidsEventService
{
    private KidsEventRepository $repository;

    public function __construct(KidsEventRepository $repository)
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
    $dayName = strtolower(trim($dayName));
    $daysMap = [
        'monday'    => 1,
        'tuesday'   => 2,
        'wednesday' => 3,
        'thursday'  => 4,
        'friday'    => 5,
        'saturday'  => 6,
        'sunday'    => 7,
    ];

    if (!isset($daysMap[$dayName])) return null;

    $today = (int)date('N'); 
    $targetDay = $daysMap[$dayName];

    $diff = ($targetDay - $today + 7) % 7; 
    $diff = $diff === 0 ? 7 : $diff; 

    return date('Y-m-d', strtotime("+$diff days"));
}
}
