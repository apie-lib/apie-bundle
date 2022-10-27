<?php
namespace Apie\ApieBundle\EventListeners;

use Apie\ApieBundle\Wrappers\DashboardContents;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\HttpStatusCodeException;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class RenderErrorListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly ?ComponentFactory $componentFactory,
        private readonly ?ComponentRendererInterface $componentRenderer,
        private readonly ?Environment $environment,
        private readonly string $cmsBaseUrl,
        private readonly string $errorTemplate
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->attributes->has('_is_apie')) {
            if ($request->attributes->has(ContextConstants::CMS)
                && null !== $this->componentFactory
                && null !== $this->componentRenderer
                && !$event->hasResponse()
            ) {
                $event->setResponse($this->createCmsResponse($event));
            } else {
                $event->setResponse($this->createApiResponse($event));
            }
        }
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            if (str_starts_with($request->getPathInfo(), $this->cmsBaseUrl)
                && null !== $this->componentFactory
                && null !== $this->componentRenderer
                && !$event->hasResponse()
            ) {
                $event->setResponse($this->createCmsResponse($event));
            }
        }
        if ($exception instanceof HttpStatusCodeException) {
            $response = $event->getResponse();
            if ($response) {
                $response->setStatusCode($exception->getStatusCode());
            }
        }
    }

    private function createApiResponse(ExceptionEvent $event): Response
    {
        $error = $event->getThrowable();
        $statusCode = $error instanceof HttpStatusCodeException ? $error->getStatusCode() : 500;
        return new JsonResponse(
            [
                'message' => $error->getMessage(),
            ],
            $statusCode
        );
    }

    private function createCmsResponse(ExceptionEvent $event): Response
    {
        $contents = new DashboardContents($this->environment, $this->errorTemplate, ['error' => $event->getThrowable()]);
        $boundedContextId = null;
        $request = $event->getRequest();
        if ($request->attributes->has(ContextConstants::BOUNDED_CONTEXT_ID)) {
            $boundedContextId = new BoundedContextId($request->attributes->get(ContextConstants::BOUNDED_CONTEXT_ID));
        }
        return new Response(
            $this->componentRenderer->render(
                $this->componentFactory->createWrapLayout(
                    'Error',
                    $boundedContextId,
                    new ApieContext(),
                    $this->componentFactory->createRawContents($contents)
                )
            )
        );
    }
}
