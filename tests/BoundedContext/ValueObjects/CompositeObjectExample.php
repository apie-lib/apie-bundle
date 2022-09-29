<?php
namespace Apie\Tests\ApieBundle\BoundedContext\ValueObjects;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;

class CompositeObjectExample implements ValueObjectInterface
{
    use CompositeValueObject;

    private string $value1;

    private string $value2;

    private int $value3;
}
