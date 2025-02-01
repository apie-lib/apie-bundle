<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;

class LogContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private readonly string $contextBuilderClass,
        private readonly ApieDataCollector $apieDataCollector
    ) {
    }

    public function process(ApieContext $context): ApieContext
    {
        $this->apieDataCollector->logApieContext($this->contextBuilderClass, $context);
        return $context;
    }
}
