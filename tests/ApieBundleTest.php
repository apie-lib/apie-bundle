<?php
namespace Apie\Tests\ApieBundle;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApieBundleTest extends TestCase
{
    /**
     * @test
     */
    public function bundle_can_be_loaded_out_of_the_box()
    {
        $boundedContexts = [
            'default' => [
                'entities_folder' => __DIR__ . '/BoundedContext/Entities',
                'entities_namespace' => 'Apie\Tests\ApieBundle\BoundedContext\Entities',
                'actions_folder' => __DIR__ . '/BoundedContext/Actions',
                'actions_namespace' => 'Apie\Tests\ApieBundle\BoundedContext\Actions',
            ],
        ];
        $testItem = new ApieBundleTestingKernel(
            [
                'bounded_contexts' => $boundedContexts
            ]
        );
        $testItem->boot();
        $container = $testItem->getContainer();
        $this->assertEquals($boundedContexts, $container->getParameter('apie.bounded_contexts'));

        $hashmap = $container->get('apie.bounded_context.hashmap');
        $this->assertInstanceOf(BoundedContextHashmap::class, $hashmap);
        $this->assertCount(1, $hashmap);
        $this->assertEquals([User::class], $hashmap['default']->resources->toStringArray());

        $request = Request::create('/api/default/openapi.json');
        $response = $testItem->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
        $actual = $response->getContent();
        $file = __DIR__ . '/../fixtures/expected-openapi.json';
        file_put_contents($file, $actual);

        $this->assertEquals(file_get_contents($file), $actual);
    }
}
