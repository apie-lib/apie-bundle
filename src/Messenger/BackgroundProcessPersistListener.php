<?php
namespace Apie\ApieBundle\Messenger;

use Apie\Common\Events\ApieResourceCreated;
use Apie\Core\BackgroundProcess\SequentialBackgroundProcess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BackgroundProcessPersistListener implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ApieResourceCreated::class => 'onApieResourceCreated'
        ];
    }

    public function onApieResourceCreated(ApieResourceCreated $apieResourceCreated): void
    {
        $resource = $apieResourceCreated->resource;
        if ($resource instanceof SequentialBackgroundProcess) {
            // TODO ApieContext
            $this->bus->dispatch(new RunSequentialProcessMessage($resource->getId()));
        }
    }
}
