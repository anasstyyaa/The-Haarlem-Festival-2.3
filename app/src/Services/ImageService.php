<?php 
namespace App\Services;

use App\Services\Interfaces\IImageService;
use App\Repositories\Interfaces\IImageRepository;
use App\Models\ImageModel;
use App\Repositories\ImageRepository;

class ImageService implements IImageService
{
    private IImageRepository $imageRepository;
    public function __construct() {
        $this->imageRepository = new ImageRepository();
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
    
}