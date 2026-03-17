<?php

namespace App\Controllers;

use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\Models\PageElementModel;
use App\Models\TextModel;
use App\Models\ImageModel;

class PageElementController
{
    private PageElementRepository $service;
    private TextRepository $textService;
     private ImageRepository $imgService;

    public function __construct()
    {
        $this->service = new PageElementRepository();
         $this->textService = new TextRepository();
          $this->imgService = new ImageRepository();
    }


    // public function store(): void
    // {
    //     $element = new PageElementModel();

    //     $element->setSection($_POST['section']);
    //     $element->setType($_POST['type']);
    //     $element->setContent($_POST['content']);
    //     $element->setOrder((int)$_POST['display_order']);

    //     $this->service->createElement($element);

    //     header('Location: /admin/page-elements');
    //     exit;
    // }

    public function showEditForm(array $vars): void
    {
        // $id = (int)$vars['id'];
        // $pageElement = $this->service->getById($id);
        // if($pageElement->getType()=='text'){
        //  $text = $this->textService->getById($pageElement->getId());
        //   include __DIR__ . '/../Views/admin/text/textEditForm.php';
        // }elseif($pageElement->getType()=='image'){
        // $img = $this->imgService->getById($pageElement->getId());
        // include __DIR__ . '/../Views/admin/img/imgEditForm.php';
        // }
         $id = (int)$vars['id'];
       
         $text = $this->textService->getById($id);
          include __DIR__ . '/../Views/admin/text/textEditForm.php';
    }
     public function showImgEditForm(array $vars): void
    {
        $id = (int)$vars['id'];
        $img = $this->imgService->getById($id);
        include __DIR__ . '/../Views/admin/img/imgEditForm.php';
        
    }
    public function saveTextChanges(array $vars):void
    {
       $id = (int)$vars['id'];
       $newText = $_POST['newText'];
       $this->textService->saveTextChanges($id, $newText);
      header('Location: /admin/kidsPage');
    }

    // public function update(array $vars): void
    // {
    //     $id = (int)$vars['id'];

    //     $element = $this->service->getElementById($id);

    //     if (!$element) {
    //         header('Location: /admin/page-elements');
    //         exit;
    //     }

    //     $element->setSection($_POST['section']);
    //     $element->setType($_POST['type']);
    //     $element->setContent($_POST['content']);
    //     $element->setOrder((int)$_POST['display_order']);

    //     $this->service->updateElement($element);

    //     header('Location: /admin/page-elements');
    //     exit;
    // }

    public function delete(array $vars): void
    {
        $id = (int)$vars['id'];

        if ($this->service->deleteElement($id)) {
            header('Location: /admin/page-elements');
            exit;
        }

        echo "Error deleting element.";
    }
}