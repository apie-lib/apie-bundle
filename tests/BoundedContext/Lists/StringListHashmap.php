<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Lists;

use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringList;

class StringListHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): StringList
    {
        return parent::offsetGet($offset);
    }
}