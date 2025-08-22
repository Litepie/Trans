<?php

/**
 * Litepie Trans - Usage Examples
 * 
 * This file contains comprehensive examples of how to use the Litepie Trans package
 * in various scenarios. Use these examples as a reference for implementing
 * internationalization in your Laravel application.
 */

// Example 1: Basic Service Usage
use Litepie\Trans\Trans;
use Litepie\Trans\Facades\Trans as TransFacade;

class ExampleController extends Controller
{
    /**
     * Example 1: Using dependency injection
     */
    public function index(Trans $trans)
    {
        // Get current locale
        $currentLocale = $trans->getCurrentLocale(); // 'en'
        
        // Get locale information
        $localeName = $trans->getCurrentLocaleName(); // 'English'
        $localeNative = $trans->getCurrentLocaleNative(); // 'English'
        $localeDirection = $trans->getCurrentLocaleDirection(); // 'ltr'
        
        // Generate localized URLs
        $spanishUrl = $trans->getLocalizedURL('es');
        $frenchUrl = $trans->getLocalizedURL('fr', '/products');
        
        // Check if multilingual
        $isMultilingual = $trans->isMultilingual();
        
        return view('home', compact(
            'currentLocale', 
            'localeName', 
            'spanishUrl', 
            'isMultilingual'
        ));
    }
    
    /**
     * Example 2: Using facade
     */
    public function products()
    {
        // Using facade for quick access
        $currentLocale = TransFacade::getCurrentLocale();
        $supportedLocales = TransFacade::getSupportedLocales();
        
        return view('products', compact('currentLocale', 'supportedLocales'));
    }
}

// Example 2: Language Switcher Component
class LanguageSwitcher extends Component
{
    public function render()
    {
        $trans = app(Trans::class);
        
        $currentLocale = $trans->getCurrentLocale();
        $supportedLocales = $trans->getSupportedLocales();
        
        // Generate language switch URLs
        $languageUrls = [];
        foreach ($supportedLocales as $code => $locale) {
            if ($code !== $currentLocale) {
                $languageUrls[$code] = [
                    'url' => $trans->getLocalizedURL($code),
                    'name' => $locale['name'],
                    'native' => $locale['native'],
                ];
            }
        }
        
        return view('components.language-switcher', [
            'currentLocale' => $currentLocale,
            'currentLocaleName' => $trans->getCurrentLocaleNative(),
            'languageUrls' => $languageUrls,
        ]);
    }
}

// Example 3: Model with Translations
use Litepie\Trans\Traits\Translatable;

class Product extends Model
{
    use Translatable;
    
    /**
     * Translatable attributes
     */
    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];
    
    protected $fillable = ['name', 'description', 'price', 'meta_title', 'meta_description'];
    
    /**
     * Get product name in current locale
     */
    public function getLocalizedName(?string $locale = null): string
    {
        return $this->getTranslation('name', $locale);
    }
    
    /**
     * Get product description in current locale
     */
    public function getLocalizedDescription(?string $locale = null): string
    {
        return $this->getTranslation('description', $locale);
    }
    
    /**
     * Set product name for specific locale
     */
    public function setLocalizedName(string $name, ?string $locale = null): self
    {
        return $this->setTranslation('name', $name, $locale);
    }
}

// Example 4: Route Definitions with Translations
Route::group(['middleware' => 'localization'], function () {
    
    // Basic localized routes
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/about', 'AboutController@index')->name('about');
    Route::get('/contact', 'ContactController@index')->name('contact');
    
    // Translated route patterns
    Route::get(trans('routes.products'), 'ProductController@index')->name('products');
    Route::get(trans('routes.products') . '/{slug}', 'ProductController@show')->name('products.show');
    
    // Resource routes with translation
    Route::resource(trans('routes.news'), 'NewsController');
    
    // API routes can also be localized
    Route::prefix('api')->group(function () {
        Route::get(trans('routes.products'), 'Api\ProductController@index');
    });
});

// Example 5: Custom Middleware for Additional Logic
class CustomLocalizationMiddleware
{
    public function handle($request, Closure $next)
    {
        $trans = app(Trans::class);
        
        // Custom logic: redirect mobile users to specific locale
        if ($request->header('User-Agent') && str_contains($request->header('User-Agent'), 'Mobile')) {
            $mobileLocale = config('trans.mobileDefaultLocale', 'en');
            if ($trans->checkLocaleInSupportedLocales($mobileLocale)) {
                $trans->setLocale($mobileLocale);
            }
        }
        
        // Custom logic: set locale based on subdomain
        $subdomain = explode('.', $request->getHost())[0];
        if ($trans->checkLocaleInSupportedLocales($subdomain)) {
            $trans->setLocale($subdomain);
        }
        
        return $next($request);
    }
}

