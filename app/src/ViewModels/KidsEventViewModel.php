<?php 
namespace App\ViewModels;
use App\Models\KidsEventModel;

class KidsEventViewModel{
    /**
     * @var KidsEventModel[]
     */
    public array $kidsEvents;

    public function __construct(array $kidsEvents){
        $this->kidsEvents = $kidsEvents;
    }
}