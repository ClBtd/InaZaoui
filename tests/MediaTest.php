<?php

use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaTest extends WebTestCase
{
    // Teste que l'utilisateur ne peut voir que ses propres médias
    public function testUserCanOnlySeeOwnMedia() : void
    {
        // Connexion d'un utilisateur classique
        $client = static::createClient();
        $user = self::getContainer()->get('doctrine')->getRepository(User::class)->findBy(['name' => 'user1']);
        $client->loginUser($user[0]);

        $client->request('GET', '/admin/media');

        // Vérification de l'affichage spécifique aux utilisateurs non admin
        $this->assertSelectorTextContains('tbody', 'user_media');
        $this->assertSelectorTextNotContains('thead', 'Artiste');
    }

    // Test la pagination des médias
    public function testMediaPaginationWorksCorrectly() : void
    {
        // Connexion d'un utilisateur admin
        $client = static::createClient();
        $admin = self::getContainer()->get('doctrine')->getRepository(User::class)->findAdmin();
        $client->loginUser($admin);

        // Vérification du nombre d'item sur la première page
        $client->request('GET', '/admin/media?page=1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(25, 'tbody tr'); // 25 médias par page

        // Vérification du nombre d'item sur la deuxième page
        $client->clickLink('Suivant');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(25, 'tbody tr');
    }

    // Teste l'ajout et la suppression de médias
    public function testUserCanAddandDeleteMedia() : void
    {
        // Connexion d'un utilisateur classique
        $client = static::createClient();
        $user = self::getContainer()->get('doctrine')->getRepository(User::class)->findBy(['name' => 'user1']);
        $client->loginUser($user[0]);

        // Prépararion du fichier de test
        $tempFilePath = sys_get_temp_dir() . '/test_' . uniqid() . '.jpg';
        copy(__DIR__ . '/./test.jpg', $tempFilePath);
        $file = new UploadedFile($tempFilePath, 'test.jpeg', 'image/jpeg', null, true);

        // Ajout du média
        $crawler = $client->request('GET', '/admin/media/add');
        $form = $crawler->selectButton('Ajouter')->form();
        $formData = [
            'media' => ['title' => 'Test Media', '_token' => $form['media[_token]']->getValue()],
        ];
        $fileData = ['media' => ['file' => $file]];

        $client->request('POST', '/admin/media/add', $formData, $fileData);
        $this->assertResponseRedirects('/admin/media');

        //Vérification de la présence du nouveau média
        $client->followRedirect();
        $this->assertSelectorTextContains('table', 'Test Media');

        // Suppression du média
        $mediaId = self::getContainer()->get('doctrine')->getRepository(Media::class)->findBy(['title' => 'Test Media'])[0]->getId();
        $client->request('GET', "/admin/media/delete/$mediaId");
        $client->followRedirect();
        $this->assertSelectorTextNotContains('tbody', 'Test Media');
    }
}
