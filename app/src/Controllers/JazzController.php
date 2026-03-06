<?php

namespace App\Controllers;

use App\Services\Interfaces\IArtistService;
use App\Models\ArtistModel;

class JazzController
{
    private IArtistService $service;

    public function __construct(IArtistService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $artists = $this->service->getAllArtists();
        include __DIR__ . '/../Views/event/jazz/index.php';
    }

    public function detail($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->service->getArtistById($id);

        if (!$artist) {
            http_response_code(404);
            echo 'Artist not found';
            return;
        }

        $events = $this->service->getJazzEventsForArtist($id);
        include __DIR__ . '/../Views/event/jazz/detail.php';
    }

    public function adminIndex()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $artists = $this->service->getAllArtists();
        include __DIR__ . '/../Views/admin/jazz/index.php';
    }

    public function showCreateForm()
    {
        include __DIR__ . '/../Views/admin/jazz/createArtist.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = $this->handleImageUpload('image_file', 'artist');

            $artist = new ArtistModel();
            $artist->setName(trim($_POST['name'] ?? ''));
            $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
            $artist->setDescription(trim($_POST['description'] ?? ''));

            if ($fileName) {
                $artist->setImageUrl('/assets/uploads/jazz/artists/' . $fileName);
            }

            if ($this->service->createArtist($artist)) {
                header('Location: /admin/jazz?status=created');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/createArtist.php';
    }

    public function showEditForm($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->service->getArtistById($id);

        if (!$artist) {
            header('Location: /admin/jazz?error=notfound');
            exit;
        }

        include __DIR__ . '/../Views/admin/jazz/editArtist.php';
    }

    public function update($vars)
    {
        $id = (int)$vars['id'];
        $artist = $this->service->getArtistById($id);

        if (!$artist) {
            header('Location: /admin/jazz');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $artist->setName(trim($_POST['name'] ?? ''));
            $artist->setShortDescription(trim($_POST['short_description'] ?? ''));
            $artist->setDescription(trim($_POST['description'] ?? ''));

            $newImage = $this->handleImageUpload('image_file', 'artist');
            if ($newImage) {
                $artist->setImageUrl('/assets/uploads/jazz/artists/' . $newImage);
            }

            if ($this->service->updateArtist($id, $artist)) {
                header('Location: /admin/jazz?status=updated');
                exit;
            }
        }

        include __DIR__ . '/../Views/admin/jazz/editArtist.php';
    }

    public function delete($vars)
    {
        $id = (int)$vars['id'];
        $this->service->deleteArtist($id);
        header('Location: /admin/jazz?status=deleted');
        exit;
    }

    private function handleImageUpload(string $inputName, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/jazz/artists/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid($prefix . '_', true) . '.' . $extension;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }

        return null;
    }
}