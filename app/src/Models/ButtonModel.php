<?php

namespace App\Models;

class ButtonModel implements Renderable
{
    private int $id;
    private string $path;
    private string $text;

    public function __construct(int $id, string $path, string $text)
    {
        $this->id = $id;
        $this->path = $path;
        $this->text = $text;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }
    public function getText(): string 
    {
        return $this->text;
    }
     public function render(): string
    {
        ob_start();

        $model = $this; 

        require __DIR__ . '/../Views/components/button.php';

        return ob_get_clean();
    }
}