<?php
namespace Apie\ApieBundle\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @TODO lazy initialization
 */
class ServiceContextBuilder implements ContextBuilderInterface
{
    public function __construct(private ServiceLocator $serviceLocator)
    {
    }
    public function process(ApieContext $context): ApieContext
    {
        foreach (array_keys($this->serviceLocator->getProvidedServices()) as $serviceId) {
            $context = $context->withContext($serviceId, $this->serviceLocator->get($serviceId));
        }
        return $context;
    }
}
