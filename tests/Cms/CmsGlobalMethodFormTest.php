<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsGlobalMethodFormTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_a_form_for_an_action(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/action/ApplicationInfo/manyArguments',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('-form', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }
}
