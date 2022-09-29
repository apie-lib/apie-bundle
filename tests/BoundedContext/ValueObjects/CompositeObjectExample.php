<?php
namespace Apie\Tests\ApieBundle\BoundedContext\ValueObjects;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use ReflectionClass;

class CompositeObjectExample implements ValueObjectInterface
{
    use CompositeValueObject;

    private string $value1;

    private string $value2;

    private int $value3;
}
