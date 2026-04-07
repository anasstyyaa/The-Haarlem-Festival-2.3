<?php 
namespace App\Services;

use App\Services\Interfaces\ITextService;
use App\Repositories\Interfaces\ITextRepository;
use App\Models\TextModel;
use App\Repositories\TextRepository;

class TextService implements ITextService
{
    private ITextRepository $textRepository;
    public function __construct(
    ) {
         $this->textRepository=new TextRepository();
      }
    public function getById(int $id): ?TextModel
    {
       return $this->textRepository->getById($id);
    }

    public function saveTextChanges($id, $newText){
       $this->textRepository->saveTextChanges($id, $newText);
    }
    
}