<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApieDataCollector extends AbstractDataCollector
{
    private ApieContextState $currentState;

    /**
     * @param array<int, ContextBuilderInterface> $items
     * @return \Generator<int, ContextBuilderInterface>
     */
    private function wrap(array $items): \Generator
    {
        yield new StartLogContextBuilder($this);
        foreach ($items as $item) {
            yield $item;
            yield new LogContextBuilder(get_class($item), $this);
        }
    }

    /**
     * @param array<int, ContextBuilderInterface> $contextBuilders
     * @return array<int, ContextBuilderInterface>
     */
    public function wrapContextBuilders(array $contextBuilders): array
    {
        return iterator_to_array($this->wrap($contextBuilders));
    }

    public function startLogApieContext(ApieContext $apieContext): void
    {
        $this->currentState = ApieContextState::createFromApieContext($apieContext);
        $this->data['apie'][] = $this->currentState;
    }

    public function logApieContext(string $className, ApieContext $apieContext): void
    {
        $this->currentState->logNextContext($className, $apieContext);
    }

    /**
     * @return array<int, array<int, ContextChange>>
     */
    public function getApieContextChanges(): array
    {
        $result = [];
        foreach ($this->data['apie'] ?? [] as $contextList) {
            $result[] = $contextList->getContextChanges();
        }
        return $result;
    }
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        unset($this->currentState);
    }
}
