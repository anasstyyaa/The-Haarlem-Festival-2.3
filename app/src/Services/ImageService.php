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
        try{
        return $this->imageRepository->getById($id);
         } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

     public function updateImage(int $id, string $imgURL, string $altText): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid image ID");
        }

        if (trim($imgURL) === '') {
            throw new \InvalidArgumentException("Image URL cannot be empty");
        }

        try {
            return $this->imageRepository->updateImage($id, $imgURL, $altText);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function createImage(string $imgURL, string $altText): int
    {try{
        return $this->imageRepository->createImage($imgURL, $altText);
    } catch (\Throwable $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

     public function uploadImage(string $inputName, string $folder, string $prefix): ?string
    {
        try {
            if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("Invalid upload");
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed)) {
                throw new \Exception("Invalid file type");
            }

            $uploadPath = __DIR__ . '/../../public/assets/images/' . $folder . '/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fileName = uniqid($prefix . '_', true) . '.' . $extension;

            if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadPath . $fileName)) {
                throw new \Exception("Move failed");
            }

            return $fileName;

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}