// Example 6: Service Provider Extension
class CustomTransServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Add custom language detection logic
        $this->app->resolving(Trans::class, function ($trans) {
            // Custom initialization logic
            $this->setupCustomLocaleDetection($trans);
        });
        
        // Add custom Blade directives
        Blade::directive('locale', function ($locale) {
            return "<?php echo app('trans.localization')->getLocalizedURL($locale); ?>";
        });
        
        Blade::directive('currentLocale', function () {
            return "<?php echo app('trans.localization')->getCurrentLocale(); ?>";
        });
    }
    
    protected function setupCustomLocaleDetection(Trans $trans)
    {
        // Add custom detection logic here
    }
}

// Example 7: API Usage for SPA/Frontend Applications
class ApiController extends Controller
{
    /**
     * Get localization data for frontend
     */
    public function localization(Trans $trans)
    {
        return response()->json([
            'current_locale' => $trans->getCurrentLocale(),
            'default_locale' => $trans->getDefaultLocale(),
            'supported_locales' => $trans->getSupportedLocales(),
            'is_multilingual' => $trans->isMultilingual(),
            'current_locale_info' => [
                'name' => $trans->getCurrentLocaleName(),
                'native' => $trans->getCurrentLocaleNative(),
                'direction' => $trans->getCurrentLocaleDirection(),
                'script' => $trans->getCurrentLocaleScript(),
            ],
        ]);
    }
    
    /**
     * Switch locale via API
     */
    public function switchLocale(Request $request, Trans $trans)
    {
        $locale = $request->input('locale');
        
        if (!$trans->checkLocaleInSupportedLocales($locale)) {
            return response()->json(['error' => 'Unsupported locale'], 400);
        }
        
        $trans->setLocale($locale);
        
        // Store in session
        $request->session()->put(config('trans.localeSessionKey', 'locale'), $locale);
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'localized_url' => $trans->getLocalizedURL($locale),
        ]);
    }
}

// Example 8: Testing Examples
class TransTest extends TestCase
{
    public function test_can_switch_locales()
    {
        $trans = $this->app->make(Trans::class);
        
        // Test setting locale
        $result = $trans->setLocale('es');
        $this->assertEquals('es', $result);
        $this->assertEquals('es', $trans->getCurrentLocale());
        
        // Test locale information
        $this->assertEquals('Español', $trans->getCurrentLocaleNative());
        $this->assertEquals('ltr', $trans->getCurrentLocaleDirection());
    }
    
    public function test_can_generate_localized_urls()
    {
        $trans = $this->app->make(Trans::class);
        
        $url = $trans->getLocalizedURL('es', '/products');
        
        $this->assertStringContains('/es/', $url);
        $this->assertStringContains('/products', $url);
    }
    
    public function test_middleware_redirects_to_correct_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8'
        ])->get('/');
        
        $response->assertRedirect();
        $this->assertStringContains('/es/', $response->headers->get('location'));
    }
}

// Example 9: Console Command for Managing Translations
class TranslationCommand extends Command
{
    protected $signature = 'trans:status {--locale=all}';
    protected $description = 'Show translation status';
    
    public function handle(Trans $trans)
    {
        $locale = $this->option('locale');
        
        if ($locale === 'all') {
            $locales = $trans->getSupportedLanguagesKeys();
        } else {
            $locales = [$locale];
        }
        
        $this->table(['Locale', 'Name', 'Native', 'Direction', 'Script'], 
            collect($locales)->map(function ($code) use ($trans) {
                $info = $trans->getSupportedLocales()[$code];
                return [
                    $code,
                    $info['name'],
                    $info['native'],
                    $info['dir'] ?? 'ltr',
                    $info['script'],
                ];
            })
        );
    }
}

// Example 10: Event Handling
class LocaleEventListener
{
    public function handle($event)
    {
        // Log locale changes
        \Log::info('Locale changed to: ' . $event->locale);
        
        // Clear specific caches when locale changes
        Cache::forget('localized-menu-' . $event->locale);
        
        // Update user preference in database
        if (auth()->check()) {
            auth()->user()->update(['preferred_locale' => $event->locale]);
        }
    }
}
