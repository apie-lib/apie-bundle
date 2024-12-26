<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Tests\ApieBundle\ApieBundleTestingKernel;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\CompositeObjectExample;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\ManyColumnsIdentifier;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsResourceOverviewTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    private function hydrateManyRecords(ApieBundleTestingKernel $kernel): void
    {
        /** @var ApieFacade $facade */
        $facade = $kernel->getContainer()->get('apie');
        for ($i = 0; $i < 100; $i++) {
            $object = new ManyColumns(new DutchPhoneNumber('0611223344'), new ManyColumnsIdentifier($i));
            $object->stringValue = 'This is text ' . $i;
            $object->intValue = $i * $i;
            $object->booleanValue = ($i & 1) ? true : false;
            $object->floatValue = 1 / (1 + $i);
            if ($i < 50) {
                $object->nullableStringValue = 'This is text ' . $i;
                $object->nullableIntValue = $i * $i;
                $object->nullableBooleanValue = ($i & 1) ? true : false;
                $object->nullableFloatValue = 1 / (2 + $i);
            }
            $object->compositeObject = CompositeObjectExample::fromNative(
                [
                    'value1' => '1',
                    'value2' => '2',
                    'value3' => '12',
                ]
            );
            $object->nullableCompositeObject = CompositeObjectExample::fromNative(
                [
                    'value1' => 'fwqwqfwq1',
                    'value2' => '2fwqfwq',
                    'value3' => $i * $i * $i,
                ]
            );
            $facade->persistNew($object, new BoundedContextId('default'));
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_a_table_with_users(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $this->hydrateManyRecords($testItem);
        $request = Request::create(
            '/cms/default/resource/ManyColumns',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('ManyColumns overview', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_no_pagination_with_few_records(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/resource/ManyColumns',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('ManyColumns overview', $response->getContent());
        $this->assertStringNotContainsString('Shown', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_supports_pagination(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $this->hydrateManyRecords($testItem);
        $request = Request::create(
            '/cms/default/resource/ManyColumns?page=2',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('ManyColumns overview', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }
}
