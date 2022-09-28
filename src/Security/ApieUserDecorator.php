<?php

namespace Apie\ApieBundle\Security;

use Apie\ApieBundle\Security\Interfaces\HasRolesInterface;
use Apie\ApieBundle\Security\ValueObjects\ApieUserDecoratorIdentifier;
use Apie\Core\Entities\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Decorator around an Apie entity to tell Symfony we are logged in.
 *
 * @template T of EntityInterface
 */
class ApieUserDecorator implements UserInterface
{
    /**
     * @param ApieUserDecoratorIdentifier<T> $id
     * @param T $entity
     */
    public function __construct(private readonly ApieUserDecoratorIdentifier $id, private readonly EntityInterface $entity)
    {
    }

    /**
     * @return T
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function getRoles(): array
    {
        if ($this->entity instanceof HasRolesInterface) {
            return $this->entity->getRoles()->toArray();
        }
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->id->toNative();
    }
}
