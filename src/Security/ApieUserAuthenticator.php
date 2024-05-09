<?php

namespace Apie\ApieBundle\Security;

use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Common\Events\AddAuthenticationCookie;
use Apie\Common\RequestBodyDecoder;
use Apie\Common\ValueObjects\DecryptedAuthenticatedUser;
use Apie\Core\Actions\ActionInterface;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\Entities\EntityInterface;
use Apie\Serializer\Exceptions\ValidationException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        private readonly RequestBodyDecoder $decoder,
        private readonly LoggerInterface $logger
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->cookies->has(AddAuthenticationCookie::COOKIE_NAME)
            || $this->isVerifyAuthenticationAction($request);
    }

    private function isVerifyAuthenticationAction(Request $request): bool
    {
        return $request->attributes->has('_is_apie')
            && in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])
            && $request->attributes->get(ContextConstants::OPERATION_ID)
            && str_starts_with($request->attributes->get(ContextConstants::OPERATION_ID), 'call-method-')
            && 'verifyAuthentication' === $request->attributes->get(ContextConstants::METHOD_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        $psrRequest = $this->httpMessageFactory->createRequest($request)
                ->withHeader('Accept', 'application/json');
        $context = $this->contextBuilderFactory->createFromRequest($psrRequest, $psrRequest->getAttributes());
        try {
            $decryptedUserId = $context->getContext(DecryptedAuthenticatedUser::class, false);
            $loginAction = $this->isVerifyAuthenticationAction($request);
            if ($decryptedUserId instanceof DecryptedAuthenticatedUser) {
                if ($decryptedUserId->isExpired()) {
                    throw new \LogicException('Token is expired!');
                }
                if (!$loginAction) {
                    return new SelfValidatingPassport(
                        new UserBadge($decryptedUserId->toNative()),
                        [
                            new RememberMeBadge()
                        ]
                    );
                }
            }
            if ($loginAction) {
                $actionClass = $psrRequest->getAttribute(ContextConstants::APIE_ACTION);
                /** @var ActionInterface $action */
                $action = new $actionClass($this->apieFacade);
                $actionResponse = $action($context, $this->decoder->decodeBody($psrRequest));
                if ($actionResponse->result instanceof EntityInterface) {
                    $decryptedUserId = DecryptedAuthenticatedUser::createFromEntity(
                        $actionResponse->result,
                        new BoundedContextId($psrRequest->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)),
                        time() + 3600
                    );
                    $request->attributes->set(ContextConstants::AUTHENTICATED_USER, $actionResponse->result);
                    $request->attributes->set(DecryptedAuthenticatedUser::class, $decryptedUserId);
                    $request->attributes->set(ContextConstants::ALREADY_CALCULATED, $actionResponse);
                    return new SelfValidatingPassport(
                        new UserBadge($decryptedUserId->toNative()),
                        [
                            new RememberMeBadge()
                        ]
                    );
                }
                if ($actionResponse->result instanceof ValidationException) {
                    throw $actionResponse->result;
                }
            }
        } catch (Exception $error) {
            $this->logger->error('Could not authenticate user: ' . $error->getMessage(), ['error' => $error]);
            throw new CustomUserMessageAuthenticationException('Could not authenticate user!', [], 0, $error);
        }
        $this->logger->error('Could not authenticate user, but no exception was thrown');
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
