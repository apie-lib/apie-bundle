<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Actions;

use Apie\Tests\ApieBundle\BoundedContext\Dtos\ApplicationInfo as DtosApplicationInfo;

final class ApplicationInfo
{
    public function __invoke(): DtosApplicationInfo
    {
        return new DtosApplicationInfo();
    }
}
