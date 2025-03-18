<?php

namespace Apie\ApieBundle\Security;

use Apie\Core\Lists\PermissionList;
use Apie\Core\Permissions\PermissionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonyUserWithPermissionDecorator extends SymfonyUserDecorator implements PermissionInterface
{
    public function __construct(UserInterface&PermissionInterface $user)
    {
        parent::__construct($user);
    }

    public function getPermissionIdentifiers(): PermissionList
    {
        $user = $this->getUser();
        assert($user instanceof PermissionInterface);
        return $user->getPermissionIdentifiers();
    }
}
