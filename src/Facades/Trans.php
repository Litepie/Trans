<?php

declare(strict_types=1);

namespace Litepie\Trans\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Trans
 *
 * Facade for the Trans service.
 *
 * @package Litepie\Trans\Facades
 * 
 * @method static string setLocale(?string $locale = null)
 * @method static string getCurrentLocale()
 * @method static string getDefaultLocale()
 * @method static string|null getCurrentLocaleRegional()
 * @method static string getCurrentLocaleNative()
 * @method static string getCurrentLocaleName()
 * @method static string getCurrentLocaleDirection()
 * @method static string getCurrentLocaleScript()
 * @method static bool checkLocaleInSupportedLocales(string $locale)
 * @method static array getSupportedLocales(bool $excludeCurrent = false)
 * @method static array getSupportedLanguagesKeys()
 * @method static bool isMultilingual()
 * @method static string getLocalizedURL(?string $locale = null, ?string $url = null, array $attributes = [], bool $forceDefaultLocation = false)
 * @method static string|false getURLFromRouteNameTranslated(string $locale, string $transKeyName, array $attributes = [], bool $forceDefaultLocation = false)
 * @method static string getNonLocalizedURL(?string $url = null)
 * @method static string createUrlFromUri(string $uri)
 * @method static void setBaseUrl(string $url)
 * @method static bool hideDefaultLocaleInURL()
 * @method static array getLocalesMapping()
 * @method static string|null getLocaleFromMapping(?string $locale)
 * @method static string|null getInversedLocaleFromMapping(?string $locale)
 */
class Trans extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'trans.localization';
    }
}
