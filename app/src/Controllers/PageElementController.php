<?php

namespace App\Controllers;

use App\Services\PageElementService;
use App\Services\TextService;
use App\Services\ImageService;
use App\Services\ButtonService;

class PageElementController
{
    private PageElementService $service;
    private TextService $textService;
     private ImageService $imgService;
     private ButtonService $buttonService;

    public function __construct()
    {
        $this->service = new PageElementService();
         $this->textService = new TextService();
          $this->imgService = new ImageService();
          $this->buttonService = new ButtonService();
    }

    public function showEditForm(array $vars): void
    {
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
      header('Location: /admin/home/index');
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
        $type = $vars['type'];

        if ($this->service->delete($id, $type)) {
            header('Location: /admin/home/index');
            exit;
        }

        echo "Error deleting element.";
    }
    public function saveImgChanges(array $vars): void
{
    $id = (int)$vars['id'];

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo "Upload failed.";
        return;
    }

    $file = $_FILES['image'];

    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = __DIR__ . '/../../public/assets/images/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo "Failed to move uploaded file.";
        return;
    }

    $imgURL = '/assets/images/' . $fileName;
    $altText = $_POST['altText'] ?? '';

    $this->imgService->updateImage($id, $imgURL, $altText);

    header('Location: /admin/home/index');
    exit;
}
public function showButtonEditForm(array $vars): void
{
    $id = (int)$vars['id'];
    $button = $this->buttonService->getById($id);

    include __DIR__ . '/../Views/admin/button/buttonEditForm.php';
}
public function saveButtonChanges(array $vars): void
{
    $id = (int)$vars['id'];

    $text = $_POST['text'] ?? '';
    $path = $_POST['path'] ?? '';

    $this->buttonService->saveButtonChanges($id, $text, $path);

    header('Location: /admin/home/index');
    exit;
}
public function createForm(): void
{
    $type = $_GET['type'] ?? null;
    $section = $_GET['section'] ?? null;
    $pageName = $_GET['pageName'] ?? null;

    if (!$type || !$section || !$pageName) {
        echo "Invalid request";
        return;
    }

    require __DIR__ . "/../Views/admin/elements/create_" . $type . ".php";
}
  public function store()
    {
        $type = $_POST['type'];
        $section = (int)$_POST['section'];
        $pageName = $_POST['pageName'];
        if ($type === 'image') {

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo "Upload failed.";
            return;
        }

        $file = $_FILES['image'];

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = __DIR__ . '/../../public/assets/images/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "Failed to move uploaded file.";
            return;
        }

        $_POST['imgURL'] = '/assets/images/' . $fileName;
    }

        $this->service->createElement(
            $type,
            $section,
            $pageName, 
            $_POST
        );

        header("Location: /admin/home/index");
        exit;
    }
}