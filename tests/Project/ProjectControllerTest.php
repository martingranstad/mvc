<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    public function testAboutRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('about-project');
    }

    public function testHomeRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        // Assert that it has arrived at the /proj route
        $this->assertRouteSame('project');
    }

    public function testTitleInHomeRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('title', 'Projekt hem');
    }

    public function testTitleInAboutRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('title', 'Om projektet');
    }

    public function testH1ExistsHomeRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('h1');
    }

    public function testH1ExistsAboutRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('h1');
    }
}
