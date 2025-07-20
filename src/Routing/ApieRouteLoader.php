<?php
namespace Apie\ApieBundle\Routing;

use Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider;
use Apie\Common\Interfaces\GlobalRouteDefinitionProviderInterface;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Common\RouteDefinitions\ActionHashmap;
use Apie\Common\RouteDefinitions\PossibleRoutePrefixProvider;
use Apie\Core\ApieLib;
use Apie\Core\Attributes\Route as AttributesRoute;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\RestApi\RouteDefinitions\RestApiRouteDefinitionProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileExistenceResource;
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
        private readonly ContextBuilderFactory $contextBuilder,
        private readonly LoggerInterface $logger = new NullLogger(),
        private readonly array $scanBoundedContexts = [],
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
                if (!@mkdir($this->scanBoundedContexts['search_path'], recursive: true)) {
                    $this->logger->error('I could not create path: "' . $this->scanBoundedContexts['search_path'] . '"');
                }
            }
            if (is_dir($this->scanBoundedContexts['search_path'])) {
                $routes->addResource(new GlobResource($this->scanBoundedContexts['search_path'], '*', true));
            } else {
                $routes->addResource(new FileExistenceResource($this->scanBoundedContexts['search_path']));
            }
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
        $apieContext = $this->contextBuilder->createGeneralContext([
            'route-gen' => true,
        ]);
        if ($this->routeProvider instanceof GlobalRouteDefinitionProviderInterface) {
            foreach ($this->routeProvider->getGlobalRoutes() as $routeDefinition) {
                $routes->addResource(new ReflectionClassResource(new ReflectionClass($routeDefinition)));
                /** @var HasRouteDefinition $routeDefinition */

                $requirements = [];
                $url = $routeDefinition->getUrl();
                $placeholders = $url->getPlaceholders();
                if (in_array('properties', $placeholders)) {
                    $requirements['properties'] = '[a-zA-Z0-9]+(/[a-zA-Z0-9]+)*';
                }
                $path = ltrim($url, '/');
                $method = $routeDefinition->getMethod();
                $defaults = $routeDefinition->getRouteAttributes()
                    + [
                        '_controller' => $routeDefinition->getController(),
                        '_is_apie' => true,
                    ];
                $route = (new Route($path, $defaults, $requirements))->setMethods([$method->value]);
                $routes->add(
                    'apie._global.' . $routeDefinition->getOperationId(),
                    $route
                );
            }
        }
        foreach ($this->boundedContextHashmap as $boundedContextId => $boundedContext) {
            foreach ($this->routeProvider->getActionsForBoundedContext($boundedContext, $apieContext) as $routeDefinition) {
                $routes->addResource(new ReflectionClassResource(new ReflectionClass($routeDefinition)));
                /** @var HasRouteDefinition $routeDefinition */
                $prefix = $this->routePrefixProvider->getPossiblePrefixes($routeDefinition);

                $requirements = $prefix->getRouteRequirements();
                $url = $routeDefinition->getUrl();
                $placeholders = $url->getPlaceholders();
                if (in_array('properties', $placeholders)) {
                    $requirements['properties'] = '[a-zA-Z0-9]+(/[a-zA-Z0-9]+)*';
                }
                $path = $prefix . $boundedContextId . '/' . ltrim($url, '/');
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
