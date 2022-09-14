<?php

namespace Apie\ApieBundle\Security\ContextBuilders;

use Apie\ApieBundle\Security\ApieUserDecorator;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Symfony\Component\Security\Core\Security;

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
            if ($user instanceof ApieUserDecorator) {
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
