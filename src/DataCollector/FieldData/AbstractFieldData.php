<?php

namespace Apie\ApieBundle\DataCollector\FieldData;

use Apie\ApieBundle\DataCollector\ContextChange;

abstract class AbstractFieldData
{
    protected string $typehint;

    protected mixed $data;

    final public static function createFromInput(mixed $input): AbstractFieldData
    {
        if (is_array($input)) {
            return ArrayType::createFromArray($input);
        }
        if (is_object($input)) {
            return new ObjectType($input);
        }
        if (is_resource($input)) {
            return new ResourceType($input);
        }
        if (is_scalar($input) || is_null($input)) {
            return new ScalarType($input);
        }

        return new UnknownType($input);
    }

    public function isSame(AbstractFieldData $fieldData): bool
    {
        if (get_class($this) !== get_class($fieldData)) {
            return false;
        }
        if ($this->typehint !== $fieldData->typehint) {
            return false;
        }
        return $this->data === $fieldData->data;
    }

    final public function getChanges(string $description, ArrayType $next): ContextChange
    {
        assert($this instanceof ArrayType);
        $removed = array_diff_key($this->data, $next->data);
        $added = array_diff_key($next->data, $this->data);

        $modified = [];
        foreach (array_intersect_key($this->data, $next->data) as $key => $value) {
            if (!$this->data[$key]->isSame($next->data[$key])) {
                $modified[$key] = $next->data;
            }
        }

        return new ContextChange(
            $description,
            $added,
            $removed,
            $modified,
        );
    }
}
