<?php

namespace App\Tests\Service;

use App\Entity\Post;
use App\Service\PostManager;
use PHPUnit\Framework\TestCase;

class PostManagerTest extends TestCase
{
    public function testValidPost()
    {
        $post = new Post();
        $post->setTitle("Symfony Test");
        $post->setContent("Ceci est un contenu valide");

        $manager = new PostManager();

        $this->assertTrue($manager->validate($post));
    }

    public function testPostWithoutTitle()
    {
        $this->expectException(\InvalidArgumentException::class);

        $post = new Post();
        $post->setContent("Contenu valide ici");

        $manager = new PostManager();
        $manager->validate($post);
    }

    public function testPostWithShortContent()
    {
        $this->expectException(\InvalidArgumentException::class);

        $post = new Post();
        $post->setTitle("Test");
        $post->setContent("court");

        $manager = new PostManager();
        $manager->validate($post);
    }
}