<?php

declare(strict_types=1);

namespace Litepie\Trans;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use Illuminate\Translation\Translator;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Litepie\Trans\Contracts\TransInterface;

/**
 * Class TransServiceProvider
 *
 * Service provider for the Litepie Trans package.
 * Registers and configures all translation services and middleware.
 *
 * @package Litepie\Trans
 */
class TransServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/trans.php' => config_path('trans.php'),
        ], 'trans-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/trans.php', 'trans');

        $this->registerLanguageNegotiator();
        $this->registerTransService();
        $this->registerMiddleware();
        $this->registerAliases();
    }

    /**
     * Register the language negotiator service.
     */
    protected function registerLanguageNegotiator(): void
    {
        $this->app->singleton(LanguageNegotiator::class, function ($app) {
            $config = $app['config'];
            $defaultLocale = $config->get('app.locale', 'en');
            $supportedLocales = $config->get('trans.supportedLocales', []);
            $request = $app['request'];

            return new LanguageNegotiator($defaultLocale, $supportedLocales, $request, $config);
        });
    }

    /**
     * Register the main Trans service.
     */
    protected function registerTransService(): void
    {
        $this->app->singleton('trans.localization', function ($app) {
            return new Trans(
                $app[Repository::class],
                $app[Translator::class],
                $app[Router::class],
                $app[Request::class],
                $app[UrlGenerator::class],
                $app[LanguageNegotiator::class]
            );
        });

        $this->app->alias('trans.localization', Trans::class);
        $this->app->alias('trans.localization', TransInterface::class);
    }

    /**
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('localization', Middleware\LocalizationMiddleware::class);
        $this->app['router']->aliasMiddleware('localeSessionRedirect', Middleware\LocaleSessionRedirectMiddleware::class);
        $this->app['router']->aliasMiddleware('localeCookieRedirect', Middleware\LocaleCookieRedirectMiddleware::class);
    }

    /**
     * Register package aliases.
     */
    protected function registerAliases(): void
    {
        $this->app->alias('trans.localization', 'localization');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'trans.localization',
            Trans::class,
            TransInterface::class,
            LanguageNegotiator::class,
        ];
    }
}
