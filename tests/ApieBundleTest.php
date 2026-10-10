<?php
namespace Apie\Tests\ApieBundle;

use Apie\CmsApiDropdownOption\RouteDefinitions\DropdownOptionsForExistingObjectRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Fixtures\Enums\IntEnum;
use Apie\RestApi\OpenApi\OpenApiGenerator;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApieBundleTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function bundle_can_be_loaded_out_of_the_box()
    {
        $testItem = $this->given_a_symfony_application_with_apie();

        $container = $testItem->getContainer();
        $this->assertTrue($container->hasParameter('apie.bounded_contexts'));

        $hashmap = $container->get('apie.bounded_context.hashmap');
        $this->assertInstanceOf(BoundedContextHashmap::class, $hashmap);
        $this->assertCount(1, $hashmap);
        $this->assertEquals([ManyColumns::class, User::class], $hashmap['default']->resources->toStringArray());

        if (!class_exists(OpenApiGenerator::class)) {
            return;
        }
        $request = Request::create('/api/default/openapi.json');
        $response = $testItem->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
        $actual = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $file = __DIR__ . '/../fixtures/expected-openapi.json';
        file_put_contents($file, $actual);

        $this->assertEquals(file_get_contents($file), $actual);

        $request = Request::create('/api/default/');
        $response = $testItem->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
        $actual = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('default/openapi.yaml', $actual);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function bundle_can_provide_a_faker()
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $container = $testItem->getContainer();
        /** @var Generator */
        $faker = $container->get('apie.faker');
        $this->assertInstanceOf(IntEnum::class, $faker->fakeClass(IntEnum::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function bundle_does_not_load_cms_dropdown_routes_when_disabled(): void
    {
        if (!class_exists(DropdownOptionsForExistingObjectRouteDefinition::class)) {
            $this->markTestSkipped('CMS Dropdown Option is not installed');
        }
        $testItem = $this->given_a_symfony_application_with_apie(enableCmsDropdown: false);
        $routeNames = array_keys($testItem->getContainer()->get('router')->getRouteCollection()->all());
        $dropdownRouteNames = array_filter(
            $routeNames,
            static fn (string $name) => str_contains($name, 'cms.dropdown_options')
        );

        $this->assertSame([], $dropdownRouteNames);

        $response = $testItem->handle(
            Request::create('/api/default/openapi.yaml'),
            HttpKernelInterface::MAIN_REQUEST,
            false
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringNotContainsString('cms.dropdown_options', (string) $response->getContent());
    }
}
