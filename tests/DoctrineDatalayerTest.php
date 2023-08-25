<?php
namespace Apie\Tests\ApieBundle;

use Apie\DoctrineEntityDatalayer\DoctrineEntityDatalayer;
use Apie\DoctrineEntityDatalayer\OrmBuilder;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\Concerns\ItValidatesOpenapi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DoctrineDatalayerTest extends TestCase
{
    use ItCreatesASymfonyApplication;
    use ItValidatesOpenapi;

    /**
     * @test
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
        $repository = $entityManager->getRepository('Generated\\apie_entity_default_user');
        $this->assertCount(1, $repository->findBy([]));
    }
}
