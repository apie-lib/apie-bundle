<?php
namespace Apie\Tests\ApieBundle;

use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\Concerns\ItValidatesOpenapi;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CreateResourceTest extends TestCase
{
    use ItCreatesASymfonyApplication;
    use ItValidatesOpenapi;

    /**
     * @test
     */
    public function it_can_create_a_resource(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
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
    }

    /**
     * @test
     * @dataProvider invalidDataProvider
     */
    public function it_can_throw_a_validation_error(array $input): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/api/default/User',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($input)
        );
        $response = $testItem->handle($request);
        $this->validateResponse($request, $response);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function invalidDataProvider(): Generator
    {
        yield 'missing field and invalid value object' => [
            [
                'phoneNumber' => 'this is not a valid phone number',
            ]
        ];
    }
}
