<?php
namespace Apie\Tests\ApieBundle\RestApi;

use Apie\CmsApiDropdownOption\RouteDefinitions\DropdownOptionsForExistingObjectRouteDefinition;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DropdownOptionsTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    #[\PHPUnit\Framework\Attributes\DataProvider('prefixProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_give_dropdown_options(string $prefix)
    {
        if (!class_exists(DropdownOptionsForExistingObjectRouteDefinition::class)) {
            $this->markTestSkipped('optional composer dependency apie/cms-api-dropdown-option missing');
        }
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/' . $prefix . '/default/ManyColumns/dropdown-options/UserIdentifier',
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

    public static function prefixProvider(): Generator
    {
        yield 'cms prefix' => ['cms'];
        yield 'api prefix' => ['api'];
    }
}
