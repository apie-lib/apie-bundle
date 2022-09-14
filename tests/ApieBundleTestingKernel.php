<?php
namespace Apie\Tests\ApieBundle;

use Apie\ApieBundle\ApieBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class ApieBundleTestingKernel extends Kernel
{
    private readonly array $apieConfig;

    public function __construct(
        array $apieConfig = [],
        private readonly bool $includeTwigBundle = false,
        private readonly bool $includeSecurityBundle = false
    ) {
        if (!$this->includeSecurityBundle) {
            $apieConfig['enable_security'] = false;
        }
        $this->apieConfig = $apieConfig;
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
        $res = [
            new FrameworkBundle(), // this is needed to have a functional http_kernel service.
            new ApieBundle()
        ];
        if ($this->includeTwigBundle) {
            $res[] = new TwigBundle();
        }
        if ($this->includeSecurityBundle) {
            $res[] = new SecurityBundle();
        }
        return $res;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../fixtures/services.yaml');
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('apie', $this->apieConfig);
            $container->loadFromExtension(
                'framework',
                [
                    'http_method_override' => false,
                    'secret' => '123456',
                    'router' => ['resource' => '.', 'type' => 'apie']
                ]
            );
        });
    }
}
