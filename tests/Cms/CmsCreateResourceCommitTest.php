<?php
namespace Apie\Tests\ApieBundle\Cms;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Tests\ApieBundle\BoundedContext\Entities\User;
use Apie\Tests\ApieBundle\Concerns\ItCreatesASymfonyApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CmsCreateResourceCommitTest extends TestCase
{
    use ItCreatesASymfonyApplication;

    /**
     * @test
     */
    public function it_can_submit_a_creation_form_for_creating_a_resource(): void
    {
        $testItem = $this->given_a_symfony_application_with_apie();
        $request = Request::create(
            '/cms/default/resource/create/User',
            'POST',
            [],
            [],
            [],
            [],
            http_build_query(['form' => [
                'password' => 'StrongP@s5',
                'phoneNumber' => '0611223344',
            ]])
        );
        $response = $testItem->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/cms/default/resource/User', $response->headers->get('Location'));
        $allUsers = $testItem->getContainer()->get('apie')->all(User::class, new BoundedContextId('default'));
        $this->assertEquals(1, $allUsers->totalCount());
    }
}
