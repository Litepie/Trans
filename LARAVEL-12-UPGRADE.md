# Laravel 12 Upgrade Guide

This guide will help you upgrade your Litepie Trans package to be compatible with Laravel 12.

## Requirements

### System Requirements
- **PHP 8.1+** (Laravel 12 requirement)
- **Laravel 12.x** or **Laravel 9.x/10.x/11.x** (backward compatible)
- **Composer 2.0+**

### Updated Dependencies
The package now supports Laravel 9-12:
```json
{
    "require": {
        "php": ">=8.1",
        "illuminate/support": "^9.0|^10.0|^11.0|^12.0",
        "illuminate/http": "^9.0|^10.0|^11.0|^12.0",
        "illuminate/config": "^9.0|^10.0|^11.0|^12.0",
        "illuminate/translation": "^9.0|^10.0|^11.0|^12.0",
        "illuminate/routing": "^9.0|^10.0|^11.0|^12.0"
    }
}
```

## Installation & Upgrade

### Fresh Installation
```bash
composer require litepie/trans:^2.1
```

### Upgrading from 2.0.x
```bash
composer update litepie/trans
```

### Upgrading from 1.x
Please see the main [UPGRADE.md](UPGRADE.md) guide for major version changes.

## Laravel 12 Specific Changes

### 1. Enhanced Configuration
The configuration file now supports environment variables for better deployment:

```php
// config/trans.php
'urls' => [
    'forceHttps' => env('TRANS_FORCE_HTTPS', false),
    'omitUrlParamsOnRedirect' => env('TRANS_OMIT_PARAMS_ON_REDIRECT', false),
    'redirectToDefaultLocale' => env('TRANS_REDIRECT_TO_DEFAULT_LOCALE', true),
    'appendTrailingSlash' => env('TRANS_APPEND_TRAILING_SLASH', false),
],

'performance' => [
    'enableRouteModelBinding' => env('TRANS_ENABLE_ROUTE_MODEL_BINDING', true),
    'enableQueryStringPersistence' => env('TRANS_ENABLE_QUERY_STRING_PERSISTENCE', true),
    'enableMemoryOptimization' => env('TRANS_ENABLE_MEMORY_OPTIMIZATION', true),
],
```

### 2. Environment Variables
Add these to your `.env` file for customization:

```env
# Translation Configuration
TRANS_FORCE_HTTPS=false
TRANS_OMIT_PARAMS_ON_REDIRECT=false
TRANS_REDIRECT_TO_DEFAULT_LOCALE=true
TRANS_APPEND_TRAILING_SLASH=false

# Performance Settings
TRANS_ENABLE_ROUTE_MODEL_BINDING=true
TRANS_ENABLE_QUERY_STRING_PERSISTENCE=true
TRANS_ENABLE_MEMORY_OPTIMIZATION=true
```

### 3. Middleware Updates
The middleware now has improved return type declarations for Laravel 12:

```php
// Before (Laravel 11 and earlier)
public function handle(Request $request, Closure $next)
{
    // ...
}

// After (Laravel 12 compatible)
public function handle(Request $request, Closure $next): SymfonyResponse
{
    // ...
}
```

### 4. Service Provider Enhancements
The service provider has been updated for Laravel 12's improved container:

```php
// Updated for Laravel 12
public function boot(): void
{
    $this->publishes([
        __DIR__ . '/../config/trans.php' => $this->app->configPath('trans.php'),
    ], 'trans-config');

    if ($this->app->runningInConsole()) {
        $this->commands([
            // Console commands
        ]);
    }
}
```

## Migration Steps

### Step 1: Update Composer
```bash
composer require litepie/trans:^2.1
```

### Step 2: Update Configuration
Republish the config file to get the latest options:
```bash
php artisan vendor:publish --provider="Litepie\Trans\TransServiceProvider" --tag="trans-config" --force
```

### Step 3: Update Environment Variables
Add the new environment variables to your `.env` file as shown above.

### Step 4: Clear Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Test Your Application
Run your test suite to ensure everything works correctly:
```bash
php artisan test
```

## Laravel 12 Performance Optimizations

### 1. Enable Memory Optimization
```php
// config/trans.php
'performance' => [
    'enableMemoryOptimization' => true,
],
```

### 2. Route Model Binding
The package now supports Laravel 12's enhanced route model binding:
```php
'performance' => [
    'enableRouteModelBinding' => true,
],
```

### 3. Query String Persistence
Improved handling of query strings in Laravel 12:
```php
'performance' => [
    'enableQueryStringPersistence' => true,
],
```

## Troubleshooting

### Common Issues

#### 1. PHP Version Error
```
Error: Package requires PHP 8.1+
```
**Solution**: Upgrade to PHP 8.1 or higher.

#### 2. Laravel Version Mismatch
```
Error: Package requires Laravel 12.x
```
**Solution**: The package supports Laravel 9-12. Check your Laravel version:
```bash
php artisan --version
```

#### 3. Configuration Cache Issues
```
Error: Configuration not found
```
**Solution**: Clear and rebuild configuration cache:
```bash
php artisan config:clear
php artisan config:cache
```

### Getting Help

If you encounter issues during the upgrade:

1. Check the [GitHub Issues](https://github.com/litepie/trans/issues)
2. Review the [Documentation](README.md)
3. Check the [Changelog](CHANGELOG.md) for breaking changes

## What's New in Laravel 12 Support

### Enhanced Features
- ✅ **Improved type safety** with strict return types
- ✅ **Better performance** with Laravel 12's optimizations
- ✅ **Enhanced middleware** with Symfony Response compatibility
- ✅ **Environment-driven configuration** for better deployment
- ✅ **Memory optimizations** for large applications
- ✅ **Container improvements** leveraging Laravel 12's service container

### Backward Compatibility
The package maintains backward compatibility with:
- Laravel 9.x
- Laravel 10.x  
- Laravel 11.x
- Laravel 12.x

### Testing
The package has been tested with:
- PHP 8.1, 8.2, 8.3
- Laravel 9.52+, 10.x, 11.x, 12.x
- All major browsers for frontend components

---

For more information, see the main [README](README.md) file.
