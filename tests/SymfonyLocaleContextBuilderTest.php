<?php
namespace Apie\Tests\ApieBundle;

use Apie\ApieBundle\ContextBuilders\SymfonyLocaleContextBuilder;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\LocaleSwitcher;

final class SymfonyLocaleContextBuilderTest extends TestCase
{
    public function test_sets_locale_context_from_symfony_request_default_locale(): void
    {
        $builder = new SymfonyLocaleContextBuilder(new LocaleSwitcher('nl_BE', []));
        $context = $builder->process(new ApieContext([]));

        $this->assertSame('nl_BE', $context->getContext(ContextConstants::LOCALE));
        $this->assertSame('nl_BE', $context->getContext(ContextConstants::ACCEPT_LOCALE));
        $this->assertSame('nl_BE', $context->getContext(ContextConstants::DATA_LOCALE));
    }

    public function test_no_locale_context_from_symfony_request_without_symfony_translator(): void
    {
        $builder = new SymfonyLocaleContextBuilder(null);
        $context = $builder->process(new ApieContext([]));

        $this->assertFalse($context->hasContext(ContextConstants::LOCALE));
        $this->assertFalse($context->hasContext(ContextConstants::ACCEPT_LOCALE));
        $this->assertFalse($context->hasContext(ContextConstants::DATA_LOCALE));
    }

    public function test_does_not_override_existing_locale_context(): void
    {
        $builder = new SymfonyLocaleContextBuilder(new LocaleSwitcher('nl_BE', []));
        $context = (new ApieContext([]))
            ->withContext(ContextConstants::LOCALE, 'en')
            ->withContext(ContextConstants::ACCEPT_LOCALE, 'en')
            ->withContext(ContextConstants::DATA_LOCALE, 'en');

        $actual = $builder->process($context);

        $this->assertSame('en', $actual->getContext(ContextConstants::LOCALE));
        $this->assertSame('en', $actual->getContext(ContextConstants::ACCEPT_LOCALE));
        $this->assertSame('en', $actual->getContext(ContextConstants::DATA_LOCALE));
    }
}
