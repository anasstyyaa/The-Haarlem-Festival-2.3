<?php

namespace App\Controllers;

use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Models\PageElementModel;
use App\Models\TextModel;

class PageElementController
{
   // private PageElementRepository $service;
    private TextRepository $service;

    public function __construct()
    {
        $this->service = new TextRepository();
    }

    // public function index(): void
    // {
    //     $elements = $this->service->getAllElements();
    //     include __DIR__ . '/../Views/admin/pageElements/index.php';
    // }

    // public function showCreateForm(): void
    // {
    //     include __DIR__ . '/../Views/admin/pageElements/createElement.php';
    // }

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
        $id = (int)$vars['id'];

        $text = $this->service->getById($id);

        if (!$text) {
            header('Location: /admin/page-elements');
            exit;
        }

        include __DIR__ . '/../Views/admin/text/textEditForm.php';
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

    // public function delete(array $vars): void
    // {
    //     $id = (int)$vars['id'];

    //     if ($this->service->deleteElement($id)) {
    //         header('Location: /admin/page-elements');
    //         exit;
    //     }

    //     echo "Error deleting element.";
    // }
}