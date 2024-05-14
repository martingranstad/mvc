<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectNavigationTest extends WebTestCase
{
    public function testAboutLinkInNavigation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        // Click the navigation link to the About page
        $link = $crawler->selectLink('About')->link();
        $crawler = $client->click($link);

        // Assert that it has arrived at the /proj/about route
        $this->assertRouteSame('about-project');
    }

    public function testHomeLinkInNavigation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();

        // Click the navigation link to the Home page
        $link = $crawler->selectLink('Home')->link();
        $crawler = $client->click($link);

        // Assert that it has arrived at the /proj route
        $this->assertRouteSame('project');
    }
}
