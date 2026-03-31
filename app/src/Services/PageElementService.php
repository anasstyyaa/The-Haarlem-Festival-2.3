<?php 
namespace App\Services;

use App\Services\Interfaces\IPageElementService;
use App\Repositories\Interfaces\IPageElementRepository;
use App\Models\PageElementModel;
use App\Repositories\PageElementRepository;

class PageElementService implements IPageElementService
{
    private IPageElementRepository $pageElementRepository;
    public function __construct(
    ) {
       $this->pageElementRepository = new PageElementRepository();
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
}