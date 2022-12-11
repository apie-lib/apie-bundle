<?php
namespace Apie\ApieBundle\ContextBuilders;

use Apie\Common\ContextConstants;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\Utils;
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
            $session = $request->getSession();
            $context = $context->withContext(SessionInterface::class, $session);
            // TODO: move to its own context builder
            if ($context->getContext(RequestMethod::class) === RequestMethod::GET) {
                if ($session->has('_filled_in')) {
                    $context = $context->withContext(
                        ContextConstants::RAW_CONTENTS,
                        Utils::toArray($session->get('_filled_in', []))
                    );
                }
                $context = $context->withContext(
                    ContextConstants::VALIDATION_ERRORS,
                    Utils::toArray($session->get('_validation_errors', []))
                );
                // TODO: unset from session
            }
        }

        return $context;
    }
}
