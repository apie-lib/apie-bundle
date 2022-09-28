<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsGlobalMethodCommitTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
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
            http_build_query(['form' => [
                'input' => 42,
            ]])
        );
        $response = $testItem->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('http://localhost/cms/default/action/ApplicationInfo/powerOf2', $response->headers->get('Location'));
    }
}
