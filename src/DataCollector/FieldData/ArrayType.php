<?php

namespace Apie\ApieBundle\DataCollector\FieldData;

final class ArrayType extends AbstractFieldData
{
    /**
     * @param array<string, AbstractFieldData> $fields
     */
    private function __construct(
        array $fields
    ) {
        $this->typehint = 'array';
        $this->data = $fields;
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function createFromArray(array $input): self
    {
        $fields = [];
        foreach ($input as $key => $value) {
            $fields[$key] = AbstractFieldData::createFromInput($value);
        }

        return new self($fields);
    }

    public function isSame(AbstractFieldData $fieldData): bool
    {
        if (get_class($this) !== get_class($fieldData)) {
            return false;
        }
        if ($this->typehint !== $fieldData->typehint) {
            return false;
        }
        $intersect = array_intersect_key($this->data, $fieldData->data);
        return count($intersect) === count($fieldData->data);
    }


}
