<?php

namespace App\Controllers;

use App\Services\Interfaces\IPageElementService;
use App\Services\Interfaces\ITextService;
use App\Services\Interfaces\IImageService;
use App\Services\Interfaces\IButtonService;

class PageElementController
{
    private IPageElementService $pageService;
    private ITextService $textService;
    private IImageService $imgService;
    private IButtonService $buttonService;

    public function __construct(
        IPageElementService $pageService,
        ITextService $textService,
        IImageService $imgService,
        IButtonService $buttonService
    ) {
        $this->pageService = $pageService;
        $this->textService = $textService;
        $this->imgService = $imgService;
        $this->buttonService = $buttonService;
    }

    public function showEditForm(array $vars): void
    {
        try {
            $id = (int)$vars['id'];
            $text = $this->textService->getById($id);

            include __DIR__ . '/../Views/admin/text/textEditForm.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading text edit form.";
        }
    }

    public function showImgEditForm(array $vars): void
    {
        try {
            $id = (int)$vars['id'];
            $img = $this->imgService->getById($id);

            include __DIR__ . '/../Views/admin/img/imgEditForm.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading image edit form.";
        }
    }

    public function saveTextChanges(array $vars): void
    {
        try {
            $id = (int)$vars['id'];
            $newText = $_POST['newText'];

            $this->textService->saveTextChanges($id, $newText);

            header('Location: /admin/home/index');
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error saving text changes.";
        }
    }

    public function delete(array $vars): void
    {
        try {
            $id = (int)$vars['id'];
            $type = $vars['type'];

            if ($this->pageService->delete($id, $type)) {
                header('Location: /admin/home/index');
                exit;
            }

            throw new \Exception("Delete failed");

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error deleting element.";
        }
    }

    public function saveImgChanges(array $vars): void
    {
        try {
            $id = (int)$vars['id'];

            $fileName = $this->imgService->uploadImage('image', 'general', 'img');

            if (!$fileName) {
                throw new \Exception("Upload failed");
            }

            $imgURL = '/assets/images/general/' . $fileName;
            $altText = $_POST['altText'] ?? '';

            $this->imgService->updateImage($id, $imgURL, $altText);

            header('Location: /admin/home/index');
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error saving image changes.";
        }
    }

    public function showButtonEditForm(array $vars): void
    {
        try {
            $id = (int)$vars['id'];
            $button = $this->buttonService->getById($id);

            include __DIR__ . '/../Views/admin/button/buttonEditForm.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading button edit form.";
        }
    }

    public function saveButtonChanges(array $vars): void
    {
        try {
            $id = (int)$vars['id'];

            $text = $_POST['text'] ?? '';
            $path = $_POST['path'] ?? '';

            $this->buttonService->saveButtonChanges($id, $text, $path);

            header('Location: /admin/home/index');
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error saving button changes.";
        }
    }

    public function createForm(): void
    {
        try {
            $type = $_GET['type'] ?? null;
            $section = $_GET['section'] ?? null;
            $pageName = $_GET['pageName'] ?? null;

            if (!$type || !$section || !$pageName) {
                throw new \Exception("Invalid request");
            }

            require __DIR__ . "/../Views/admin/elements/create_" . $type . ".php";

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading create form.";
        }
    }

    public function store(): void
    {
        try {
            $type = $_POST['type'];
            $section = (int)$_POST['section'];
            $pageName = $_POST['pageName'];

            if ($type === 'image') {

                $folder = 'pages/' . $pageName;

                $fileName = $this->imgService->uploadImage('image', $folder, 'img');

                if (!$fileName) {
                    throw new \Exception("Upload failed");
                }

                $_POST['imgURL'] = '/assets/images/' . $folder . '/' . $fileName;
            }

            $this->pageService->createElement(
                $type,
                $section,
                $pageName,
                $_POST
            );

            header("Location: /admin/home/index");
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error storing element.";
        }
    }
}