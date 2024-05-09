<?php
namespace Apie\ApieBundle\Doctrine;

use Apie\DoctrineEntityDatalayer\OrmBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

class MergedRegistry implements ManagerRegistry
{
    public const APIE_MANAGER_NAME = 'apie_manager';
    public const APIE_CONNECTION_NAME = 'apie_connection';

    public function __construct(
        private ManagerRegistry $internal,
        private OrmBuilder $ormBuilder
    ) {
    }

    public function getDefaultManagerName(): string
    {
        return $this->internal->getDefaultManagerName();
    }

    public function getManager(?string $name = null): ?ObjectManager
    {
        if ($name === self::APIE_MANAGER_NAME) {
            return $this->ormBuilder->createEntityManager();
        }
        return $this->internal->getManager($name);
    }

    public function getManagers(): array
    {
        $internal = $this->internal->getManagers();
        $internal[self::APIE_MANAGER_NAME] = $this->ormBuilder->createEntityManager();

        return $internal;
    }

    public function resetManager(?string $name = null): ObjectManager
    {
        $this->ormBuilder->createEntityManager()->close();
        return $this->internal->resetManager($name);
    }

    public function getManagerNames(): array
    {
        $internal = $this->internal->getManagerNames();
        $internal[self::APIE_MANAGER_NAME] = 'doctrine.orm.apie_manager_entity_manager';
        return $internal;
    }

    public function getRepository(
        string $persistentObject,
        ?string $persistentManagerName = null
    ): ObjectRepository {
        if ($persistentManagerName === null && str_starts_with($persistentObject, $this->ormBuilder->getGeneratedNamespace())) {
            return $this->ormBuilder->createEntityManager();
        }
        if ($persistentManagerName === self::APIE_MANAGER_NAME) {
            return $this->ormBuilder->createEntityManager();
        }

        return $this->internal->getRepository($persistentObject, $persistentManagerName);
    }

    public function getManagerForClass(string $class): ?ObjectManager
    {
        if (str_starts_with($class, $this->ormBuilder->getGeneratedNamespace())) {
            return $this->ormBuilder->createEntityManager();
        }

        return $this->internal->getManagerForClass($class);
    }

    public function getDefaultConnectionName(): string
    {
        return $this->internal->getDefaultConnectionName();
    }

    public function getConnection(?string $name = null): object
    {
        if ($name === self::APIE_CONNECTION_NAME) {
            return $this->ormBuilder->createEntityManager()->getConnection();
        }
        return $this->internal->getConnection($name);
    }

    public function getConnections(): array
    {
        $internal = $this->internal->getConnections();
        $internal[self::APIE_CONNECTION_NAME] = $this->getConnection(self::APIE_CONNECTION_NAME);

        return $internal;
    }

    public function getConnectionNames(): array
    {
        $internal = $this->internal->getConnectionNames();
        $internal[self::APIE_CONNECTION_NAME] = 'doctrine.dbal.apie_connection';

        return $internal;
    }

    /**
     * @param array<int, mixed> $args) $args
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->internal->$method(...$args);
    }
}
