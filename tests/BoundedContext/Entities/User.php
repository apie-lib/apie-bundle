<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Entities;

use Apie\Core\Entities\EntityInterface;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\UserIdentifier;

class User implements EntityInterface
{
    private UserIdentifier $id;

    public function __construct()
    {
        $this->id = UserIdentifier::createRandom();
    }

    public function getId(): UserIdentifier
    {
        return $this->id;
    }
}
