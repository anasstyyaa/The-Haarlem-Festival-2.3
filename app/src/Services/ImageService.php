<?php

namespace App\Services;

use App\Services\Interfaces\IImageService;
use App\Repositories\Interfaces\IImageRepository;
use App\Models\ImageModel;

class ImageService implements IImageService
{
    private IImageRepository $imageRepository;

    public function __construct(IImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function getById(int $id): ?ImageModel
    {
        return $this->imageRepository->getById($id);
    }

    public function updateImage(int $id, string $imgURL, string $altText): bool
    {
        return $this->imageRepository->updateImage($id, $imgURL, $altText);
    }

    public function createImage(string $imgURL, string $altText): int
    {
        return $this->imageRepository->createImage($imgURL, $altText);
    }

    public function uploadImage(string $inputName, string $folder, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadPath = __DIR__ . '/../../public/assets/images/' . $folder . '/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $fileName = uniqid($prefix . '_', true) . '.' . $extension;

        $success = move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadPath . $fileName);

        return $success ? $fileName : null;
    }
}