<?php
namespace Apie\ApieBundle\Twig;

use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ComponentExtension extends AbstractExtension
{
    public function __construct(private readonly ComponentRendererInterface $renderer)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderApieComponent', [$this, 'renderApieComponent'], ['is_safe' => ['all']]),
        ];
    }

    public function renderApieComponent(ComponentInterface $component): string
    {
        return $this->renderer->render($component);
    }
}
