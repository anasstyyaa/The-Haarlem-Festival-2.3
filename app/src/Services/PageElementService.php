<?php 
namespace App\Services;

use App\Services\Interfaces\IPageElementService;
use App\Repositories\Interfaces\IPageElementRepository;
use App\Models\PageElementModel;

class PageElementService implements IPageElementService
{
    public function __construct(
        private IPageElementRepository $pageElementRepository
    ) {}
   
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
}