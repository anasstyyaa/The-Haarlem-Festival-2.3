<?php

namespace App\Services\Interfaces;
use App\Models\KidsEventModel;

interface IKidsEventService
{
       
    public function getAll(): array;
    public function getEventById(int $id): ?KidsEventModel; 
    public function getEventBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel;
    public function mapDayToDate(string $dayName): ?string;
}