<?php
namespace Apie\ApieBundle\DependencyInjection\Compiler;

use Apie\ApieBundle\Wrappers\BoundedContextHashmapFactory;
use Apie\Core\Exceptions\InvalidTypeException;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Actions ony work properly in a Symfony application if they are services with the
 * tag 'apie.context'.
 *
 * This compiler pass makes sure they exist as service definition and contain the tag.
 */
class AutoTagActionsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $boundedContextConfig = $container->getParameter('apie.bounded_contexts');
        $factory = new BoundedContextHashmapFactory($boundedContextConfig);
        $hashmap = $factory->create();
        foreach ($hashmap as $boundedContext) {
            foreach ($boundedContext->actions as $action) {
                $class = $action->getDeclaringClass();
                if (!$class->isInstantiable()) {
                    continue;
                }
                $className = $class->name;
                if (!$container->hasDefinition($className)) {
                    $container->addDefinitions([
                        $className => $this->createDefinition($class)
                    ]);
                }
                $definition = $container->getDefinition($className);
                $tag = $definition->getTag('apie.context');
                if (empty($tag)) {
                    $definition->addTag('apie.context');
                }
            }
        }
    }

    /**
     * @param ReflectionClass<object> $refl
     */
    private function createDefinition(ReflectionClass $refl): Definition
    {
        $arguments = [];
        $constructor = $refl->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType) {
                    $arguments[] = new Reference($type->getName());
                } else {
                    throw new InvalidTypeException($type, 'ReflectionNamedType');
                }
            }
        }
        return new Definition($refl->name, $arguments);
    }
}
