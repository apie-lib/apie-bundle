<?php
namespace Apie\Tests\ApieBundle\Concerns;

use Apie\Tests\ApieBundle\ApieBundleTestingKernel;

trait ItCreatesASymfonyApplication
{
    public function given_a_symfony_application_with_apie(bool $includeTwig = false): ApieBundleTestingKernel
    {
        $boundedContexts = [
            'default' => [
                'entities_folder' => __DIR__ . '/../BoundedContext/Entities',
                'entities_namespace' => 'Apie\Tests\ApieBundle\BoundedContext\Entities',
                'actions_folder' => __DIR__ . '/../BoundedContext/Actions',
                'actions_namespace' => 'Apie\Tests\ApieBundle\BoundedContext\Actions',
            ],
        ];
        $testItem = new ApieBundleTestingKernel(
            [
                'bounded_contexts' => $boundedContexts
            ],
            $includeTwig
        );
        $testItem->boot();

        return $testItem;
    }
}
