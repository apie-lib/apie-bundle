<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use Apie\Tests\ApieBundle\BoundedContext\ValueObjects\CompositeObjectExample;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsModifyResourceFormTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_a_form_for_modifying_a_resource(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        /** @var ApieFacade $apie */
        $apie = $testItem->getContainer()->get('apie');
        $entity = new ManyColumns(new DutchPhoneNumber('0611223344'));
        $entity->stringValue = 'string value';
        $entity->intValue = 42;
        $entity->booleanValue = true;
        $entity->floatValue = M_PI;
        $entity->compositeObject = CompositeObjectExample::fromNative([
            'value1' => 'text',
            'value2' => 'another text',
            'value3' => 42,
        ]);
        $entity->nullableCompositeObject = null;
        $apie->persistNew($entity, new BoundedContextId('default'));
        $request = Request::create(
            '/cms/default/resource/edit/ManyColumns/1',
            'GET'
        );
        $response = $testItem->handle($request);
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('-form', $response->getContent());
    }
}
