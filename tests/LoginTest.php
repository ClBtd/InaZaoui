<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    // Teste une connexion réussie
    public function testLoginSuccessful(): void
    {
        $client = static::createClient();

        // Connexion d'un utilisateur classique
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            '_username' => 'user1@example.com',
            '_password' => 'password',
        ]);

        // Vérification de la connexion réussie
        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorTextContains('nav', 'Déconnexion');
    }

    // Teste une connexion échouée
    public function testLoginFailed(): void
    {
        $client = static::createClient();

        // Connexion d'un utilisateur classique
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            '_username' => 'user1@example.com',
            '_password' => 'fail',
        ]);

        // Vérification de l'échec de la connexion
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials');
    }

    // Teste qu'un utilisateur désactivé ne peut pas se connecter
    public function testUserCannotLoginIfDisabled(): void
    {
        // Récupération d'un l'utilisateur classique
        $client = static::createClient();
        $repository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $repository->findOneByEmail('user2@example.com');
        $this->assertNotNull($user);

        // Désactivation de son accès
        $user->setAccess(false);
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->flush();

        // Tentative de connexion de l'utilisateur
        $client->request('GET', '/login');
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();
        $client->submit($form, [
            '_username' => 'user2@example.com',
            '_password' => 'password',
        ]);

        // Vérification de l'échec de la connexion
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'Votre compte est désactivé.');

        // Réactivation de l'utilisateur après le test
        $user->setAccess(true);
        $entityManager->flush();
    }
}
