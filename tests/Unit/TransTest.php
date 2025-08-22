<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery;
use Litepie\Trans\Trans;
use Litepie\Trans\LanguageNegotiator;
use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use Illuminate\Translation\Translator;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;

/**
 * Unit tests for the Trans class.
 */
class TransTest extends TestCase
{
    protected Trans $trans;
    protected $configRepository;
    protected $translator;
    protected $router;
    protected $request;
    protected $url;
    protected $languageNegotiator;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->configRepository = Mockery::mock(Repository::class);
        $this->translator = Mockery::mock(Translator::class);
        $this->router = Mockery::mock(Router::class);
        $this->request = Mockery::mock(Request::class);
        $this->url = Mockery::mock(UrlGenerator::class);
        $this->languageNegotiator = Mockery::mock(LanguageNegotiator::class);

        // Setup basic config expectations
        $this->configRepository->shouldReceive('get')
            ->with('app.locale', 'en')
            ->andReturn('en');
            
        $this->configRepository->shouldReceive('get')
            ->with('trans.supportedLocales', [])
            ->andReturn([
                'en' => ['name' => 'English', 'native' => 'English'],
                'es' => ['name' => 'Spanish', 'native' => 'Español'],
                'fr' => ['name' => 'French', 'native' => 'Français'],
            ]);

        // Create Trans instance
        $this->trans = new Trans(
            $this->configRepository,
            $this->translator,
            $this->router,
            $this->request,
            $this->url,
            $this->languageNegotiator
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_get_default_locale(): void
    {
        $defaultLocale = $this->trans->getDefaultLocale();
        $this->assertEquals('en', $defaultLocale);
    }

    public function test_can_get_supported_locales(): void
    {
        $supportedLocales = $this->trans->getSupportedLocales();
        
        $this->assertIsArray($supportedLocales);
        $this->assertArrayHasKey('en', $supportedLocales);
        $this->assertArrayHasKey('es', $supportedLocales);
        $this->assertArrayHasKey('fr', $supportedLocales);
    }

    public function test_can_check_if_locale_is_supported(): void
    {
        $this->assertTrue($this->trans->checkLocaleInSupportedLocales('en'));
        $this->assertTrue($this->trans->checkLocaleInSupportedLocales('es'));
        $this->assertFalse($this->trans->checkLocaleInSupportedLocales('de'));
    }

    public function test_can_get_supported_language_keys(): void
    {
        $keys = $this->trans->getSupportedLanguagesKeys();
        
        $this->assertIsArray($keys);
        $this->assertContains('en', $keys);
        $this->assertContains('es', $keys);
        $this->assertContains('fr', $keys);
    }

    public function test_can_detect_if_multilingual(): void
    {
        $isMultilingual = $this->trans->isMultilingual();
        $this->assertTrue($isMultilingual);
    }

    public function test_can_set_locale(): void
    {
        // Mock the segment method to return null (no locale in URL)
        $this->request->shouldReceive('segment')
            ->with(1)
            ->andReturn(null);

        // Mock config for locale hiding
        $this->configRepository->shouldReceive('get')
            ->with('trans.hideDefaultLocaleInURL', false)
            ->andReturn(false);

        $this->configRepository->shouldReceive('get')
            ->with('trans.useAcceptLanguageHeader', true)
            ->andReturn(false);

        $result = $this->trans->setLocale('es');
        
        $this->assertEquals('es', $result);
        $this->assertEquals('es', $this->trans->getCurrentLocale());
    }

    public function test_get_current_locale_native(): void
    {
        // Set locale first
        $this->request->shouldReceive('segment')->with(1)->andReturn(null);
        $this->configRepository->shouldReceive('get')->with('trans.hideDefaultLocaleInURL', false)->andReturn(false);
        $this->configRepository->shouldReceive('get')->with('trans.useAcceptLanguageHeader', true)->andReturn(false);
        
        $this->trans->setLocale('es');
        
        $native = $this->trans->getCurrentLocaleNative();
        $this->assertEquals('Español', $native);
    }

    public function test_get_current_locale_direction_defaults_to_ltr(): void
    {
        $direction = $this->trans->getCurrentLocaleDirection();
        $this->assertEquals('ltr', $direction);
    }

    public function test_get_current_locale_script_defaults_to_latn(): void
    {
        $script = $this->trans->getCurrentLocaleScript();
        $this->assertEquals('Latn', $script);
    }

    public function test_can_create_url_from_uri(): void
    {
        $this->url->shouldReceive('to')
            ->with('test-path')
            ->andReturn('http://example.com/test-path');

        $result = $this->trans->createUrlFromUri('test-path');
        $this->assertEquals('http://example.com/test-path', $result);
    }

    public function test_can_set_base_url(): void
    {
        $this->trans->setBaseUrl('http://example.com');
        
        // Test URL creation with base URL
        $result = $this->trans->createUrlFromUri('test');
        $this->assertEquals('http://example.com/test', $result);
    }

    public function test_hide_default_locale_in_url_config(): void
    {
        $this->configRepository->shouldReceive('get')
            ->with('trans.hideDefaultLocaleInURL', false)
            ->andReturn(true);

        $result = $this->trans->hideDefaultLocaleInURL();
        $this->assertTrue($result);
    }

    public function test_get_locales_mapping(): void
    {
        $this->configRepository->shouldReceive('get')
            ->with('trans.localesMapping', [])
            ->andReturn(['en-us' => 'en']);

        $mapping = $this->trans->getLocalesMapping();
        $this->assertIsArray($mapping);
        $this->assertEquals('en', $mapping['en-us'] ?? null);
    }

    public function test_get_locale_from_mapping(): void
    {
        $this->configRepository->shouldReceive('get')
            ->with('trans.localesMapping', [])
            ->andReturn(['en-us' => 'en']);

        $result = $this->trans->getLocaleFromMapping('en-us');
        $this->assertEquals('en', $result);

        $result = $this->trans->getLocaleFromMapping('fr');
        $this->assertEquals('fr', $result);
    }

    public function test_throws_exception_for_unsupported_default_locale(): void
    {
        $this->expectException(\Litepie\Trans\Exceptions\UnsupportedLocaleException::class);

        // Mock config to return unsupported locale as default
        $configRepository = Mockery::mock(Repository::class);
        $configRepository->shouldReceive('get')
            ->with('app.locale', 'en')
            ->andReturn('unsupported');
            
        $configRepository->shouldReceive('get')
            ->with('trans.supportedLocales', [])
            ->andReturn(['en' => ['name' => 'English']]);

        new Trans(
            $configRepository,
            $this->translator,
            $this->router,
            $this->request,
            $this->url,
            $this->languageNegotiator
        );
    }

    public function test_throws_exception_when_no_supported_locales_defined(): void
    {
        $this->expectException(\Litepie\Trans\Exceptions\SupportedLocalesNotDefinedException::class);

        $configRepository = Mockery::mock(Repository::class);
        $configRepository->shouldReceive('get')
            ->with('app.locale', 'en')
            ->andReturn('en');
            
        $configRepository->shouldReceive('get')
            ->with('trans.supportedLocales', [])
            ->andReturn([]);

        $trans = new Trans(
            $configRepository,
            $this->translator,
            $this->router,
            $this->request,
            $this->url,
            $this->languageNegotiator
        );

        $trans->getSupportedLocales();
    }
}
