<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\BoundedContextAwareApieDatalayer;
use Apie\Core\Datalayers\InMemory\InMemoryDatalayer;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

final class RequestAwareInMemoryDatalayer implements BoundedContextAwareApieDatalayer
{
    /**
     * @var array<string, InMemoryDatalayer>
     */
    private array $createdRepositories = [];
    public function __construct(
        private readonly BoundedContextSelected $boundedContextSelected
    ) {
    }

    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): LazyLoadedList
    {
        return $this->getRepository($class, $boundedContext)->all($class, $boundedContext);
    }

    public function find(IdentifierInterface $identifier, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->getRepository($identifier::getReferenceFor(), $boundedContext)->find($identifier, $boundedContext);
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->getRepository($entity->getId()::getReferenceFor(), $boundedContext)->persistNew($entity, $boundedContext);
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->getRepository($entity->getId()::getReferenceFor(), $boundedContext)->persistExisting($entity, $boundedContext);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function getRepository(ReflectionClass $class, ?BoundedContext $boundedContext = null): InMemoryDatalayer
    {
        if ($boundedContext === null) {
            $boundedContext = $this->boundedContextSelected->getBoundedContextFromRequest();
            if (!$boundedContext) {
                $boundedContext = $this->boundedContextSelected->getBoundedContextFromClassName($class->name);
            }
        }
        $boundedContextId = $boundedContext ? $boundedContext->getId() : new BoundedContextId('unknown');
        if (!isset($this->createdRepositories[$boundedContextId->toNative()])) {
            $this->createdRepositories[$boundedContextId->toNative()] = new InMemoryDatalayer($boundedContextId);
        }

        return $this->createdRepositories[$boundedContextId->toNative()];
    }
}
