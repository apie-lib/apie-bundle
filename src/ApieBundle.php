<?php
namespace Apie\ApieBundle;

use Apie\ApieBundle\DependencyInjection\Compiler\AutoTagActionsCompilerPass;
use Apie\ApieBundle\Wrappers\ConsoleCommandFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ApieBundle extends Bundle
{
    public function registerCommands(Application $application): void
    {
        if ($this->container->has('apie.console.factory')) {
            /** @var ConsoleCommandFactory $factory */
            $factory = $this->container->get('apie.console.factory');
            $application->addCommands(iterator_to_array($factory->create($application)));
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AutoTagActionsCompilerPass());
    }
}
