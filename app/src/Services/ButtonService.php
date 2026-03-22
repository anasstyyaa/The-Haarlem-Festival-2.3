<?php 
namespace App\Services;

use App\Repositories\ButtonRepository;
use App\Models\ButtonModel;
use App\Services\Interfaces\IButtonService;

class ButtonService implements IButtonService
{
    private ButtonRepository $buttonRepository;

    public function __construct(
    ) {
        $this->buttonRepository = new ButtonRepository();
    }

   public function getById(int $id): ?ButtonModel
    {
        return $this->buttonRepository->getById($id);
    }

    public function mapToModel(array $row): ButtonModel
    {
         return $this->buttonRepository->mapToModel($row);
    }
    public function saveButtonTextChanges($id, $newText){
        return $this->buttonRepository->saveButtonTextChanges($id,$newText);
    }

}
