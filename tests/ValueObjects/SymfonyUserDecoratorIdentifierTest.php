<?php
namespace Apie\Tests\ApieBundle\ValueObjects;

use Apie\ApieBundle\Security\SymfonyUserDecoratorIdentifier;
use Apie\Fixtures\TestHelpers\ValueObjectTestCase;

class SymfonyUserDecoratorIdentifierTest extends ValueObjectTestCase
{
    public static function className(): string
    {
        return SymfonyUserDecoratorIdentifier::class;
    }

    public static function createExampleObject(): object
    {
        return SymfonyUserDecoratorIdentifier::createRandom();
    }

    public static function provideFromNative(): array
    {
        $id = SymfonyUserDecoratorIdentifier::createRandom()->toNative();
        return [
            'regular' => [$id, $id]
        ];
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'string',
            'format' => 'symfonyuserdecoratoridentifier',
        ];
    }
}
