<?php

namespace Apie\ApieBundle\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\ContextConstants;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class LogoutUrlContextBuilder implements ContextBuilderInterface
{

    public function __construct(
        private Security $security,
        private LogoutUrlGenerator $logoutUrlGenerator,
        private RequestStack $requestStack
    ) {
    }

    public function process(ApieContext $context): ApieContext
    {
        $firewall = null;
        $masterRequest = $this->requestStack->getMainRequest();

        try {
            if ($masterRequest) {
                $firewall = $this->security->getFirewallConfig($masterRequest)?->getName();
            }
            return $context->withContext(ContextConstants::LOGOUT_URL, $this->logoutUrlGenerator->getLogoutUrl($firewall));
        } catch (\LogicException) {
            return $context;
        }
    }
}
