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
       ITextRepository $textRepository
    ) {
         $this->textRepository= $textRepository;
      }
        public function getById(int $id): ?TextModel
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid text ID");
        }

        try {
            return $this->textRepository->getById($id);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function saveTextChanges(int $id, string $newText): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid text ID");
        }

        if (trim($newText) === '') {
            throw new \InvalidArgumentException("Text cannot be empty");
        }

        try {
            return $this->textRepository->saveTextChanges($id, $newText);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
}