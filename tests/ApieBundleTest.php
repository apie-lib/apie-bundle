<?php
namespace Apie\Tests\ApieBundle;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Fixtures\Enums\IntEnum;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApieBundleTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function bundle_can_be_loaded_out_of_the_box()
    {
        $testItem = $this->given_a_symfony_application_with_apie();

        $container = $testItem->getContainer();
        $this->assertTrue($container->hasParameter('apie.bounded_contexts'));

        $hashmap = $container->get('apie.bounded_context.hashmap');
        $this->assertInstanceOf(BoundedContextHashmap::class, $hashmap);
        $this->assertCount(1, $hashmap);
        $this->assertEquals([User::class], $hashmap['default']->resources->toStringArray());

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

    /**
     * @test
     */
    public function bundle_can_provide_a_faker()
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $container = $testItem->getContainer();
        /** @var Generator */
        $faker = $container->get('apie.faker');
        $this->assertInstanceOf(IntEnum::class, $faker->fakeClass(IntEnum::class));
    }
}
