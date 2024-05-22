<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectLoginUnitTest extends WebTestCase
{
    public function testProjectRouteFormExists(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj');

        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input[type="submit"]');
    }

    public function testProjectRouteFormHasCorrectLabel(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj');

        $this->assertSelectorTextContains('label', 'Ditt namn:');
    }

    public function testProjectRouteFormMethodIsPost(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj');

        $this->assertSelectorExists('form[method="post"]');
    }


}