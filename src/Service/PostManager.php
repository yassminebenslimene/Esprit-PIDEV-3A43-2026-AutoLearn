<?php

namespace App\Service;

use App\Entity\Post;

class PostManager
{
    public function validate(Post $post): bool
    {
        if (empty($post->getTitre())) {
            throw new \InvalidArgumentException("Le titre est obligatoire");
        }

        if (strlen($post->getContenu()) < 10) {
            throw new \InvalidArgumentException("Le contenu doit contenir au moins 10 caractères");
        }

        return true;
    }
}
