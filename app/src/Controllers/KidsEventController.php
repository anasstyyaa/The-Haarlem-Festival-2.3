<?php 
namespace App\Controllers;

use App\Services\KidsEventService;
use App\Repositories\KidsEventRepository;
use App\ViewModels\KidsEventViewModel;


class KidsEventController
{
private KidsEventService $service;
    public function __construct()
    {
        $this->service = new KidsEventService(new KidsEventRepository);
    }

   public function index(): void
{
    $kidsEvents = $this->service->getAll();
   //  var_dump($kidsEvents);
    $vm = new KidsEventViewModel($kidsEvents);
   require __DIR__ . '/../Views/event/kidsEvent.php';
}

}
