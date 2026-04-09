<?php 
namespace App\Services;

use App\Services\Interfaces\IPageElementService;
use App\Repositories\Interfaces\IPageElementRepository;
use App\Models\PageElementModel;

use App\Repositories\Interfaces\IButtonRepository;
use App\Repositories\Interfaces\IImageRepository;
use App\Repositories\Interfaces\ITextRepository;

class PageElementService implements IPageElementService
{
    private IPageElementRepository $pageElementRepository;
    private IButtonRepository $buttonRepo;
    private IImageRepository $imageRepo;
    private ITextRepository $textRepo;
    public function __construct(
         IPageElementRepository $pageElementRepository,
         IButtonRepository $buttonRepo,
         IImageRepository $imageRepo,
         ITextRepository $textRepo
    ) {
       $this->pageElementRepository = $pageElementRepository;
       $this->buttonRepo = $buttonRepo;
       $this->imageRepo = $imageRepo;
       $this->textRepo = $textRepo;

    }
   
     /**
     * @return PageElementModel[]
     */
      public function getByPageName(string $pageName): array
    {
     return $this->pageElementRepository->getByPageName($pageName);
    }
    public function getById(int $id): ?PageElementModel
    {
       return $this->pageElementRepository->getById($id);
    }
   public function getPageSections(string $pageName): array
    {
        if (trim($pageName) === '') {
            throw new \InvalidArgumentException("Page name cannot be empty");
        }

        try {
            $elements = $this->pageElementRepository->getByPageName($pageName);
            $sections = [];

            foreach ($elements as $el) {
                $model = match ($el->getType()) {
                    'text' => $this->textRepo->getById($el->getSubElementId()),
                    'image' => $this->imageRepo->getById($el->getSubElementId()),
                    'button' => $this->buttonRepo->getById($el->getSubElementId()),
                    default => null
                };

                if ($model) {
                    $sections[$el->getSection()][] = $model;
                }
            }

            return $sections;

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function createElement(string $type, int $section, string $pageName, array $data): bool
    {
        if (!in_array($type, ['text', 'image', 'button'])) {
            throw new \InvalidArgumentException("Invalid type");
        }

        if ($section < 0) {
            throw new \InvalidArgumentException("Invalid section");
        }

        try {
            switch ($type) {
                case 'text':
                    if (empty($data['content'])) {
                        throw new \Exception("Content required");
                    }
                    $subId = $this->textRepo->create($data['content']);
                    break;

                case 'image':
                    if (empty($data['imgURL'])) {
                        throw new \Exception("Image URL required");
                    }
                    $subId = $this->imageRepo->createImage(
                        $data['imgURL'],
                        $data['altText'] ?? ''
                    );
                    break;

                case 'button':
                    if (empty($data['text']) || empty($data['path'])) {
                        throw new \Exception("Button data required");
                    }
                    $subId = $this->buttonRepo->create(
                        $data['text'],
                        $data['path']
                    );
                    break;
            }

            $position = $this->pageElementRepository
                ->getNextPosition($pageName, $section);

            return $this->pageElementRepository->create(
                $subId,
                $type,
                $pageName,
                $section,
                $position
            );

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete(int $id, string $type): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid ID");
        }

        try {
            match ($type) {
                'text' => $this->textRepo->delete($id),
                'image' => $this->imageRepo->delete($id),
                'button' => $this->buttonRepo->delete($id),
                default => throw new \Exception("Invalid type")
            };

            return $this->pageElementRepository->delete($id, $type);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}