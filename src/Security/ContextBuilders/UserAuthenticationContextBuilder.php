<?php

namespace Apie\ApieBundle\Security\ContextBuilders;

use Apie\ApieBundle\Wrappers\SymfonyUserDecoratorFactory;
use Apie\Common\Interfaces\UserDecorator;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\ContextConstants;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Checks the authenticated user used in Symfony and add it to the context.
 */
class UserAuthenticationContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly SymfonyUserDecoratorFactory $factory
    ) {
    }

    public function process(ApieContext $context): ApieContext
    {
        $user = $this->security->getUser();
        if ($user) {
            $context = $context->registerInstance($user);
            if ($user instanceof UserDecorator) {
                $context = $context->withContext(ContextConstants::AUTHENTICATED_USER, $user->getEntity());
            } else {
                $context = $context->withContext(ContextConstants::AUTHENTICATED_USER, $this->factory->create($user));
            }
        }
        $token = $this->security->getToken();
        if ($token) {
            $context = $context->registerInstance($token);
        }

        return $context;
    }
}
