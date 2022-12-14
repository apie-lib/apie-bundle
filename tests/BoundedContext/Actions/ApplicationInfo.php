<?php
namespace Apie\Tests\ApieBundle\BoundedContext\Actions;

use Apie\Fixtures\Enums\ColorEnum;
use Apie\Fixtures\Enums\Gender;
use Apie\Fixtures\Enums\IntEnum;
use Apie\Fixtures\Enums\NoValueEnum;
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

    public function manyArguments(int $input, DtosApplicationInfo $applicationInfo, string $string, DateTime $dateTime, ColorEnum|Gender $colorOrGender, NoValueEnum|IntEnum|ColorEnum $unionTypehint): string
    {
        return 'string';
    }
}
