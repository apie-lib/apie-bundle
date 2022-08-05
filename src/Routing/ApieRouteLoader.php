<?php
namespace Apie\ApieBundle\Routing;

use Apie\Core\Actions\HasRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Context\ApieContext;
use Apie\Core\RouteDefinitions\RouteDefinitionProviderInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApieRouteLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly RouteDefinitionProviderInterface $routeProvider,
        private readonly BoundedContextHashmap $boundedContextHashmap
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
                $path = '/api/' . $boundedContextId . '/' . ltrim($routeDefinition->getUrl(), '/');
                $method = $routeDefinition->getMethod();
                $defaults = $routeDefinition->getRouteAttributes() + ['_controller' => $routeDefinition->getController()];

                $route = (new Route($path, $defaults, []))->setMethods([$method->value]);
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
