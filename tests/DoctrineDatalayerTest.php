<?php
namespace Apie\Tests\ApieBundle;

use Apie\Common\Interfaces\ApieFacadeInterface;
use Apie\Core\ApieLib;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\CountryAndPhoneNumber\BritishPhoneNumber;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\DoctrineEntityDatalayer\DoctrineEntityDatalayer;
use Apie\DoctrineEntityDatalayer\OrmBuilder;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\Concerns\ItValidatesOpenapi;
use Apie\TextValueObjects\StrongPassword;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DoctrineDatalayerTest extends TestCase
{
    use ItCreatesASymfonyApplication;
    use ItValidatesOpenapi;

    protected function setUp(): void
    {
        ApieLib::registerValueObject(DutchPhoneNumber::class);
        ApieLib::registerValueObject(BritishPhoneNumber::class);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_store_in_the_database_with_doctrine_datalayer()
    {
        if (!class_exists(DoctrineEntityDatalayer::class)) {
            $this->markTestSkipped('DoctrineEntityDatalayer class is not loaded');
        }

        $testItem = $this->given_a_symfony_application_with_apie(
            false,
            DoctrineEntityDatalayer::class
        );
        $request = Request::create(
            '/api/default/User',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode([
                'password' => 'This-is-Strong-P4ssword#',
                'phoneNumber' => '+31611223344',
            ])
        );
        $response = $testItem->handle($request);
        $this->validateResponse($request, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $ormBuilder = $testItem->getContainer()->get(OrmBuilder::class);
        $this->assertInstanceOf(OrmBuilder::class, $ormBuilder);
        $entityManager = $ormBuilder->createEntityManager();
        $repository = $entityManager->getRepository('Generated\\ApieEntities\\apie_resource__default_user');
        $this->assertCount(1, $repository->findBy([]));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_retrieve_data_with_pagination_from_database()
    {
        if (!class_exists(DoctrineEntityDatalayer::class)) {
            $this->markTestSkipped('DoctrineEntityDatalayer class is not loaded');
        }

        $testItem = $this->given_a_symfony_application_with_apie(
            false,
            DoctrineEntityDatalayer::class
        );
        /** @var ApieFacadeInterface */
        $apie = $testItem->getContainer()->get('apie');
        for ($i = 10; $i < 100; $i++) {
            $user = $this->createUser($i);
            $apie->persistNew($user, new BoundedContextId('default'));
        }
        $request = Request::create(
            '/api/default/User',
            'GET',
            [],
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json'],
        );
        $response = $testItem->handle($request);
        $this->validateResponse($request, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $code = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('totalCount', $code);
        $this->assertEquals(90, $code['totalCount']);
        $this->assertArrayHasKey('list', $code);
        $this->assertCount(20, $code['list']);
        $this->assertArrayHasKey('first', $code);
        $this->assertEquals('/default/User', $code['first']);
        $this->assertArrayHasKey('last', $code);
        $this->assertEquals('/default/User?page=4', $code['last']);
        $this->assertArrayNotHasKey('prev', $code);
        $this->assertArrayHasKey('next', $code);
        $this->assertEquals('/default/User?page=1', $code['next']);
    }

    private function createUser(int $index): User
    {
        return new User(
            new StrongPassword('This-is-Strong-P4ssword#'),
            new DutchPhoneNumber('0611223344'),
            new UserIdentifier('550e8400-e29b-41d4-a716-4466554400' . $index)
        );
    }
}
