<?php
namespace Apie\ApieBundle\ValueObjects;

use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;

class EntityNamespace implements StringValueObjectInterface, HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^([A-Z][a-zA-Z0-9]*\\\\)+$/';
    }

    protected function convert(string $input): string
    {
        return str_ends_with($input, '\\') ? $input : ($input . '\\');
    }

    public function toClass(string $className): ReflectionClass
    {
        return new ReflectionClass($this->internal . $className);
    }

    public function toMethod(string $className): ReflectionMethod
    {
        return new ReflectionMethod($this->internal . $className, '__invoke');
    }

    public function getClasses(string $path): ReflectionClassList
    {
        $classes = [];
        if (!file_exists($path) || !is_dir($path)) {
            return new ReflectionClassList([]);
        }
        foreach (Finder::create()->in($path)->files()->name('*.php')->depth('== 0') as $file) {
            $classes[] = $this->toClass($file->getBasename('.php'));
        }
        return new ReflectionClassList($classes);
    }

    public function getMethods(string $path): ReflectionMethodList
    {
        $methods = [];
        if (!file_exists($path) || !is_dir($path)) {
            return new ReflectionMethodList([]);
        }
        foreach (Finder::create()->in($path)->files()->name('*.php')->depth('== 0') as $file) {
            $methods[] = $this->toMethod($file->getBasename('.php'));
        }
        return new ReflectionMethodList($methods);
    }
}
