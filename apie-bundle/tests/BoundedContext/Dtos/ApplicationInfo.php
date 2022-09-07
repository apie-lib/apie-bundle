<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Dtos;

use Apie\Core\Dto\DtoInterface;

class ApplicationInfo implements DtoInterface
{
    public function __construct(
        public readonly string $applicationName = 'Apie test REST API',
        public readonly string $applicationVersion = '1.2.3',
        public readonly string $author = 'Pieter Jordaan'
    ) {
    }
}
