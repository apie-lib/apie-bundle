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

/**
 * Value object that represents a PHP class namespace.
 *
 * Example values:
 * "Apie\Common\"
 * "Symfony\Component\"
 */
final class EntityNamespace implements StringValueObjectInterface, HasRegexValueObjectInterface
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

    /**
     * @return ReflectionClass<object>
     */
    public function toClass(string $className): ReflectionClass
    {
        return new ReflectionClass($this->internal . $className);
    }

    /**
     * Returns all classes found in $path assuming the namespace of this value object.
     */
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

    /**
     * Returns all non-magic methods found in $path assuming the namespace of this value object.
     */
    public function getMethods(string $path): ReflectionMethodList
    {
        $methods = [];
        if (!file_exists($path) || !is_dir($path)) {
            return new ReflectionMethodList([]);
        }
        foreach (Finder::create()->in($path)->files()->name('*.php')->depth('== 0') as $file) {
            $class = $this->toClass($file->getBasename('.php'));
            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (strpos($method->name, '__') !== 0 || $method->name === '__invoke') {
                    $methods[] = $method;
                }
            }
        }
        return new ReflectionMethodList($methods);
    }
}
