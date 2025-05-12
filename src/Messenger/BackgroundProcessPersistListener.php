<?php
namespace Apie\ApieBundle\Messenger;

use Apie\Core\BackgroundProcess\SequentialBackgroundProcess;
use Apie\Core\Datalayers\Events\EntityPersisted;
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
            EntityPersisted::class => 'onApieResourceUpdated'
        ];
    }

    public function onApieResourceUpdated(EntityPersisted $apieResourceCreated): void
    {
        $resource = $apieResourceCreated->entity;
        if ($resource instanceof SequentialBackgroundProcess) {
            $this->bus->dispatch(new RunSequentialProcessMessage(
                $resource->getId(),
                $apieResourceCreated->boundedContextId
            ));
        }
    }
}
