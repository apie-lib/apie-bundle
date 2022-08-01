<?php
namespace Apie\Tests\ApieBundle\BoundedContext\ValueObjects;

use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Identifiers\UuidV4;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use ReflectionClass;

class UserIdentifier extends UuidV4 implements IdentifierInterface
{
    /**
     * @return RefectionClass<EntityInterface>
     */
    public static function getReferenceFor(): ReflectionClass
    {
        return new ReflectionClass(User::class);
    }
}
