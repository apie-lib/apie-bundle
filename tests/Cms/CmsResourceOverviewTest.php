<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\ApieBundle\Wrappers\DashboardContents;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsResourceOverviewTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function it_can_display_a_table_with_users(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/resource/User',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('User overview', $response->getContent());
    }
}
