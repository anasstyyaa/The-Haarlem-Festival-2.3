<?php

namespace App\Models;

class TextModel implements Renderable
{
    private int $id;
    private string $content;

    public function __construct(int $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }
     public function render(): string
    {
        ob_start();

        $model = $this; 

        require __DIR__ . '/../Views/components/text.php';

        return ob_get_clean();
    }
}