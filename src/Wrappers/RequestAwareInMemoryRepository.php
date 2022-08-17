<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Repositories\ApieRepository;
use Apie\Core\Repositories\InMemory\InMemoryRepository;
use Apie\Core\Repositories\Lists\LazyLoadedList;
use ReflectionClass;

final class RequestAwareInMemoryRepository implements ApieRepository
{
    /**
     * @var array<string, InMemoryRepository>
     */
    private array $createdRepositories = [];
    public function __construct(
        private readonly BoundedContextSelected $boundedContextSelected
    ) {
    }

    public function all(ReflectionClass $class): LazyLoadedList
    {
        return $this->getRepository($class)->all($class);
    }

    public function find(IdentifierInterface $identifier): EntityInterface
    {
        return $this->getRepository($identifier::getReferenceFor())->find($identifier);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function getRepository(ReflectionClass $class): InMemoryRepository
    {
        $boundedContext = $this->boundedContextSelected->getBoundedContextFromRequest();
        if (!$boundedContext) {
            $boundedContext = $this->boundedContextSelected->getBoundedContextFromClassName($class->name);
        }
        $id = $boundedContext ? $boundedContext->getId() : new BoundedContextId('unknown');
        if (!isset($this->createdRepositories[$id->toNative()])) {
            $this->createdRepositories[$id->toNative()] = new InMemoryRepository($id);
        }

        return $this->createdRepositories[$id->toNative()];
    }
}
