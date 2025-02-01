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
}
