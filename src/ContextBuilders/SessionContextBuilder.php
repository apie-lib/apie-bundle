<?php
namespace Apie\ApieBundle\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\Session\CsrfTokenProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionContextBuilder implements ContextBuilderInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function process(ApieContext $context): ApieContext
    {
        $request = $this->requestStack->getMainRequest();
        if ($request && $request->hasSession()) {
            return $context->withContext(SessionInterface::class, $request->getSession());
        }

        return $context;
    }
}