<?php
namespace Apie\ApieBundle\ContextBuilders;

use Apie\Common\ContextConstants;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\Exceptions\InvalidCsrfTokenException;
use Apie\Core\Session\CsrfTokenProvider;
use Apie\Core\Session\FakeTokenProvider;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfTokenContextBuilder implements ContextBuilderInterface, CsrfTokenProvider
{
    private string $tokenName = 'apie,apie';

    public function __construct(private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null)
    {
    }

    public function process(ApieContext $context): ApieContext
    {
        if (null === $this->csrfTokenManager) {
            return $context->withContext(CsrfTokenProvider::class, new FakeTokenProvider());
        }
        $this->tokenName = $context->hasContext(ContextConstants::RESOURCE_NAME)
            ? $context->getContext(ContextConstants::RESOURCE_NAME)
            : 'apie';
        $this->tokenName .= ',';
        $this->tokenName .=  $context->hasContext(ContextConstants::APIE_ACTION)
            ? $context->getContext(ContextConstants::APIE_ACTION)
            : 'apie';
        return $context->withContext(CsrfTokenProvider::class, $this);
    }

    public function createToken(): string
    {
        return $this->csrfTokenManager->getToken($this->tokenName)->getValue();
    }

    public function validateToken(string $token): void
    {
        if ($this->csrfTokenManager) {
            $csrfToken = new CsrfToken($this->tokenName, $token);
            if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
                throw new InvalidCsrfTokenException();
            }
            $this->csrfTokenManager->removeToken($this->tokenName);
        } else {
            (new FakeTokenProvider())->validateToken($token);
        }
    }
}
