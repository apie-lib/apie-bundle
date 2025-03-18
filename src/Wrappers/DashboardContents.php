<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\HtmlBuilders\ErrorHandler\CmsErrorRenderer;
use Apie\HtmlBuilders\ErrorHandler\StacktraceRenderer;
use Stringable;
use Throwable;
use Twig\Environment;

class DashboardContents implements Stringable
{
    public const NO_TWIG_MESSAGE = 'To configure the dashboard, you require to include symfony/twig-bundle...';

    /**
     * @param array<int|string, mixed> $templateParameters
     */
    public function __construct(
        private readonly ?Environment $twig,
        private readonly string $twigTemplate,
        private readonly array $templateParameters = []
    ) {
    }

    public function __toString(): string
    {
        if ($this->twig === null) {
            $fallback = self::NO_TWIG_MESSAGE;
            // @see CmsErrorRenderer
            if (($this->templateParameters['debugMode'] ?? null) && ($this->templateParameters['error'] ?? null) instanceof Throwable) {
                $fallback = (string) new StacktraceRenderer($this->templateParameters['error']);
            }
            if (file_exists($this->twigTemplate)) {
                return file_get_contents($this->twigTemplate) ? : $fallback;
            }
            return $fallback;
        }
        return $this->twig->render($this->twigTemplate, $this->templateParameters);
    }
}
