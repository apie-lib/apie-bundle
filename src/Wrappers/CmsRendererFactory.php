<?php
namespace Apie\ApieBundle\Wrappers;

use Apie\CmsLayoutGraphite\GraphiteDesignSystemLayout;
use Apie\HtmlBuilders\Assets\AssetManager;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;

final class CmsRendererFactory
{
    private function __construct()
    {
    }

    public static function createRenderer(?AssetManager $assetManager): ComponentRendererInterface
    {
        if (class_exists(GraphiteDesignSystemLayout::class)) {
            return GraphiteDesignSystemLayout::createRenderer($assetManager);
        }
        $contents = file_get_contents(__DIR__ . '/../../resources/html/install-instructions-cms-renderer.html');
        return new class($contents) implements ComponentRendererInterface {
            public function __construct(private string $contents)
            {
            }

            public function render(ComponentInterface $componentInterface): string
            {
                return $this->contents;
            }
        };
    }
}
