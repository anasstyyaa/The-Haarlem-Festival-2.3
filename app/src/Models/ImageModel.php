<?php

namespace App\Models;

class ImageModel implements Renderable
{
    private int $id;
    private string $imgURL;
    private ?string $altText;

    public function __construct(int $id, string $imgURL, ?string $altText = null)
    {
        $this->id = $id;
        $this->imgURL = $imgURL;
        $this->altText = $altText;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getImgURL(): string
    {
        return $this->imgURL;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }
      public function render(): string
    {
        ob_start();

        $model = $this;

        require __DIR__ . '/../Views/components/image.php';

        return ob_get_clean();
    }
}