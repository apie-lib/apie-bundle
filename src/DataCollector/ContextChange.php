<?php

namespace Apie\ApieBundle\DataCollector;

use Apie\ApieBundle\DataCollector\FieldData\AbstractFieldData;
use Apie\Core\Dto\DtoInterface;

class ContextChange implements DtoInterface
{
    /**
     * @param array<string, AbstractFieldData> $added
     * @param array<string, AbstractFieldData> $removed
     * @param array<string, AbstractFieldData> $modified
     */
    public function __construct(
        public readonly ?string $name,
        public readonly array $added,
        public readonly array $removed,
        public readonly array $modified
    ) {
    }
}
