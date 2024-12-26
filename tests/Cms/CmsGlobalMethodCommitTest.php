<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsGlobalMethodCommitTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_a_form_without_a_valid_csrf(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/action/ApplicationInfo/powerOf2',
            'POST',
            [],
            [],
            [],
            [],
            http_build_query([
                'form' => [
                    'input' => 42,
                ],
            ])
        );
        $response = $testItem->handle($request);
        $this->assertEquals(419, $response->getStatusCode());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_submit_a_form_for_an_action(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/action/ApplicationInfo/powerOf2',
            'POST',
            [],
            [],
            [],
            [],
            http_build_query([
                'form' => [
                    'input' => 42,
                ],
                '_csrf' => 'string',
            ])
        );
        $response = $testItem->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertStringStartsWith(
            '/cms/default/last-action-result/',
            $response->headers->get('Location')
        );
    }
}
