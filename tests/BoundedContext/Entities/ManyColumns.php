<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\StringList;
use Apie\CountryAndPhoneNumber\BelgianPhoneNumber;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Fixtures\Lists\StrongPasswordList;
use Apie\Tests\ApieBundle\BoundedContext\Lists\AnimalList;
use Apie\Tests\ApieBundle\BoundedContext\Lists\StringListHashmap;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\CompositeObjectExample;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\ManyColumnsIdentifier;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;

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

    public CompositeObjectExample|AnimalList|int|null $conflictingTypes = null;

    public AnimalList $animalList;

    public ?AnimalList $nullableAnimalList = null;

    public CompositeObjectExample $compositeObject;

    public ?CompositeObjectExample $nullableCompositeObject;

    public StrongPasswordList $passwordList;

    public ?StrongPasswordList $nullablePasswordList = null;

    public ?UserIdentifier $userIdentifier = null;

    public function __construct(
        public DutchPhoneNumber|BelgianPhoneNumber $phonenumber,
        private ?ManyColumnsIdentifier $id = null
    ) {
        $this->id ??= new ManyColumnsIdentifier(null);
        $this->stringList = new StringList();
        $this->stringListHashmap = new StringListHashmap();
        $this->animalList = new AnimalList();
        $this->passwordList = new StrongPasswordList();
    }

    public function getId(): ManyColumnsIdentifier
    {
        return $this->id;
    }
}
