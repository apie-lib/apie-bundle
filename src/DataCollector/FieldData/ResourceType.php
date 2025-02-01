<?php

namespace Apie\ApieBundle\DataCollector\FieldData;

class ResourceType extends AbstractFieldData
{
    /**
     * @param resource $value
     */
    public function __construct(
        mixed $value
    ) {
        assert(is_resource($value));
        $this->data = get_resource_id($value);
        $this->typehint = 'resource';
    }
}
