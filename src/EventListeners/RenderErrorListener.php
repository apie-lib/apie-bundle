<?php
namespace Apie\ApieBundle\EventListeners;

use Apie\Common\ContextConstants;
use Apie\Common\ErrorHandler\ApiErrorRenderer;
use Apie\Common\IntegrationTestLogger;
use Apie\Core\Exceptions\HttpStatusCodeException;
use Apie\HtmlBuilders\ErrorHandler\CmsErrorRenderer;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RenderErrorListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly ?CmsErrorRenderer $cmsErrorRenderer,
        private readonly ApiErrorRenderer $apiErrorRenderer,
        private readonly LoggerInterface $logger,
        private readonly string $cmsBaseUrl
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->logger->error(
            'An error occured in Symfony: ' . $event->getThrowable()->getMessage(),
            ['error' => $event->getThrowable()]
        );
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->attributes->has('_is_apie')) {
            if ($request->attributes->has(ContextConstants::CMS)
                && null !== $this->cmsErrorRenderer
                && !$event->hasResponse()
            ) {
                $event->setResponse($this->cmsErrorRenderer->createCmsResponse($event->getRequest(), $event->getThrowable()));
            } else {
                $event->setResponse($this->apiErrorRenderer->createApiResponse($event->getThrowable()));
            }
        }
        $exception = $event->getThrowable();
        IntegrationTestLogger::logException($exception);
        if ($exception instanceof NotFoundHttpException) {
            if (str_starts_with($request->getPathInfo(), $this->cmsBaseUrl)
                && null !== $this->cmsErrorRenderer
                && !$event->hasResponse()
            ) {
                $event->setResponse($this->cmsErrorRenderer->createCmsResponse($event->getRequest(), $event->getThrowable()));
            }
        }
        if ($exception instanceof HttpStatusCodeException) {
            $response = $event->getResponse();
            if ($response) {
                $response->setStatusCode($exception->getStatusCode());
            }
        }
    }
}
