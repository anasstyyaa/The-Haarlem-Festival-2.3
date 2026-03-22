<?php
namespace App\ViewModels;

use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\Models\PageElementModel;
use App\Services\ButtonService;

class PageElementViewModel
{
    private array $sections = [];

    public function __construct(
        private TextRepository $textRepo,
        private ImageRepository $imageRepo,
        private ButtonService $buttonService
    ) {}

    /**
     * @param PageElementModel[] $elements
     */
   public function build(array $elements): void
{
    foreach ($elements as $el) {

        $model = null;

        switch ($el->getType()) {

            case 'text':
                $model = $this->textRepo->getById($el->getSubElementId());
                break;

            case 'image':
                $model = $this->imageRepo->getById($el->getSubElementId());
                break;
            
            case 'button':
                $model = $this->buttonService->getById($el->getSubElementId());
                break;

            default:
                continue 2; // skip unknown types
        }

        if ($model !== null) {
            $this->sections[$el->getSection()][] = $model;
        }
    }
}

    public function getSections(): array
    {
        return $this->sections;
    }
}