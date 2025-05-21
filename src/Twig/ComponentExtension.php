<?php
namespace Apie\ApieBundle\Twig;

use Apie\Common\Wrappers\TemplateRenderFunctions;
use Apie\HtmlBuilders\Factories\FieldDisplayComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ComponentExtension extends AbstractExtension
{
    use TemplateRenderFunctions;
    
    public function __construct(
        private readonly ComponentRendererInterface $renderer,
        private readonly FieldDisplayComponentFactory $fieldDisplayComponentFactory
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderApieComponent', [$this, 'renderApieComponent'], ['is_safe' => ['all']]),
            new TwigFunction('renderStacktrace', [$this, 'renderStacktrace'], ['is_safe' => ['all']]),
            new TwigFunction('renderApieCmsData', [$this, 'renderApieCmsData'], ['is_safe' => ['all']])
        ];
    }
}
