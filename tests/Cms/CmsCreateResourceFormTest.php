<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsCreateResourceFormTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function it_can_display_a_form_for_creating_a_resource(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/resource/create/ManyColumns',
            'GET'
        );
        $response = $testItem->handle($request);
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('<form', $response->getContent());
    }
}
