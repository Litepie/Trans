<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Define the locales that your application supports. The array key is the
    | locale code, and the value is an array containing locale metadata.
    |
    | Available metadata fields:
    | - name: Display name in English
    | - native: Native name in the locale's language
    | - script: Unicode script code (e.g., 'Latn', 'Arab')
    | - dir: Text direction ('ltr' or 'rtl')
    | - regional: Regional locale code for formatting
    |
    */

    'supportedLocales' => [
        'en' => [
            'name' => 'English',
            'script' => 'Latn',
            'native' => 'English',
            'regional' => 'en_US',
            'dir' => 'ltr',
        ],
        'es' => [
            'name' => 'Spanish',
            'script' => 'Latn',
            'native' => 'Español',
            'regional' => 'es_ES',
            'dir' => 'ltr',
        ],
        'fr' => [
            'name' => 'French',
            'script' => 'Latn',
            'native' => 'Français',
            'regional' => 'fr_FR',
            'dir' => 'ltr',
        ],
        'de' => [
            'name' => 'German',
            'script' => 'Latn',
            'native' => 'Deutsch',
            'regional' => 'de_DE',
            'dir' => 'ltr',
        ],
        'it' => [
            'name' => 'Italian',
            'script' => 'Latn',
            'native' => 'Italiano',
            'regional' => 'it_IT',
            'dir' => 'ltr',
        ],
        'pt' => [
            'name' => 'Portuguese',
            'script' => 'Latn',
            'native' => 'Português',
            'regional' => 'pt_PT',
            'dir' => 'ltr',
        ],
        'ar' => [
            'name' => 'Arabic',
            'script' => 'Arab',
            'native' => 'العربية',
            'regional' => 'ar_SA',
            'dir' => 'rtl',
        ],
        'zh' => [
            'name' => 'Chinese (Simplified)',
            'script' => 'Hans',
            'native' => '简体中文',
            'regional' => 'zh_CN',
            'dir' => 'ltr',
        ],
        'ja' => [
            'name' => 'Japanese',
            'script' => 'Jpan',
            'native' => '日本語',
            'regional' => 'ja_JP',
            'dir' => 'ltr',
        ],
        'ko' => [
            'name' => 'Korean',
            'script' => 'Hang',
            'native' => '한국어',
            'regional' => 'ko_KR',
            'dir' => 'ltr',
        ],
        'ru' => [
            'name' => 'Russian',
            'script' => 'Cyrl',
            'native' => 'Русский',
            'regional' => 'ru_RU',
            'dir' => 'ltr',
        ],
        'hi' => [
            'name' => 'Hindi',
            'script' => 'Deva',
            'native' => 'हिन्दी',
            'regional' => 'hi_IN',
            'dir' => 'ltr',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Use Accept-Language Header
    |--------------------------------------------------------------------------
    |
    | Determine if the package should negotiate the locale using the
    | Accept-Language header if it's not defined in the URL.
    | If false, the application's default locale will be used.
    |
    */

    'useAcceptLanguageHeader' => true,

    /*
    |--------------------------------------------------------------------------
    | Hide Default Locale in URL
    |--------------------------------------------------------------------------
    |
    | If this option is set to true, the default application locale will be
    | hidden from URLs. For example, '/en/page' becomes '/page' when English
    | is the default locale.
    |
    */

    'hideDefaultLocaleInURL' => false,

    /*
    |--------------------------------------------------------------------------
    | Locale Mapping
    |--------------------------------------------------------------------------
    |
    | Map locale codes to alternative representations. This is useful when
    | you want to use different locale codes in URLs than in your application.
    | For example, map 'en-us' to 'en'.
    |
    */

    'localesMapping' => [
        // 'en-us' => 'en',
        // 'en-gb' => 'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | Locales Order
    |--------------------------------------------------------------------------
    |
    | Define the order in which locales should be displayed in language
    | selectors. If not specified, locales will be displayed in the order
    | they are defined in the supportedLocales array.
    |
    */

    'localesOrder' => [
        'en',
        'es',
        'fr',
        'de',
        'it',
        'pt',
        'ar',
        'zh',
        'ja',
        'ko',
        'ru',
        'hi',
    ],

    /*
    |--------------------------------------------------------------------------
    | UTF-8 Suffix
    |--------------------------------------------------------------------------
    |
    | The UTF-8 suffix to append to locale codes when setting system locales
    | for proper formatting of dates, numbers, and currencies.
    |
    */

    'utf8suffix' => '.UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Route Translation Caching
    |--------------------------------------------------------------------------
    |
    | Enable caching of translated routes for improved performance. When
    | enabled, route translations will be cached to reduce database queries
    | and improve response times.
    |
    */

    'cacheRouteTranslations' => true,

    /*
    |--------------------------------------------------------------------------
    | Route Translation Cache TTL
    |--------------------------------------------------------------------------
    |
    | Time to live (in minutes) for cached route translations. Set to null
    | to cache indefinitely until manually cleared.
    |
    */

    'routeTranslationCacheTTL' => 60,

    /*
    |--------------------------------------------------------------------------
    | Auto-Detect Locale from Browser
    |--------------------------------------------------------------------------
    |
    | When enabled, the package will attempt to automatically detect the
    | user's preferred locale from their browser settings and redirect
    | them to the appropriate localized version of the site.
    |
    */

    'autoDetectLocale' => true,

    /*
    |--------------------------------------------------------------------------
    | Locale Session Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store the user's selected locale. This allows
    | the locale preference to persist across requests.
    |
    */

    'localeSessionKey' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | Locale Cookie Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the locale cookie that can be used to remember
    | the user's language preference across browser sessions.
    |
    */

    'localeCookie' => [
        'name' => 'locale',
        'minutes' => 60 * 24 * 365, // 1 year
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httpOnly' => false,
        'sameSite' => 'lax',
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for URL generation and localization.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | URL Generation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for URL generation and localization.
    | These settings work with Laravel 12's improved URL generation.
    |
    */

    'urls' => [
        'forceHttps' => env('TRANS_FORCE_HTTPS', false),
        'omitUrlParamsOnRedirect' => env('TRANS_OMIT_PARAMS_ON_REDIRECT', false),
        'redirectToDefaultLocale' => env('TRANS_REDIRECT_TO_DEFAULT_LOCALE', true),
        'appendTrailingSlash' => env('TRANS_APPEND_TRAILING_SLASH', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Settings for optimizing performance in Laravel 12.
    |
    */

    'performance' => [
        'enableRouteModelBinding' => env('TRANS_ENABLE_ROUTE_MODEL_BINDING', true),
        'enableQueryStringPersistence' => env('TRANS_ENABLE_QUERY_STRING_PERSISTENCE', true),
        'enableMemoryOptimization' => env('TRANS_ENABLE_MEMORY_OPTIMIZATION', true),
    ],

];
