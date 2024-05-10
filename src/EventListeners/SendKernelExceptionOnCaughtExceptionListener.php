<?php
namespace Apie\ApieBundle\EventListeners;

use Apie\Common\Events\ApieResponseCreated;
use Apie\Core\Actions\ActionResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SendKernelExceptionOnCaughtExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly HttpKernelInterface $kernel,
        private readonly RequestStack $requestStack,
        private readonly ?ProfilerListener $profilerListener = null
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ApieResponseCreated::class => ['onApieResponseCreated'],
        ];
    }

    public function onApieResponseCreated(ApieResponseCreated $event): void
    {
        if (!$this->profilerListener) {
            return;
        }
        $actionResponse = $event->context->getContext(ActionResponse::class, false);
        if ($actionResponse instanceof ActionResponse && isset($actionResponse->error)) {
            $this->profilerListener->onKernelException(
                new ExceptionEvent(
                    $this->kernel,
                    $this->requestStack->getMainRequest(),
                    HttpKernelInterface::MAIN_REQUEST,
                    $actionResponse->error
                )
            );
        }
    }
}
