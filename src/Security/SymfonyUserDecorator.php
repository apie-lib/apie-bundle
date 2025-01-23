<?php

namespace Apie\ApieBundle\Security;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonyUserDecorator implements EntityInterface
{
    private SymfonyUserDecoratorIdentifier $id;
    public function __construct(private readonly UserInterface $user)
    {
        $this->id = SymfonyUserDecoratorIdentifier::createFrom($user);
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }


    public function getId(): SymfonyUserDecoratorIdentifier
    {
        return $this->id;
    }
}