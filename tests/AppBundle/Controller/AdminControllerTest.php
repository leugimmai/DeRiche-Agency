<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/');

        // This test verifies whether the logged in user can access the /admin without a 302
        // It also verifies that the "Admin Dashboard" text can be verified.
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Admin Dashboard', $crawler->filter('h2')->text());
    }

    public function testCreatePage()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/#create');

        // This test verifies whether the logged in user can access the /admin without a 302
        // It also verifies that the "Admin Dashboard" text can be verified.
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Below is the form for creating users. Can only be accessed by the Administrator Role.', $this->client->getResponse()->getContent());
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        // Create a test "user" token that has the role of admin so that it can view the correct page.
        $token = new UsernamePasswordToken('admin', 'test', $firewallContext, array('ROLE_ADMIN', 'ROLE_USER'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}