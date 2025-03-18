<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\Common\Wrappers\GeneralServiceFactory;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Serializer\DecoderHashmap;

class GeneralServiceFactoryWithDataCollector
{
    public function __construct(private readonly ApieDataCollector $apieDataCollector)
    {
    }

    /**
     * @param iterable<int, ContextBuilderInterface> $contextBuilders
     */
    public function createContextBuilderFactory(
        BoundedContextHashmap $boundedContextHashmap,
        ?DecoderHashmap $decoderHashmap,
        iterable $contextBuilders
    ): ContextBuilderFactory {
        $contextBuilders = $this->apieDataCollector->wrapContextBuilders(
            [...$contextBuilders]
        );
        return GeneralServiceFactory::createContextBuilderFactory(
            $boundedContextHashmap,
            $decoderHashmap,
            $contextBuilders
        );
    }
}
