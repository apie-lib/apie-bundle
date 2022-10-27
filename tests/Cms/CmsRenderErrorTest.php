<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsRenderErrorTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function it_displays_an_error_page(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie(true);
        $request = Request::create(
            '/cms/does-not-exist/',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('An error occurred. Please try again later.', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }
}
