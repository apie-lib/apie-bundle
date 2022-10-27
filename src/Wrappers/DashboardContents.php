<?php
namespace Apie\ApieBundle\Wrappers;

use Stringable;
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
            if (file_exists($this->twigTemplate)) {
                return file_get_contents($this->twigTemplate) ? : self::NO_TWIG_MESSAGE;
            }
            return self::NO_TWIG_MESSAGE;
        }
        return $this->twig->render($this->twigTemplate, $this->templateParameters);
    }
}
