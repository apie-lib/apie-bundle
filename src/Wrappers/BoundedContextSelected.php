<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Entities\EntityInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Helper class that returns the current bounded context.
 */
final class BoundedContextSelected
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly BoundedContextHashmap $boundedContextHashmap
    ) {
    }

    /**
     * Returns a bounded context from a symfony request if it is available.
     */
    public function getBoundedContextFromRequest(): ?BoundedContext
    {
        $request = $this->requestStack->getMainRequest();
        if (!$request) {
            return null;
        }
        if ($request->attributes->has(ContextConstants::BOUNDED_CONTEXT_ID)) {
            return $this->boundedContextHashmap[$request->attributes->get(ContextConstants::BOUNDED_CONTEXT_ID)];
        }
        if ($request->attributes->has(ContextConstants::RESOURCE_NAME)) {
            return $this->getBoundedContextFromClassName($request->attributes->get(ContextConstants::RESOURCE_NAME));
        }
        return null;
    }

    /**
     * Returns the boundedcontext linked to an entity class. If the entity is linked to multiple bounded contexts it will return
     * the first bounded context.
     *
     * @param class-string<EntityInterface> $className
     */
    public function getBoundedContextFromClassName(string $className): ?BoundedContext
    {
        foreach ($this->boundedContextHashmap as $boundedContext) {
            foreach ($boundedContext->resources as $resource) {
                if ($resource->name === $className) {
                    return $boundedContext;
                }
            }
        }
        return null;
    }
}
