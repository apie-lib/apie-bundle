<?php
namespace Apie\ApieBundle\Routing;

use Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Common\RouteDefinitions\ActionHashmap;
use Apie\Common\RouteDefinitions\PossibleRoutePrefixProvider;
use Apie\Core\ApieLib;
use Apie\Core\Attributes\Route as AttributesRoute;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\RestApi\RouteDefinitions\RestApiRouteDefinitionProvider;
use ReflectionClass;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\GlobResource;
use Symfony\Component\Config\Resource\ReflectionClassResource;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads the Apie routing into the symfony loader system.
 */
final class ApieRouteLoader extends Loader
{
    private bool $loaded = false;

    /**
     * @param array<string, string> $scanBoundedContexts
     */
    public function __construct(
        private readonly RouteDefinitionProviderInterface $routeProvider,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly PossibleRoutePrefixProvider $routePrefixProvider,
        private readonly array $scanBoundedContexts
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
        $classesForCaching = [
            __CLASS__,
            $this->routeProvider,
            $this->boundedContextHashmap,
            $this->routePrefixProvider,
            RestApiRouteDefinitionProvider::class,
            CmsRouteDefinitionProvider::class,
            RouteDefinitionProviderInterface::class,
            HasRouteDefinition::class,
            UrlRouteDefinition::class,
            RequestMethod::class,
            UrlPrefixList::class,
            ActionHashmap::class,
            ApieLib::class,
            AttributesRoute::class,
        ];
        if (!empty($this->scanBoundedContexts['search_path'])) {
            if (!is_dir($this->scanBoundedContexts['search_path'])) {
                mkdir($this->scanBoundedContexts['search_path'], recursive: true);
            }
            $routes->addResource(new GlobResource($this->scanBoundedContexts['search_path'], '*', true));
        }
        
        foreach ($classesForCaching as $classForCaching) {
            if (is_object($classForCaching) || class_exists($classForCaching)) {
                $routes->addResource(new ReflectionClassResource(new ReflectionClass($classForCaching)));
            }
        }
        $pathsHandled = [];
        foreach ($this->boundedContextHashmap as $boundedContext) {
            foreach ($boundedContext->resources as $resource) {
                $routes->addResource(new ReflectionClassResource($resource));
                $path = dirname($resource->getFileName());
                if (!isset($pathsHandled[$path])) {
                    $pathsHandled[$path] = true;
                    $routes->addResource(new DirectoryResource($path));
                }
            }
        }
        $apieContext = new ApieContext([]);
        foreach ($this->boundedContextHashmap as $boundedContextId => $boundedContext) {
            foreach ($this->routeProvider->getActionsForBoundedContext($boundedContext, $apieContext) as $routeDefinition) {
                $routes->addResource(new ReflectionClassResource(new ReflectionClass($routeDefinition)));
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
