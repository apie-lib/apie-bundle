<?php

namespace Apie\ApieBundle\Security;

use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Common\RequestBodyDecoder;
use Apie\Core\Actions\ActionInterface;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Utils;
use Exception;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApieUserAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly HttpMessageFactoryInterface $httpMessageFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly RequestBodyDecoder $decoder
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->has('_is_apie')
            && $request->attributes->get(ContextConstants::OPERATION_ID)
            && str_starts_with($request->attributes->get(ContextConstants::OPERATION_ID), 'call-method-')
            && 'verifyAuthentication' === $request->attributes->get(ContextConstants::METHOD_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $psrRequest = $this->httpMessageFactory->createRequest($request)
                ->withHeader('Accept', 'application/json');
            $actionClass = $psrRequest->getAttribute(ContextConstants::APIE_ACTION);
            /** @var ActionInterface $action */
            $action = new $actionClass($this->apieFacade);
            $context = $this->contextBuilderFactory->createFromRequest($psrRequest, $psrRequest->getAttributes());
            $actionResponse = $action($context, $this->decoder->decodeBody($psrRequest));

            if ($actionResponse->result instanceof EntityInterface) {
                $userIdentifier = get_class($actionResponse->result)
                    . '/'
                    . $psrRequest->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)
                    . '/'
                    . Utils::toString($actionResponse->result->getId());
                return new SelfValidatingPassport(
                    new UserBadge($userIdentifier),
                    [
                        new RememberMeBadge()
                    ]
                );
            }
        } catch (Exception $error) {
            throw new CustomUserMessageAuthenticationException('Could not authenticate user!', [], 0, $error);
        }
        throw new CustomUserMessageAuthenticationException('Could not authenticate user!');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
