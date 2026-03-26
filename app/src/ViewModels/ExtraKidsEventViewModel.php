<?php 
namespace App\ViewModels;

use App\Models\ExtraKidsEventModel;

class ExtraKidsEventViewModel
{
    public array $events = [];

    public function __construct(array $events)
    {
        $this->events = array_map(function (ExtraKidsEventModel $event) {
            return [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'image' => $event->getImageUrl()
            ];
        }, $events);
    }
}