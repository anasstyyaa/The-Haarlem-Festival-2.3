<?php 
namespace App\Services;

use App\Services\Interfaces\ITextService;
use App\Repositories\Interfaces\ITextRepository;
use App\Models\TextModel;

class TextService implements ITextService
{
    public function __construct(
        private ITextRepository $textRepository
    ) {}
    public function getById(int $id): ?TextModel
    {
       return $this->textRepository->getById($id);
    }

    public function saveTextChanges($id, $newText){
       $this->textRepository->saveTextChanges($id, $newText);
    }
    
}