<?php
namespace Apie\Tests\ApieBundle;

final class HtmlOutput
{
    private function __construct()
    {
    }

    public static function writeHtml(string $testCase, string $contents): void
    {
        if (!filter_var(getenv('STORE_HTML_OUTPUT'), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }
        $sluggedName = str_replace(['\\', ':'], '_', $testCase);
        @mkdir(__DIR__ . '/../var/html', 0755, true);
        file_put_contents(__DIR__ . '/../var/html/' . $sluggedName . '.html', $contents);
    }
}
