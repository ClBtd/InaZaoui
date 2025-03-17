<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    // Vérifie que la page d'accueil est accessible et contient le titre 'Photographe'
    public function testControllerReturnsHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Photographe');
    }

    // Vérifie que la page 'guests' est accessible et contient 'Invités'
    public function testControllerReturnsGuests(): void
    {
        $client = static::createClient();
        $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Invités');
    }

    // Vérifie que la page 'portfolio' est accessible et contient 'Portfolio'
    public function testControllerReturnsPortfolio(): void
    {
        $client = static::createClient();
        $client->request('GET', '/portfolio');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Portfolio');
    }

    // Vérifie que la page 'about' est accessible et contient 'Qui suis-je ?'
    public function testControllerReturnsAbout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Qui suis-je ?');
    }
}
