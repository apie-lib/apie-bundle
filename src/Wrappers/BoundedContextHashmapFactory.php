<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\ApieBundle\ValueObjects\EntityNamespace;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;

class BoundedContextHashmapFactory
{
    /**
     * @param array<string, mixed> $boundedContexts
     */
    public function __construct(private array $boundedContexts)
    {
    }

    public function create(): BoundedContextHashmap
    {
        $result = [];
        foreach ($this->boundedContexts as $boundedContextId => $boundedContextConfig) {
            $contextId = new BoundedContextId($boundedContextId);
            $namespace = new EntityNamespace($boundedContextConfig['entities_namespace']);
            $classes = $namespace->getClasses($boundedContextConfig['entities_folder']);
            $namespace = new EntityNamespace($boundedContextConfig['actions_namespace']);
            $methods = $namespace->getMethods($boundedContextConfig['actions_folder']);
            $result[$boundedContextId] = new BoundedContext(
                $contextId,
                $classes,
                $methods
            );
        }
        return new BoundedContextHashmap($result);
    }
}
