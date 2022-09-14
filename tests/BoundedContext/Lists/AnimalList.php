<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Lists;

use Apie\Core\Lists\ItemList;
use Apie\Fixtures\Entities\Polymorphic\Animal;

class AnimalList extends ItemList
{
    public function offsetGet(mixed $offset): Animal
    {
        return parent::offsetGet($offset);
    }
}
