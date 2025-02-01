<?php

namespace Apie\ApieBundle\DataCollector\FieldData;

class UnknownType extends AbstractFieldData
{
    public function __construct(
        mixed $value
    ) {
        $this->data = null;
        $this->typehint = get_debug_type($value);
    }
}
