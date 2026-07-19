<?php
namespace Apie\ApieBundle\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\ContextConstants;
use Symfony\Component\Translation\LocaleSwitcher;

final class SymfonyLocaleContextBuilder implements ContextBuilderInterface
{
    public function __construct(private readonly LocaleSwitcher $localeSwitcher)
    {
    }

    public function process(ApieContext $context): ApieContext
    {
        $locale = $this->localeSwitcher->getLocale();
        if (!$locale) {
            return $context;
        }

        if (!$context->hasContext(ContextConstants::LOCALE)) {
            $context = $context->withContext(ContextConstants::LOCALE, $locale);
        }
        if (!$context->hasContext(ContextConstants::ACCEPT_LOCALE)) {
            $context = $context->withContext(ContextConstants::ACCEPT_LOCALE, $locale);
        }
        if (!$context->hasContext(ContextConstants::DATA_LOCALE)) {
            $context = $context->withContext(ContextConstants::DATA_LOCALE, $locale);
        }

        return $context;
    }
}
