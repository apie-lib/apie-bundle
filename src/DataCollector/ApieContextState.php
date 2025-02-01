<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\ApieBundle\DataCollector\FieldData\AbstractFieldData;
use Apie\ApieBundle\DataCollector\FieldData\ArrayType;
use Apie\Core\Context\ApieContext;

final class ApieContextState
{

    /**
     * @param array{0: class-string<object>|null, 1: AbstractFieldData}[] $states
     */
    private function __construct(
        private array $states = []
    ) {
    }
    public static function createFromApieContext(ApieContext $apieContext): ApieContextState
    {
        $context = (new \ReflectionProperty(ApieContext::class, 'context'))->getValue($apieContext);
        $states = [[null, AbstractFieldData::createFromInput($context)]];
        return new self($states);
    }

    public function logNextContext(string $className, ApieContext $apieContext): ApieContextState
    {
        $context = (new \ReflectionProperty(ApieContext::class, 'context'))->getValue($apieContext);
        $this->states[] = [$className, AbstractFieldData::createFromInput($context)];
        return $this;
    }

    /**
     * @return array<int, ContextChange>
     */
    public function getContextChanges(): array
    {
        $previous = AbstractFieldData::createFromInput([]);
        assert($previous instanceof ArrayType);
        $diff = [];
        foreach ($this->states as $state) {
            assert($state[1] instanceof ArrayType);
            $diff[] = $previous->getChanges($state[0] ?? '-', $state[1]);
            $previous = $state[1];
        }
        return $diff;
    }
}
