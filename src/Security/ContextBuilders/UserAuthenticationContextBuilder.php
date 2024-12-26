<?php

namespace Apie\ApieBundle\Security\ContextBuilders;

use Apie\Common\Interfaces\UserDecorator;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Checks the authenticated user used in Symfony and add it to the context.
 */
class UserAuthenticationContextBuilder implements ContextBuilderInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function process(ApieContext $context): ApieContext
    {
        $user = $this->security->getUser();
        if ($user) {
            $context = $context->registerInstance($user);
            if ($user instanceof UserDecorator) {
                $context = $context->withContext('authenticated', $user->getEntity());
            }
        }
        $token = $this->security->getToken();
        if ($token) {
            $context = $context->registerInstance($token);
        }

        return $context;
    }
}
