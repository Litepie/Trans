# Litepie Trans - Enhanced Laravel Translation Package

[![Latest Version](https://img.shields.io/github/v/release/litepie/trans?style=flat-square)](https://github.com/litepie/trans/releases)
[![License](https://img.shields.io/github/license/litepie/trans?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-8892BF?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D9.0-FF2D20?style=flat-square)](https://laravel.com)

A modern, production-ready, and well-documented PHP translation package with advanced language negotiation, URL localization, route translation, and comprehensive Laravel integration.

## ✨ Features

- 🌍 **Automatic Language Detection** - Intelligent locale detection from Accept-Language headers
- 🔗 **URL-based Locale Switching** - Clean, SEO-friendly localized URLs
- 🛣️ **Route Translation** - Translate route patterns and parameters
- ⚡ **Performance Optimized** - Built-in caching and optimization
- 🔧 **Middleware Integration** - Seamless Laravel middleware support
- 🎯 **Type Safe** - Full PHP 8+ type declarations and strict types
- 📱 **Multi-directional Support** - LTR and RTL language support
- 🍪 **Session & Cookie Support** - Remember user language preferences
- 🧪 **Comprehensive Testing** - Full test suite included
- 📚 **Rich Documentation** - Extensive documentation and examples

## 🚀 Installation

Install the package via Composer:

```bash
composer require litepie/trans
```

### Laravel Auto-Discovery

The package will automatically register its service provider and aliases.

### Manual Registration (if needed)

Add the service provider to your `config/app.php`:

```php
'providers' => [
    // Other providers...
    Litepie\Trans\TransServiceProvider::class,
],
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Litepie\Trans\TransServiceProvider" --tag="trans-config"
```

## ⚙️ Configuration

The configuration file `config/trans.php` provides extensive customization options:

```php
<?php

return [
    // Supported locales with metadata
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
        // More locales...
    ],

    // Use Accept-Language header for negotiation
    'useAcceptLanguageHeader' => true,

    // Hide default locale in URLs (/en/page becomes /page)
    'hideDefaultLocaleInURL' => false,

    // Auto-detect and redirect to user's preferred locale
    'autoDetectLocale' => true,

    // Additional configuration options...
];
```

## 🎯 Basic Usage

### Setting Up Middleware

Add the localization middleware to your routes:

```php
// In routes/web.php
Route::group(['middleware' => 'localization'], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/about', 'AboutController@index');
    // Your localized routes...
});
```

### Using the Trans Service

```php
<?php

use Litepie\Trans\Trans;

class HomeController extends Controller
{
    protected Trans $trans;

    public function __construct(Trans $trans)
    {
        $this->trans = $trans;
    }

    public function index()
    {
        // Get current locale
        $currentLocale = $this->trans->getCurrentLocale(); // 'en'

        // Get localized URL for different locale
        $spanishUrl = $this->trans->getLocalizedURL('es'); 

        // Check if multilingual
        $isMultilingual = $this->trans->isMultilingual(); // true/false

        // Get all supported locales
        $locales = $this->trans->getSupportedLocales();

        return view('home', compact('currentLocale', 'spanishUrl', 'locales'));
    }
}
```

### Language Switcher in Blade Templates

Create a language switcher component:

```blade
{{-- resources/views/components/language-switcher.blade.php --}}
@inject('trans', 'Litepie\Trans\Trans')

<div class="language-switcher">
    <div class="dropdown">
        <button class="dropdown-toggle">
            {{ $trans->getCurrentLocaleNative() }}
            <span class="flag flag-{{ $trans->getCurrentLocale() }}"></span>
        </button>
        
        <div class="dropdown-menu">
            @foreach($trans->getSupportedLocales() as $code => $locale)
                @if($code !== $trans->getCurrentLocale())
                    <a href="{{ $trans->getLocalizedURL($code) }}" 
                       class="dropdown-item">
                        <span class="flag flag-{{ $code }}"></span>
                        {{ $locale['native'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
```

## 🌐 Advanced Features

### Route Translation

Define translated routes in your language files:

```php
// resources/lang/en/routes.php
return [
    'about' => 'about',
    'contact' => 'contact',
    'products' => 'products',
];

// resources/lang/es/routes.php  
return [
    'about' => 'acerca-de',
    'contact' => 'contacto', 
    'products' => 'productos',
];
```

Register translated routes:

```php
Route::group(['middleware' => 'localization'], function () {
    Route::get(trans('routes.about'), 'AboutController@index')->name('about');
    Route::get(trans('routes.contact'), 'ContactController@index')->name('contact');
});
```

### Custom Language Negotiation

```php
use Litepie\Trans\LanguageNegotiator;

$negotiator = new LanguageNegotiator(
    'en', // default locale
    ['en' => [...], 'es' => [...]], // supported locales
    $request
);

$bestLocale = $negotiator->negotiateLanguage();
```

### Locale Detection Priority

The package detects locale in this order:

1. **URL segment** - `/es/page` (highest priority)
2. **Session** - Stored user preference
3. **Cookie** - Persistent preference
4. **Accept-Language header** - Browser preference
5. **Default locale** - Fallback (lowest priority)

## 🔧 Middleware Options

### LocalizationMiddleware

Automatically handles locale detection and URL redirection:

```php
Route::group(['middleware' => 'localization'], function () {
    // Your routes
});
```

### Custom Middleware Usage

```php
public function handle($request, Closure $next)
{
    // Custom logic before localization
    
    $response = $next($request);
    
    // Custom logic after localization
    
    return $response;
}
```

## 📊 API Reference

### Trans Class Methods

#### Locale Management

```php
// Set current locale
$trans->setLocale('es'); // Returns: 'es'

// Get current locale  
$trans->getCurrentLocale(); // Returns: 'en'

// Get default locale
$trans->getDefaultLocale(); // Returns: 'en'

// Check if locale is supported
$trans->checkLocaleInSupportedLocales('fr'); // Returns: true/false
```

#### Locale Information

```php
// Get locale display name
$trans->getCurrentLocaleName(); // Returns: 'English'

// Get locale native name  
$trans->getCurrentLocaleNative(); // Returns: 'English'

// Get text direction
$trans->getCurrentLocaleDirection(); // Returns: 'ltr' or 'rtl'

// Get locale script
$trans->getCurrentLocaleScript(); // Returns: 'Latn'

// Get regional locale
$trans->getCurrentLocaleRegional(); // Returns: 'en_US'
```

#### URL Generation

```php
// Generate localized URL
$trans->getLocalizedURL('es', '/products'); 
// Returns: 'https://site.com/es/productos'

// Generate non-localized URL
$trans->getNonLocalizedURL('/es/productos');
// Returns: 'https://site.com/productos'

// Get URL from route name
$trans->getURLFromRouteNameTranslated('es', 'products');
```

#### Utility Methods

```php
// Check if application is multilingual
$trans->isMultilingual(); // Returns: true/false

// Get all supported locales
$trans->getSupportedLocales(); // Returns: array

// Get supported locale keys
$trans->getSupportedLanguagesKeys(); // Returns: ['en', 'es', 'fr']
```

### LanguageNegotiator Class

```php
// Negotiate best language
$negotiator->negotiateLanguage(); // Returns: 'es'

// Get supported languages  
$negotiator->getSupportedLanguages(); // Returns: array

// Check if language is supported
$negotiator->isLanguageSupported('fr'); // Returns: true/false

// Get best match for language
$negotiator->getBestMatch('en-US'); // Returns: 'en'
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test
./vendor/bin/phpunit tests/Unit/TransTest.php
```

### Example Test

```php
<?php

use Litepie\Trans\Trans;
use Litepie\Trans\LanguageNegotiator;

class TransTest extends TestCase
{
    public function test_can_set_and_get_locale()
    {
        $trans = $this->app->make(Trans::class);
        
        $result = $trans->setLocale('es');
        
        $this->assertEquals('es', $result);
        $this->assertEquals('es', $trans->getCurrentLocale());
    }

    public function test_can_generate_localized_url()
    {
        $trans = $this->app->make(Trans::class);
        
        $url = $trans->getLocalizedURL('es', '/products');
        
        $this->assertStringContains('/es/', $url);
    }
}
```

## 🔒 Security Considerations

- ✅ **Input Validation** - All locale inputs are validated against supported locales
- ✅ **XSS Protection** - Output is properly escaped in Blade components  
- ✅ **CSRF Protection** - Compatible with Laravel's CSRF middleware
- ✅ **SQL Injection** - No direct database queries, uses Laravel's query builder

## 🚀 Performance Tips

1. **Enable Route Caching**:
   ```php
   'cacheRouteTranslations' => true,
   'routeTranslationCacheTTL' => 60, // minutes
   ```

2. **Use Laravel's Route Caching**:
   ```bash
   php artisan route:cache
   ```

3. **Optimize Config Loading**:
   ```bash
   php artisan config:cache
   ```

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/litepie/trans.git

# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

## 📝 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## 🛡️ Security

If you discover any security-related issues, please email security@litepie.com instead of using the issue tracker.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Credits

- **Original Lavalite Team** - For the foundation
- **Laravel Community** - For the amazing framework
- **Contributors** - All the people who have contributed to this project

## 🔗 Links

- [Documentation](https://docs.litepie.com/trans)
- [Issue Tracker](https://github.com/litepie/trans/issues)
- [Source Code](https://github.com/litepie/trans)
- [Packagist](https://packagist.org/packages/litepie/trans)

---

Made with ❤️ by the [Lavalite Team](https://litepie.com)
