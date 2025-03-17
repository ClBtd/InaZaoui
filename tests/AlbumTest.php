<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlbumTest extends WebTestCase
{
    // Test de l'ajout et de la suppression d'un album
    public function testAddandDeleteAlbum()
    {
        // Connexion d'un utilisateur admin 
        $client = static::createClient();
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();

        $client->loginUser($user);

        // Création de l'album
        $client->request('GET', '/admin/album/add');
        $client->submitForm('Ajouter', ['album[name]' => 'Album Test']);
        $client->followRedirect();

        // Vérification de son existence
        $this->assertSelectorTextContains('tbody', 'Album Test');

        //Récupération des données de l'album
        $albumRepository = self::getContainer()->get('doctrine')->getRepository(Album::class);
        $albumTest = $albumRepository->findOneBy(['name' => 'Album Test']);
        $this->assertNotNull($albumTest, "L'album n'existe pas.");

        $albumTestId = $albumTest->getId();

        // Suppression de l'album
        $client->request('GET', "/admin/album/delete/$albumTestId");
        $client->followRedirect();

        // Vérification de sa supression
        $this->assertSelectorTextNotContains('tbody', 'Album Test');
    }

    // Test de la suppression d'un média avec cascade
    public function testDeletMediaCascade()
    {
        // Connexion d'un utilisateur admin 
        $client = static::createClient();
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();
        $client->loginUser($user);

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Création de l'album 
        $newAlbum = new Album();
        $newAlbum->setName('Album Test');
        $entityManager->persist($newAlbum);
        $entityManager->flush();

        // Récupération des données sur l'album
        $albumRepository = self::getContainer()->get('doctrine')->getRepository(Album::class);
        $testAlbum = $albumRepository->findOneBy(['name' => 'Album Test']);

        // Création du média 
        $newMedia = new Media();
        $newMedia->setTitle('Media Test');
        $newMedia->setPath('uploads/test.jpg');
        $newMedia->setUser($user);
        $newMedia->setAlbum($testAlbum);
        $entityManager->persist($newMedia);
        $entityManager->flush();

        // Vérification de l'existence du média
        $crawler = $client->request('GET', "/admin/media");
        $link = $crawler->selectLink('Dernière page')->link();
        $crawler = $client->click($link);
        $this->assertSelectorTextContains('tbody', 'Media Test');

        // Suppression de l'album
        $testAlbumId = $testAlbum->getId();
        $client->request('GET', "/admin/album/delete/$testAlbumId");

        // Vérification de la suppression du média
        $crawler = $client->request('GET', "/admin/media");
        $link = $crawler->selectLink('Dernière page')->link();
        $crawler = $client->click($link);

        $this->assertSelectorTextNotContains('tbody', 'Media Test');
    }

    // Test de la mise à jour d'un album
    public function testUpdateAlbum()
    {
        // Connexion d'un utilisateur admin 
        $client = static::createClient();
        $userRepository = self::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findAdmin();

        $client->loginUser($user);

        // Modification de l'album
        $crawler = $client->request('GET', '/admin/album');
        $link = $crawler->selectLink("Modifier")->link();
        $client->click($link);

        $client->submitForm('Modifier', ['album[name]' => 'Album Test']);
        $client->followRedirect();

        // Vérification de la modification de l'album 
        $this->assertSelectorTextContains('tbody', 'Album Test');

        // Récupération des données de l'album 
        $albumRepository = self::getContainer()->get('doctrine')->getRepository(Album::class);
        $albumTest = $albumRepository->findOneBy(['name' => 'Album Test']);
        $this->assertNotNull($albumTest, "L'album n'existe pas.");

        $albumTestId = $albumTest->getId();

        // Nouvelle modification de l'album
        $client->request('GET', "/admin/album/update/$albumTestId");
        $client->submitForm('Modifier', ['album[name]' => "Album $albumTestId"]);
        $client->followRedirect();

        $this->assertSelectorTextNotContains('tbody', 'Album Test');
    }
}
