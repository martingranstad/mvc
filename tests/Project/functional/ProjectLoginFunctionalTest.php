<?php
/*
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectLoginFunctionalTest extends WebTestCase
{
    public function testUserLoginUserDoesNotExist(): void
    {
        $client = static::createClient();

        // Johan is not a user, he visits the /proj route
        $client->request('GET', '/proj');

        // Johan succesfully arrives at the /proj route
        $this->assertResponseIsSuccessful();

        // There he finds a form with a textfield with the label "Ditt namn:" and a submit button
        // Johan fills in the textfield with his name and submits the form
        $client->submitForm('Skicka', [
            'name' => 'Johan'
        ]);

        // Johan is redirected to the /proj/game route
        $this->assertResponseRedirects('/proj/game');

        // Johan faces a welcome message greeting him and informing him that he's account balance is 1000
        $this->assertSelectorTextContains('h1', 'Välkommen Johan!');
        $this->assertSelectorTextContains('p', 'Ditt konto innehåller 1000 kr');
    }
}
*/
