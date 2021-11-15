<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddressBookControllerTest extends WebTestCase
{
    public function testShowAddressListAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('h1')->count());
        $this->assertStringContainsString('Address Book', $crawler->filter('.container h1')->text());

        $link = $crawler
            ->filter('a:contains("Add New")')
            ->eq(0)
            ->link();
        $crawler = $client->click($link);
        $this->assertStringContainsString('Add New Address', $crawler->filter('.container h2')->text());
    }

    public function testAddAddressAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/address/add');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add New Address', $crawler->filter('.container h2')->text());

        $form = $crawler->selectButton('Add')->form();

        $form['address[lastname]'] = 'Testln';
        $form['address[phoneNumber]'] = '0777777777';
        $form['address[birthday]'] = '1996-08-14';
        $form['address[emailAddress]'] = 'radsp@gmail';
        $form['address[streetNumber]'] = '100, Elsy Mw';
        $form['address[zip]'] = '12500';
        $form['address[city]'] = 'Panadura';
        $form['address[country]'] = '82';

        $client->followRedirects();
        $crawler = $client->submit($form);
        $this->assertStringContainsString('Add New Address', $crawler->filter('.container h2')->text());

        //Test invalid input
        $this->assertStringContainsString('This value should not be blank.', $crawler->filter('.container li')->text());
        $this->assertStringContainsString('This value is not a valid email address.', $crawler->filter('.container')->text());

        //Test with vaild data
        $form['address[firstname]'] = 'Testfn';
        $form['address[emailAddress]'] = 'radsperera@gmail.com';

        $photos_directory = $client->getKernel()->getContainer()->getParameter('photos_directory');
        $form['address[picture]']->upload($photos_directory . '/blank_photo.jpg');

        $crawler = $client->submit($form);
        $this->assertStringContainsString('Address Book', $crawler->filter('.container h1')->text());
        $this->assertStringContainsString('Address successfully added!', $crawler->filter('.alert-success')->text());

    }

    public function testEditAddressAction()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $address = $this->entityManager->getRepository(Address::class)
            ->findOneBy([], ['id' => 'DESC'], 1, 0);

        $this->assertEquals('Testfn', $address->getFirstname());
        $this->assertEquals('Testln', $address->getLastname());
        $this->assertEquals('radsperera@gmail.com', $address->getEmailAddress());

        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/address/edit/'.$address->getId());
        $this->assertStringContainsString('Edit Address', $crawler->filter('.container h2')->text());

        $form = $crawler->selectButton('Edit')->form();
        $form['address[phoneNumber]'] = '0999999999';
        $client->followRedirects();
        $crawler = $client->submit($form);
        $this->assertStringContainsString('Address successfully updated!', $crawler->filter('.alert-success')->text());
    }

    public function testRemoveAddressAction()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $address = $this->entityManager->getRepository(Address::class)
            ->findOneBy([], ['id' => 'DESC'], 1, 0);

        $this->assertEquals('Testfn', $address->getFirstname());
        $this->assertEquals('Testln', $address->getLastname());
        $this->assertEquals('radsperera@gmail.com', $address->getEmailAddress());

        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/address/delete/'.$address->getId());
        $this->assertStringContainsString('Address successfully deleted!', $crawler->filter('.alert-success')->text());
    }

    public function testShowOverviewAction()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $allAddress = $this->entityManager->getRepository(Address::class)
            ->findAll();

        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/overview');
        $this->assertStringContainsString('Overview - Address Book', $crawler->filter('.container h2')->text());
        $this->assertEquals(count($allAddress), $crawler->filter('#total-count')->text());
    }
}
