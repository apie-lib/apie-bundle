<?php
namespace Apie\Tests\ApieBundle\DataCollector;

use Apie\ApieBundle\DataCollector\ApieDataCollector;
use Apie\ApieBundle\DataCollector\ContextChange;
use Apie\ApieBundle\DataCollector\FieldData\ArrayType;
use Apie\ApieBundle\DataCollector\FieldData\ScalarType;
use Apie\Core\Context\ApieContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApieDataCollectorTest extends TestCase
{
    #[Test]
    public function it_can_calculate_context_changes(): void
    {
        $testItem = new ApieDataCollector();
        $testItem->startLogApieContext(new ApieContext([]));
        $testItem->logApieContext('TestClass', new ApieContext(['string' => 'value']));
        $testItem->logApieContext('AnotherClass', new ApieContext(['integer' => 12]));
        $testItem->logApieContext('AddListClass', new ApieContext(['integer' => 12, 'list' => [1,2]]));
        $testItem->logApieContext('ModifyListClass', new ApieContext(['integer' => 12, 'list' => [1]]));
        $testItem->startLogApieContext(new ApieContext(['answer-to-the-ultimate-question' => 42]));
        $actual = $testItem->getApieContextChanges();
        $expected = [
                [
                    new ContextChange('-', [], [], []),
                    new ContextChange(
                        'TestClass',
                        ['string' => new ScalarType('value')],
                        [],
                        []
                    ),
                    new ContextChange(
                        'AnotherClass',
                        ['integer' => new ScalarType(12)],
                        ['string' => new ScalarType('value')],
                        []
                    ),
                    new ContextChange(
                        'AddListClass',
                        ['list' => ArrayType::createFromInput([1, 2])],
                        [],
                        []
                    ),
                    new ContextChange(
                        'ModifyListClass',
                        [],
                        [],
                        ['list' => ArrayType::createFromInput([1])]
                    ),
                ],
                [
                    new ContextChange('-', ['answer-to-the-ultimate-question' => new ScalarType(42)], [], [])
                ],
            ];
        $this->assertEquals(
            $expected,
            $actual
        );

        $testItem->collect(
            $this->createMock(Request::class),
            $this->createMock(Response::class)
        );

        $this->assertEquals($expected, $testItem->getApieContextChanges());
    }
}
