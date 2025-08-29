<?php

declare(strict_types=1);

namespace Litepie\Trans\Tests\Integration;

use Litepie\Trans\Tests\TestCase;
use Litepie\Trans\Trans;
use Litepie\Trans\LanguageNegotiator;
use Illuminate\Http\Request;

/**
 * Laravel 12 Compatibility Test
 *
 * Tests to ensure the package works correctly with Laravel 12.
 */
class Laravel12CompatibilityTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_trans_service(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $this->assertInstanceOf(Trans::class, $trans);
    }

    /** @test */
    public function it_can_detect_supported_locales(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $supportedLocales = $trans->getSupportedLocales();
        
        $this->assertIsArray($supportedLocales);
        $this->assertContains('en', $supportedLocales);
    }

    /** @test */
    public function it_can_set_and_get_locale(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $trans->setLocale('es');
        
        $this->assertEquals('es', $trans->getCurrentLocale());
    }

    /** @test */
    public function it_can_generate_localized_url(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $url = $trans->localizeURL('/test', 'fr');
        
        $this->assertStringContains('/fr/test', $url);
    }

    /** @test */
    public function it_can_negotiate_language_from_header(): void
    {
        $negotiator = $this->app->make(LanguageNegotiator::class);
        
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'fr-FR,fr;q=0.9,en;q=0.8');
        
        $locale = $negotiator->negotiateLanguage($request, ['en', 'fr', 'es']);
        
        $this->assertEquals('fr', $locale);
    }

    /** @test */
    public function it_handles_unsupported_locale_gracefully(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $trans->setLocale('invalid-locale');
        
        // Should fall back to default locale
        $this->assertEquals(config('app.locale', 'en'), $trans->getCurrentLocale());
    }

    /** @test */
    public function it_can_get_localized_routes(): void
    {
        $trans = $this->app->make(Trans::class);
        
        $routes = $trans->getLocalizedRoutes();
        
        $this->assertIsArray($routes);
    }

    /** @test */
    public function it_respects_configuration_settings(): void
    {
        // Test that configuration is properly loaded
        $this->assertIsArray(config('trans.supportedLocales'));
        $this->assertIsString(config('trans.defaultLocale'));
        $this->assertIsBool(config('trans.autoDetectLocale'));
    }

    /** @test */
    public function middleware_can_be_resolved(): void
    {
        // Test that middleware classes can be resolved
        $middleware = $this->app->make(\Litepie\Trans\Middleware\LocalizationMiddleware::class);
        
        $this->assertInstanceOf(\Litepie\Trans\Middleware\LocalizationMiddleware::class, $middleware);
    }

    /** @test */
    public function it_supports_environment_configuration(): void
    {
        // Test environment variable support
        putenv('TRANS_FORCE_HTTPS=true');
        putenv('TRANS_REDIRECT_TO_DEFAULT_LOCALE=false');
        
        // Reload config
        $this->app['config']->set('trans.urls.forceHttps', env('TRANS_FORCE_HTTPS', false));
        $this->app['config']->set('trans.urls.redirectToDefaultLocale', env('TRANS_REDIRECT_TO_DEFAULT_LOCALE', true));
        
        $this->assertTrue(config('trans.urls.forceHttps'));
        $this->assertFalse(config('trans.urls.redirectToDefaultLocale'));
        
        // Cleanup
        putenv('TRANS_FORCE_HTTPS');
        putenv('TRANS_REDIRECT_TO_DEFAULT_LOCALE');
    }
}
