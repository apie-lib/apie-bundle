<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\Common\Interfaces\DashboardContentFactoryInterface;
use Twig\Environment;

class DashboardContentFactory implements DashboardContentFactoryInterface
{
    public function __construct(
        private readonly ?Environment $environment
    ) {
    }

    public function create(
        string $template,
        array $templateParameters = []
    ): DashboardContents {
        return new DashboardContents($this->environment, $template, $templateParameters);
    }
}
