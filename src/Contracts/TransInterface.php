<?php

declare(strict_types=1);

namespace Litepie\Trans\Contracts;

/**
 * Interface TransInterface
 *
 * Contract for the Trans class defining all public methods
 * for locale management and URL localization.
 *
 * @package Litepie\Trans\Contracts
 */
interface TransInterface
{
    /**
     * Set the current locale.
     *
     * @param string|null $locale
     * @return string
     */
    public function setLocale(?string $locale = null): string;

    /**
     * Get the current locale.
     *
     * @return string
     */
    public function getCurrentLocale(): string;

    /**
     * Get the default locale.
     *
     * @return string
     */
    public function getDefaultLocale(): string;

    /**
     * Get current locale's regional setting.
     *
     * @return string|null
     */
    public function getCurrentLocaleRegional(): ?string;

    /**
     * Get current locale's native name.
     *
     * @return string
     */
    public function getCurrentLocaleNative(): string;

    /**
     * Get current locale's display name.
     *
     * @return string
     */
    public function getCurrentLocaleName(): string;

    /**
     * Get current locale's script direction.
     *
     * @return string
     */
    public function getCurrentLocaleDirection(): string;

    /**
     * Get current locale's script.
     *
     * @return string
     */
    public function getCurrentLocaleScript(): string;

    /**
     * Check if the given locale is supported.
     *
     * @param string $locale
     * @return bool
     */
    public function checkLocaleInSupportedLocales(string $locale): bool;

    /**
     * Get supported locales.
     *
     * @param bool $excludeCurrent
     * @return array
     */
    public function getSupportedLocales(bool $excludeCurrent = false): array;

    /**
     * Get supported locale keys.
     *
     * @return array
     */
    public function getSupportedLanguagesKeys(): array;

    /**
     * Check if the application supports multiple languages.
     *
     * @return bool
     */
    public function isMultilingual(): bool;

    /**
     * Generate a localized URL.
     *
     * @param string|null $locale
     * @param string|null $url
     * @param array $attributes
     * @param bool $forceDefaultLocation
     * @return string
     */
    public function getLocalizedURL(?string $locale = null, ?string $url = null, array $attributes = [], bool $forceDefaultLocation = false): string;

    /**
     * Get URL from translated route name.
     *
     * @param string $locale
     * @param string $transKeyName
     * @param array $attributes
     * @param bool $forceDefaultLocation
     * @return string|false
     */
    public function getURLFromRouteNameTranslated(string $locale, string $transKeyName, array $attributes = [], bool $forceDefaultLocation = false): string|false;

    /**
     * Get non-localized URL.
     *
     * @param string|null $url
     * @return string
     */
    public function getNonLocalizedURL(?string $url = null): string;

    /**
     * Create URL from URI.
     *
     * @param string $uri
     * @return string
     */
    public function createUrlFromUri(string $uri): string;

    /**
     * Set base URL.
     *
     * @param string $url
     * @return void
     */
    public function setBaseUrl(string $url): void;

    /**
     * Check if default locale should be hidden in URL.
     *
     * @return bool
     */
    public function hideDefaultLocaleInURL(): bool;

    /**
     * Get locale mapping.
     *
     * @return array
     */
    public function getLocalesMapping(): array;

    /**
     * Get locale from mapping.
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocaleFromMapping(?string $locale): ?string;

    /**
     * Get inversed locale from mapping.
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getInversedLocaleFromMapping(?string $locale): ?string;
}
