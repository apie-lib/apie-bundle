<?php
namespace Apie\Tests\ApieBundle\BoundedContext\ValueObjects;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use ReflectionClass;

class ManyColumnsIdentifier extends AutoIncrementInteger implements IdentifierInterface
{
    /**
     * @return RefectionClass<EntityInterface>
     */
    public static function getReferenceFor(): ReflectionClass
    {
        return new ReflectionClass(ManyColumns::class);
    }
}
