<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Actions;

use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;
use Exception;
use LogicException;

final class Login {
    public function __construct(private readonly ApieFacade $apieFacade)
    {
    }

    public function verifyAuthentication(string $username, string $password): ?User
    {
        try {
            $user = $this->apieFacade->find(new UserIdentifier($username), new BoundedContextId('default'));
        } catch (Exception $error) {
            return null;
        }
        if ($user->verifyAuthentication($password)) {
            return $user;
        }
        return null;
    }
}