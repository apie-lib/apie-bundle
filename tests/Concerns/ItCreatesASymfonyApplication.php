<?php
namespace Apie\Tests\ApieBundle\Concerns;

use Apie\Common\Wrappers\RequestAwareInMemoryDatalayer;
use Apie\Tests\ApieBundle\ApieBundleTestingKernel;
use PHPUnit\Framework\Attributes\After;

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
                'encryption_key' => 'test',
                'bounded_contexts' => $boundedContexts,
                'datalayers' => [
                    'default_datalayer' => $defaultDatalayer,
                ],
                'enable_doctrine_bundle_connection' => false,
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

    #[After()]
    public function __internalDisableErrorHandler(): void
    {
        restore_exception_handler();
    }
}
