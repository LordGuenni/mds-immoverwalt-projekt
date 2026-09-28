<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsReachable()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Anmeldung');
    }

    public function testStartPageRequiresLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects();
    }
}
