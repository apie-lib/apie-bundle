<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Entities\EntityInterface;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\ManyColumnsIdentifier;

class ManyColumns implements EntityInterface
{
    public string $stringValue;

    public int $intValue;

    public bool $booleanValue;

    public float $floatValue;

    public ?string $nullableStringValue = null;

    public ?int $nullableIntValue = null;

    public ?bool $nullableBooleanValue = null;

    public ?float $nullableFloatValue = null;

    public function __construct(private ManyColumnsIdentifier $id)
    {
    }

    public function getId(): ManyColumnsIdentifier
    {
        return $this->id;
    }
}
