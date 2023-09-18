<?php
namespace Apie\Tests\ApieBundle\RestApi;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DropdownOptionsTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     * @dataProvider prefixProvider
     */
    public function it_can_give_dropdown_options(string $prefix)
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/api/default/ManyColumns/dropdown-options/UserIdentifier',
            'POST',
            [],
            [],
            [],
            [],
            json_encode([
                'input' => '123'
            ])
        );
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function prefixProvider(): Generator
    {
        yield 'cms prefix' => ['cms'];
        yield 'api prefix' => ['api'];
    }
}
