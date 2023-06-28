<?php

namespace Apie\ApieBundle\Security;

use Apie\Common\Interfaces\HasRolesInterface;
use Apie\Common\Wrappers\AbstractApieUserDecorator;
use Apie\Core\Entities\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Decorator around an Apie entity to tell Symfony we are logged in.
 *
 * @template T of EntityInterface
 * @extends AbstractApieUserDecorator<T>
 */
final class ApieUserDecorator extends AbstractApieUserDecorator implements UserInterface
{
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
