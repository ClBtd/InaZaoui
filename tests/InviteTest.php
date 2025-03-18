<?php

namespace App\Tests;

use App\Entity\User;
use App\Security\UserChecker;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InviteTest extends WebTestCase
{
    // Vérifie que le UserChecker peut être instancié
    public function testUserCheckerCanBeInstantiated(): void
    {
        $userChecker = new UserChecker();
        $this->assertInstanceOf(UserChecker::class, $userChecker);
    }

    // Teste l'ajout et la suppression d'un utilisateur
    public function testAddandDeleteUser()
    {
        // Connexion d'un utilisateur admin
        $client = static::createClient();
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();
        $client->loginUser($user);

        // Ajout d'un utilisateur
        $client->request('GET', '/admin/invite/add');
        $client->submitForm('Ajouter', [
            'user[name]' => 'userTest@example.com',
            'user[description]' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit.',
            'user[email]' => 'userTest@example.com',
            'user[password]' => 'password',
        ]);

        // Vérifie si l'utilisateur a été ajouté
        $client->followRedirect();
        $this->assertSelectorTextContains('tbody', 'userTest');
        $userTest = $userRepository->findOneBy(['email' => 'userTest@example.com']);
        $this->assertNotNull($userTest);

        $userTestId = $userTest->getId();

        // Suppression de l'utilisateur
        $client->request('GET', "/admin/invite/delete/$userTestId");
        $client->followRedirect();
        $this->assertSelectorTextNotContains('tbody', 'userTest');
    }

    // Teste la désactivation et la réactivation de l'accès d'un utilisateur
    public function testCancelUserAccess()
    {
        // Connexion d'un utilisateur admin
        $client = static::createClient();
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();
        $client->loginUser($user);

        // Désactivation de l'accès de l'utilisateur et vérifie la désactivation
        $crawler = $client->request('GET', '/admin/invite');
        $link = $crawler->selectLink("Désactiver l'accès")->link();
        $client->click($link);
        $crawler = $client->request('GET', '/admin/invite');
        $this->assertSelectorTextContains('tbody', "Réactiver l'accès");

        // Réactivation de l'accès de l'utilisateur
        $link = $crawler->selectLink("Réactiver l'accès")->link();
        $client->click($link);
        $crawler = $client->request('GET', '/admin/invite');
        $this->assertSelectorTextNotContains('tbody', 'Réactiver');
    }
}
