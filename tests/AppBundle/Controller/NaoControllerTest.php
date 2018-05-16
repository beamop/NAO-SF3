<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NaoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Engagez-vous', $crawler->filter('h2.headline')->text());
    }

    /**
     * @dataProvider urlsProvider
     */
    public function testUrls($url, $expectedStatusCode)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertSame($expectedStatusCode, $client->getResponse()->getStatusCode());

    }

    public function urlsProvider()
    {
        return [
            ['/observations', 200],
            ['observation/detail/1', 200],
            ['/news', 200],
            ['/news/detail/reconnaitre-les-oiseaux', 200],
            ['/a-propos', 200],
            ['/contact', 200],
            ['/observation/ajouter', 302],
            ['/register/', 200],
            ['/login', 200],
        ];
    }
}
