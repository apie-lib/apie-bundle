<?php
namespace Apie\Tests\ApieBundle;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\Concerns\ItValidatesOpenapi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RunActionTest extends TestCase
{
    use ItCreatesASymfonyApplication;
    use ItValidatesOpenapi;

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_execute_a_method_call_without_arguments()
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/api/default/ApplicationInfo',
            'GET',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json']
        );
        $response = $testItem->handle($request);
        $this->validateResponse($request, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
