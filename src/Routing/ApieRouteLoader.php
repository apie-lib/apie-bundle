<?php
namespace Apie\ApieBundle\Routing;

use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\RouteDefinitions\PossibleRoutePrefixProvider;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Context\ApieContext;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads the Apie routing into the symfony loader system.
 */
final class ApieRouteLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly RouteDefinitionProviderInterface $routeProvider,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly PossibleRoutePrefixProvider $routePrefixProvider,
    ) {
    }

    /**
     * @param mixed $resource
     * @param mixed $type
     */
    public function load($resource, $type = null): RouteCollection
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "apie" loader twice');
        }

        $routes = new RouteCollection();
        $apieContext = new ApieContext([]);
        foreach ($this->boundedContextHashmap as $boundedContextId => $boundedContext) {
            foreach ($this->routeProvider->getActionsForBoundedContext($boundedContext, $apieContext) as $routeDefinition) {
                /** @var HasRouteDefinition $routeDefinition */
                $prefix = $this->routePrefixProvider->getPossiblePrefixes($routeDefinition);

                $requirements = $prefix->getRouteRequirements();
                $path = $prefix . $boundedContextId . '/' . ltrim($routeDefinition->getUrl(), '/');

                $method = $routeDefinition->getMethod();
                $defaults = $routeDefinition->getRouteAttributes()
                    + [
                        '_controller' => $routeDefinition->getController(),
                        '_is_apie' => true,
                    ];
                $route = (new Route($path, $defaults, $requirements))->setMethods([$method->value]);
                $routes->add(
                    'apie.' . $boundedContextId . '.' . $routeDefinition->getOperationId(),
                    $route
                );
            }
        }
        
        return $routes;
    }

    /**
     * @param mixed $resource
     * @param mixed $type
     */
    public function supports($resource, $type = null): bool
    {
        return 'apie' === $type;
    }
}
