<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\StringList;
use Apie\Tests\ApieBundle\BoundedContext\Lists\AnimalList;
use Apie\Tests\ApieBundle\BoundedContext\Lists\StringListHashmap;
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

    public StringList $stringList;

    public ?StringList $nullableStringList = null;

    public StringListHashmap $stringListHashmap;

    public ?StringListHashmap $nullableStringListHashmap = null;

    public AnimalList $animalList;

    public ?AnimalList $nullableAnimalList = null;

    public function __construct(private ManyColumnsIdentifier $id)
    {
        $this->stringList = new StringList();
        $this->stringListHashmap = new StringListHashmap();
        $this->animalList = new AnimalList();
    }

    public function getId(): ManyColumnsIdentifier
    {
        return $this->id;
    }
}
