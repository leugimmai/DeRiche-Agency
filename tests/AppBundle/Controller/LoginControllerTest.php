<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/admin/');

        // This test verifies whether the logged in user can access the /admin without a 302
        // It also verifies that the "Admin Dashboard" text can be verified.
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Admin Dashboard', $crawler->filter('h2')->text());
    }

    public function testAuthor()
    {
        $this->logIn('ROLE_WRITER');
        $crawler = $this->client->request('GET', '/note/');

        // This test verifies whether the logged in user can access the /admin without a 302
        // It also verifies that the "Admin Dashboard" text can be verified.
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Type in Individual\'s Name', $crawler->filter('h5')->text());
    }

    public function testReviewer()
    {
        $this->logIn('ROLE_REVIEWER');
        $crawler = $this->client->request('GET', '/reviewer/');

        // This test verifies whether the logged in user can access the /admin without a 302
        // It also verifies that the "Admin Dashboard" text can be verified.
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Notes Awaiting Approval', $crawler->filter('h2')->text());
    }

    private function logIn($role)
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        // Create a test "user" token that has the role of admin so that it can view the correct page.
        $token = new UsernamePasswordToken('admin', 'test', $firewallContext, array($role));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}