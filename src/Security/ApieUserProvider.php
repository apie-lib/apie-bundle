<?php

namespace Apie\ApieBundle\Security;

use Apie\Common\ApieFacade;
use Apie\Common\Interfaces\CheckLoginStatusInterface;
use Apie\Common\Wrappers\ApieUserDecoratorIdentifier;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\ValueObjects\Utils;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<ApieUserDecorator>
 */
class ApieUserProvider implements UserProviderInterface
{
    public function __construct(private readonly ApieFacade $apieFacade)
    {
    }

    /**
     * @template T of EntityInterface
     * @param ApieUserDecorator<T> $user
     * @return ApieUserDecorator<T>|null
     */
    public function refreshUser(UserInterface $user): ?ApieUserDecorator
    {
        if (!$user instanceof ApieUserDecorator) {
            throw new UnsupportedUserException(get_debug_type($user) . ' is not supported');
        }
        $entity = $user->getEntity();
        if ($entity instanceof CheckLoginStatusInterface) {
            $user = $this->loadUserByIdentifier($user->getUserIdentifier());
            $entity = $user->getEntity();
            if (!($entity instanceof CheckLoginStatusInterface) || $entity->isDisabled()) {
                return null;
            }
        }
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === ApieUserDecorator::class;
    }

    /**
     * @return ApieUserDecorator<EntityInterface>
     */
    public function loadUserByIdentifier(string $identifier): ApieUserDecorator
    {
        $identifier = new ApieUserDecoratorIdentifier($identifier);
        $boundedContextId = $identifier->getBoundedContextId();
        try {
            $entity = $this->apieFacade->find($identifier->getIdentifier(), $boundedContextId);
        } catch (EntityNotFoundException $notFound) {
            throw new UserNotFoundException(
                'User ' . Utils::toString($identifier->getIdentifier()) . ' is removed, logging out',
                0,
                $notFound
            );
        }
        return new ApieUserDecorator($identifier, $entity);
    }
}
