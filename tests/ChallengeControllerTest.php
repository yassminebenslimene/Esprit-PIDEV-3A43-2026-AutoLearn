<?php

namespace App\Tests;

use App\Entity\Challenge;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChallengeControllerTest extends WebTestCase
{
    public function testChallengeListPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/challenges');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.challenge-list-page');
    }

    public function testChallengeDetailPage(): void
    {
        $client = static::createClient();
        
        // Récupérer un challenge existant
        $challenge = static::getContainer()
            ->get('doctrine')
            ->getRepository(Challenge::class)
            ->findOneBy([]);
        
        $this->assertNotNull($challenge, 'Aucun challenge trouvé dans la base de test');
        
        $crawler = $client->request('GET', '/challenge/' . $challenge->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testChallengePlayPageWithUser(): void
    {
        $client = static::createClient();
        
        // Récupérer un utilisateur existant
        $user = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([]);
        
        $this->assertNotNull($user, 'Aucun utilisateur trouvé dans la base de test');
        
        // Simuler l'utilisateur connecté
        $client->loginUser($user);
        
        $challenge = static::getContainer()
            ->get('doctrine')
            ->getRepository(Challenge::class)
            ->findOneBy([]);
        
        $this->assertNotNull($challenge, 'Aucun challenge trouvé dans la base de test');
        
        $crawler = $client->request('GET', '/challenge/' . $challenge->getId() . '/play/0');
        
        // Accepter soit une page 200 OK, soit une redirection vers la page de détail
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || 
            $client->getResponse()->isRedirect('/challenge/' . $challenge->getId()),
            'La réponse devrait être 200 OK ou une redirection vers /challenge/' . $challenge->getId()
        );
        
        // Optionnel : Vérifier la redirection spécifique
        if ($client->getResponse()->isRedirect()) {
            $this->assertResponseRedirects('/challenge/' . $challenge->getId());
        }
    }

    public function testChallengePlayPageRedirectsWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        
        $challenge = static::getContainer()
            ->get('doctrine')
            ->getRepository(Challenge::class)
            ->findOneBy([]);
        
        $this->assertNotNull($challenge, 'Aucun challenge trouvé dans la base de test');
        
        $crawler = $client->request('GET', '/challenge/' . $challenge->getId() . '/play/0');
        
        // Devrait rediriger vers login car l'utilisateur n'est pas connecté
        $this->assertResponseRedirects('/login');
    }
}