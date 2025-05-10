<?php
namespace Apie\ApieBundle\Messenger;

use Apie\Core\BackgroundProcess\SequentialBackgroundProcessIdentifier;
use Apie\Core\BoundedContext\BoundedContextId;

class RunSequentialProcessMessage
{
    public function __construct(
        private SequentialBackgroundProcessIdentifier $processId,
        private ?BoundedContextId $boundedContextId = null,
    ) {
    }

    public function getProcessId(): SequentialBackgroundProcessIdentifier
    {
        return $this->processId;
    }

    public function getBoundedContextId(): ?BoundedContextId
    {
        return $this->boundedContextId;
    }
}
