<?php
namespace Apie\Tests\ApieBundle\Doctrine;

use Apie\ApieBundle\Doctrine\MergedRegistry;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\DoctrineEntityConverter\Factories\PersistenceLayerFactory;
use Apie\DoctrineEntityConverter\OrmBuilder as DoctrineEntityConverterOrmBuilder;
use Apie\DoctrineEntityDatalayer\OrmBuilder;
use Apie\Tests\ApieBundle\Doctrine\Entities\SomeEntity;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class MergedRegistryTest extends TestCase
{
    private MergedRegistry $testItem;

    private string $tempPath;

    protected function createEntityManager(): EntityManagerInterface
    {
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/Entities'],
            $isDevMode,
            $proxyDir,
            $cache
        );
        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];
        $connection = DriverManager::getConnection($conn, $config);
        return new EntityManager($connection, $config);
    }

    protected function setUp(): void
    {
        $entityManager = $this->createEntityManager();

        $internalRegistry = new class($entityManager) implements ManagerRegistry {
            private EntityManager $entityManager;

            public function __construct(EntityManager $entityManager)
            {
                $this->entityManager = $entityManager;
            }

            public function getDefaultManagerName(): string
            {
                return 'default';
            }

            public function getManager(?string $name = null): EntityManager
            {
                return $this->entityManager;
            }

            public function getManagers(): array
            {
                return ['default' => $this->entityManager];
            }

            public function resetManager(?string $name = null): EntityManager
            {
                return $this->entityManager;
            }

            public function getManagerNames(): array
            {
                return ['default' => 'default'];
            }

            public function getRepository(string $persistentObject, ?string $persistentManagerName = null)
            {
                return $this->entityManager->getRepository($persistentObject);
            }

            public function getManagerForClass(string $class): ?EntityManager
            {
                return $this->entityManager;
            }

            public function getDefaultConnectionName(): string
            {
                return 'default';
            }

            public function getConnection(?string $name = null): object
            {
                return $this->entityManager->getConnection();
            }

            public function getConnections(): array
            {
                return ['default' => $this->entityManager->getConnection()];
            }

            public function getConnectionNames(): array
            {
                return ['default' => 'default'];
            }

            public function __call(string $method, array $args): mixed
            {
                return $this->entityManager->$method(...$args);
            }
        };
        $this->tempPath = sys_get_temp_dir() . '/' . uniqid('merged');
        $internalOrmBuilder = new DoctrineEntityConverterOrmBuilder(
            new PersistenceLayerFactory(),
            new BoundedContextHashmap()
        );
        $ormBuilder = new OrmBuilder(
            $internalOrmBuilder,
            false,
            false,
            true,
            $this->tempPath . '/proxies',
            null,
            $this->tempPath . '/entities',
            [
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ]
        );

        $this->testItem = new MergedRegistry($internalRegistry, $ormBuilder);
    }

    protected function tearDown(): void
    {
        if ($this->tempPath && $this->tempPath !== '/') {
            system('rm -rf ' . escapeshellarg($this->tempPath));
        }
    }

    public function testGetDefaultManagerName(): void
    {
        $this->assertSame('default', $this->testItem->getDefaultManagerName());
    }

    public function testGetManager(): void
    {
        $manager = $this->testItem->getManager(MergedRegistry::APIE_MANAGER_NAME);
        $this->assertInstanceOf(EntityManager::class, $manager);

        $defaultManager = $this->testItem->getManager();
        $this->assertInstanceOf(EntityManager::class, $defaultManager);
    }

    public function testGetManagers(): void
    {
        $managers = $this->testItem->getManagers();
        $this->assertArrayHasKey('default', $managers);
        $this->assertArrayHasKey(MergedRegistry::APIE_MANAGER_NAME, $managers);
        $this->assertInstanceOf(EntityManager::class, $managers[MergedRegistry::APIE_MANAGER_NAME]);
    }

    public function testResetManager(): void
    {
        $manager = $this->testItem->resetManager();
        $this->assertInstanceOf(EntityManager::class, $manager);
    }

    public function testGetRepository(): void
    {
        $repository = $this->testItem->getRepository(SomeEntity::class);
        $this->assertNotNull($repository);
    }

    public function testGetConnection(): void
    {
        $connection = $this->testItem->getConnection(MergedRegistry::APIE_CONNECTION_NAME);
        $this->assertNotNull($connection);

        $defaultConnection = $this->testItem->getConnection();
        $this->assertNotNull($defaultConnection);
    }
}
