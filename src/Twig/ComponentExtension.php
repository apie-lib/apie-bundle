<?php
namespace Apie\ApieBundle\Twig;

use Apie\HtmlBuilders\ErrorHandler\StacktraceRenderer;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Throwable;
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
            new TwigFunction('renderStacktrace', [$this, 'renderStacktrace'], ['is_safe' => ['all']]),
        ];
    }

    public function renderStacktrace(Throwable $throwable): string
    {
        $renderer = new StacktraceRenderer($throwable);
        return (string) $renderer;
    }

    public function renderApieComponent(ComponentInterface $component): string
    {
        return $this->renderer->render($component);
    }
}
