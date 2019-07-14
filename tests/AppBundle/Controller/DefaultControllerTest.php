<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'adminpassword',
        ]);
    }

    public function testSecuredPage1()
    {
        $crawler = $this->client->request('GET', '/page/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Hello 1 Admin', $crawler->filter('p')->text());
    }

    public function testSecuredPage2()
    {
        $crawler = $this->client->request('GET', '/page/2');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Hello 2 Admin', $crawler->filter('p')->text());
    }

}