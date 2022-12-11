<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Tests\ApieBundle\BoundedContext\Entities\ManyColumns;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use Apie\Tests\ApieBundle\HtmlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsModifyResourceFormTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
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
        $apie->persistNew($entity, new BoundedContextId('default'));
        $request = Request::create(
            '/cms/default/resource/edit/ManyColumns/1',
            'GET'
        );
        $response = $testItem->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('<form', $response->getContent());
        HtmlOutput::writeHtml(__METHOD__, $response->getContent());
    }
}