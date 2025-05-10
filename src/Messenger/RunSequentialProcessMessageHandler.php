<?php
namespace Apie\ApieBundle\Messenger;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayer;

class RunSequentialProcessMessageHandler
{
    public function __construct(
        private readonly ApieDatalayer $apieDatalayer,
        private readonly ContextBuilderFactory $contextBuilderFactory,
    ) {
    }

    public function __invoke(RunSequentialProcessMessage $message): void
    {
        $boundedContextId = $message->getBoundedContextId();
        $process = $this->apieDatalayer->find($message->getProcessId(), $boundedContextId);
        $context = [];
        if ($boundedContextId) {
            $context[ContextConstants::BOUNDED_CONTEXT_ID] = $boundedContextId->toNative();
            $context[BoundedContextId::class] = $boundedContextId;
        }
            
        $apieContext = $this->contextBuilderFactory->createGeneralContext($context);
        $process->runStep($apieContext);
        $this->apieDatalayer->persistExisting($process, $boundedContextId);
    }
}
