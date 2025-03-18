<?php

namespace Apie\ApieBundle\DataCollector\FieldData;

class ScalarType extends AbstractFieldData
{
    public function __construct(
        int|bool|null|float|string $value
    ) {
        $this->data = $value;
        $this->typehint = get_debug_type($value);
    }

    public function __toString(): string
    {
        return json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
