<?php

namespace App\Tests\Controller;

use App\Entity\Url;
use App\Utils\Str;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlsControllerTest extends WebTestCase
{
    /** @test */
    public function homepage_should_display_url_shortener_form()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Botly');
        $this->assertSelectorTextContains('h1', 'The best URL shortener out there !');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="form[original]"]');
        $this->assertSelectorExists('input[placeholder="Enter the URL to shorten here"]');
    }

    /** @test */
    public function form_should_work_with_valid_data()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $client->submit($form, [
            'form[original]' => 'https://python.org',
        ]);
        $this->assertResponseRedirects();
    }

    /** @test */
    public function shortened_version_should_redirect_to_original_url()
    {
        $client = static::createClient();

        $em = self::$container->get('doctrine')->getManager();

        $url = new Url();
        $url->setOriginal('https://airbnb.com');
        $shortened = Str::random(6);
        $url->setShortened($shortened);
        $em->persist($url);
        $em->flush();

        $client->request('GET', '/'.$shortened);
        $this->assertResponseRedirects();
    }
}
