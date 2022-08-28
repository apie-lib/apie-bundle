<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\ApieBundle\Wrappers\DashboardContents;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsDashboardTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function it_can_display_a_dashboard_without_twig(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(DashboardContents::NO_TWIG_MESSAGE, $response->getContent());
    }

    /**
     * @test
     */
    public function it_can_display_a_dashboard_with_twig(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie(true);
        $request = Request::create(
            '/cms/default/',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringNotContainsString(DashboardContents::NO_TWIG_MESSAGE, $response->getContent());
        // see dashboard.html.twig
        $this->assertStringContainsString('default dashboard', $response->getContent());
    }
}
