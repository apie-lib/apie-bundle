<?php
namespace Apie\Tests\ApieBundle\Security;

use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Fixtures\ValueObjects\Password as StrongPassword;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\Concerns\ItValidatesOpenapi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LoginTest extends TestCase
{
    use ItCreatesASymfonyApplication;
    use ItValidatesOpenapi;

    /** @test */
    public function it_can_login_as_an_user()
    {
        $kernel = $this->given_a_symfony_application_with_apie();
        /** @var ApieFacade $facade */
        $facade = $kernel->getContainer()->get('apie');
        $user = new User(new StrongPassword('Str0ngP#ss'), new DutchPhoneNumber('0611223344'));
        $facade->persistNew(
            $user,
            new BoundedContextId('default')
        );
        $request = Request::create(
            '/api/default/Login/verifyAuthentication',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode([
                'username' => $user->getId(),
                'password' => 'Str0ngP#ss',
            ])
        );
        $response = $kernel->handle($request);
        $this->validateResponse($request, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
