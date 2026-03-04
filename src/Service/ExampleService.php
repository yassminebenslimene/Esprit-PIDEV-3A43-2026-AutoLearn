<?php

namespace App\Service;

class ExampleService
{
    private ?string $title = null;
    
    // Correction: type de retour ajouté
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    // Correction: paramètre typé
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    
    // Correction: gestion de la valeur nulle
    public function getLength(): int
    {
        return strlen($this->title ?? '');
    }
}
