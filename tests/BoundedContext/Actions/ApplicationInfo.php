<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Actions;

use Apie\Fixtures\Enums\ColorEnum;
use Apie\Tests\ApieBundle\BoundedContext\Dtos\ApplicationInfo as DtosApplicationInfo;
use DateTime;

final class ApplicationInfo
{
    public function __invoke(): DtosApplicationInfo
    {
        return new DtosApplicationInfo();
    }

    public function powerOf2(int $input): int
    {
        return $input * $input;
    }

    public function manyArguments(int $input, ApplicationInfo $applicationInfo, string $string, DateTime $dateTime, ColorEnum $color)
    {
        return new DtosApplicationInfo();
    }
}
