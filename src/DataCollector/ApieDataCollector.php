<?php

namespace Apie\ApieBundle\DataCollector;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApieDataCollector extends AbstractDataCollector
{

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        // TODO: Implement collect() method.
    }
}
