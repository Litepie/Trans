# Changelog

All notable changes to the Litepie Trans package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2025-08-29

### Added
- **Laravel 12 compatibility** - Full support for Laravel 12.x
- **Enhanced middleware** with improved return type declarations
- **Environment-based configuration** options for better deployment flexibility
- **Performance optimizations** for Laravel 12's improved container
- **Memory optimization settings** for better resource management
- **Query string persistence** configuration options
- **Route model binding** enhancements for Laravel 12

### Changed
- **Minimum PHP version** raised to 8.1 for Laravel 12 compatibility
- **Service provider** updated for Laravel 12's container improvements
- **Middleware return types** improved with Symfony Response compatibility
- **Configuration structure** enhanced with environment variable support
- **Dependencies** updated to support Laravel 9-12

### Enhanced
- **Type safety** with stricter return type declarations
- **Container resolution** optimized for Laravel 12
- **Error handling** improved with Laravel 12's exception handling
- **URL generation** compatibility with Laravel 12's URL builder

### Performance
- **Container binding** optimized for Laravel 12's service container
- **Route caching** improvements for Laravel 12's route system
- **Memory usage** optimization with Laravel 12's efficiency improvements

## [2.0.0] - 2025-08-22

### Added
- **Complete rewrite from scratch** with modern PHP 8+ features
- **Strict type declarations** throughout the codebase
- **Comprehensive interface contracts** for better code organization
- **Enhanced language negotiation** with improved Accept-Language header support
- **Advanced middleware system** for automatic locale detection and redirection
- **Rich configuration options** with extensive customization capabilities
- **Full PSR-4 compliance** with proper namespacing
- **Production-ready error handling** with custom exceptions
- **Translatable trait** for Eloquent models
- **Language switcher components** with Blade directive support
- **API endpoints** for SPA/frontend applications
- **Comprehensive test suite** with unit and integration tests
- **Extensive documentation** with usage examples
- **Performance optimizations** with caching support
- **Multi-directional text support** (LTR/RTL languages)
- **Session and cookie persistence** for user language preferences
- **URL localization** with SEO-friendly clean URLs
- **Route translation** with parameter substitution
- **Facade support** for convenient access
- **Laravel auto-discovery** for seamless installation

### Enhanced
- **Language detection algorithm** with priority-based fallback system
- **URL generation** with proper encoding and validation
- **Configuration system** with environment-aware settings
- **Error messages** with detailed context and suggestions
- **Code quality** with PHPStan and PHP-CS-Fixer integration

### Security
- **Input validation** for all locale parameters
- **XSS protection** in output rendering
- **Path traversal prevention** in URL handling
- **Injection attack mitigation** in query parameters

### Performance
- **Route caching** for translated URLs
- **Lazy loading** of language negotiation
- **Optimized locale detection** with minimal overhead
- **Memory-efficient** data structures

### Developer Experience
- **Rich IDE support** with comprehensive PHPDoc
- **Clear error messages** with actionable guidance
- **Extensive examples** covering common use cases
- **Migration guide** from version 1.x
- **Testing utilities** for application testing

### Backwards Compatibility
- **Breaking changes** from version 1.x (see UPGRADE.md)
- **Legacy adapter** available as separate package
- **Migration path** documented with step-by-step guide

### Documentation
- **Complete API reference** with method signatures
- **Usage examples** for common scenarios
- **Configuration guide** with all options explained
- **Best practices** for production deployment
- **Troubleshooting section** with common issues

### Testing
- **100% code coverage** target with comprehensive tests
- **Integration tests** with Laravel framework
- **Performance benchmarks** for optimization validation
- **Browser testing** for frontend components

### Requirements
- **PHP 8.0+** for modern language features
- **Laravel 9.0+** for framework compatibility
- **Composer 2.0+** for dependency management

---

## Legacy Versions

For changes in version 1.x and earlier, please see the [legacy changelog](CHANGELOG-LEGACY.md).

---

### Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

### Security

See [SECURITY.md](SECURITY.md) for security reporting guidelines.
