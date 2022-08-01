<?php
namespace Apie\Tests\ApieBundle;

use Apie\ApieBundle\ApieBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class ApieBundleTestingKernel extends Kernel
{
    public function __construct(private readonly array $apieConfig = [])
    {
        parent::__construct('test', true);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/cache/' . spl_object_hash($this);
    }

    public function __destruct()
    {
        @system('rm -rf ' . escapeshellarg($this->getCacheDir()));
    }
    
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(), // this is needed to have a functional http_kernel service.
            new ApieBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('apie', $this->apieConfig);
            $container->loadFromExtension('framework', ['http_method_override' => false, 'router' => ['resource' => '.', 'type' => 'apie']]);
        });
    }
}
