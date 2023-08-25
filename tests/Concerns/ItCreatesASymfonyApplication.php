<?php
namespace Apie\Tests\ApieBundle\Concerns;

use Apie\Common\Wrappers\RequestAwareInMemoryDatalayer;
use Apie\Tests\ApieBundle\ApieBundleTestingKernel;

trait ItCreatesASymfonyApplication
{
    public function given_a_symfony_application_with_apie(bool $includeTwig = false, string $defaultDatalayer = RequestAwareInMemoryDatalayer::class): ApieBundleTestingKernel
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
                'bounded_contexts' => $boundedContexts,
                'datalayers' => [
                    'default_datalayer' => $defaultDatalayer,
                ],
                'doctrine' => [
                    'run_migrations' => true,
                    'connection_params' => [
                        'driver' => 'pdo_sqlite'
                    ]
                ],
            ],
            $includeTwig
        );
        $testItem->boot();

        return $testItem;
    }
}
