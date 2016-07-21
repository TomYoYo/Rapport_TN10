<?php

namespace FrontOffice\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class MainControllerTest extends WebTestCase
{
    protected $client;
    protected $crawler;
    protected $session;
    protected $em;

    public function setUp($connect = true)
    {
        parent::setUp();

        if ($connect) {
            $this->logIn();
            //$this->connect();
        } else {
            $this->client = static::createClient();
        }

        $this->em = $this->getEm();

    }

    public function connect()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'phpunit',
            'PHP_AUTH_PW'   => 'phpunit'
        ));
    }

    private function logIn()
    {
            $this->client = static::createClient();
        $session = $this->getService('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('phpunit', 'phpunit', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

/*
    public function testBase()
    {
        $this->assertEquals(42, 42);
    }
*/
    public function getEm()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    protected function getService($name)
    {
        return static::$kernel->getContainer()->get($name);
    }

    protected function getParameter($name)
    {
        return static::$kernel->getContainer()->getParameter($name);
    }

    public function generateUrl($route, $parameters = array())
    {
        return $this->getService('router')->generate($route, $parameters);
    }

    public function checkPresence($element)
    {
        $container = str_replace('&#039;', "'", $this->client->getResponse()->getContent());
        $msg = 'Il manque "'.$element.'" dans la page';//'.$container.'"';
        $this->assertGreaterThan(0, substr_count($container, $element), $msg);
    }

    public function checkNonPresence($container, $element)
    {
        $msg = 'Il y a toujours "'.$element.'" dans "'.$container.'"';
        $this->assertEquals(0, $this->crawler->filter($container.':contains("'.$element.'")')->count(), $msg);
    }

    public function gotoURL($url, $method = 'GET', $param = array())
    {
        $this->crawler = $this->client->request($method, $url, $param);
    }

    public function gotoRoute($route, $parameters = array())
    {
        //$parameters['_locale']='fr';
        $url = $this->generateUrl($route, $parameters);
        //if ($route == 'mobile_client_autocomplete') die($url);
        $this->crawler = $this->client->request('GET', $url);
    }

    public function checkStatus($status)
    {
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    public function debug($die = true)
    {
        echo '<pre>'.$this->client->getResponse()->getContent().'</pre>';
        if ($die) {
            throw  new \Exception('La fonction debug a coupé l\'execution du script');
        }
    }

    public function supp($reponame, $id, $bundle = 'MobiliteTiersBundle:')
    {
        $repo = $this->em->getRepository($bundle.$reponame);
        $entity = $repo->find($id);

        if ($entity != null) {
            $this->em->remove($entity);
            $this->em->flush();
        } else {
            echo 'suppression impossible de '.$reponame.' '.$id."\n";
        }
    }
}
