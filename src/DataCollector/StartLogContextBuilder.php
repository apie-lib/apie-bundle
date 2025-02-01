<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;

class StartLogContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private readonly ApieDataCollector $apieDataCollector
    ) {
    }

    public function process(ApieContext $context): ApieContext
    {
        $this->apieDataCollector->startLogApieContext($context);
        return $context;
    }
}
