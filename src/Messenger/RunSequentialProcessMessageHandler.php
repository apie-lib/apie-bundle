<?php
namespace Apie\ApieBundle\Messenger;

use Apie\Core\BackgroundProcess\Utils;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
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
        Utils::runBackgroundProcess(
            $message->getProcessId(),
            $message->getBoundedContextId(),
            $this->apieDatalayer,
            $this->contextBuilderFactory
        );
    }
}
