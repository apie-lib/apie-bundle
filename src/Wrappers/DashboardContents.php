<?php
namespace Apie\ApieBundle\Wrappers;

use Stringable;
use Twig\Environment;

class DashboardContents implements Stringable
{
    public const NO_TWIG_MESSAGE = 'To configure the dashboard, you require to include symfony/twig-bundle...';

    public function __construct(private readonly ?Environment $twig, private readonly string $twigTemplate)
    {
    }

    public function __toString(): string
    {
        if ($this->twig === null) {
            if (file_exists($this->twigTemplate)) {
                return file_get_contents($this->twigTemplate) ? : self::NO_TWIG_MESSAGE;
            }
            return self::NO_TWIG_MESSAGE;
        }
        return $this->twig->render($this->twigTemplate);
    }
}
