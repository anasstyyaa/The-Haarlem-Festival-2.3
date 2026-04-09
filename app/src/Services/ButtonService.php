<?php 
namespace App\Services;

use App\Repositories\ButtonRepository;
use App\Models\ButtonModel;
use App\Services\Interfaces\IButtonService;

class ButtonService implements IButtonService
{
    private ButtonRepository $buttonRepository;

    public function __construct(
        ButtonRepository $buttonRepository
    ) {
        $this->buttonRepository = $buttonRepository;
    }

    public function getById(int $id): ?ButtonModel
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid button ID");
        }

        try {
            return $this->buttonRepository->getById($id);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            throw new \Exception("Failed to fetch button");
        }
    }

    public function saveButtonChanges( $id,  $text, $path): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid button ID");
        }

        if (trim($text) === '') {
            throw new \InvalidArgumentException("Button text cannot be empty");
        }

        if (!filter_var($path, FILTER_VALIDATE_URL) && !str_starts_with($path, '/')) {
            throw new \InvalidArgumentException("Invalid path");
        }

        try {
            return $this->buttonRepository->saveButtonChanges($id, $text, $path);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            throw new \Exception("Failed to update button");
        }
    }
}


