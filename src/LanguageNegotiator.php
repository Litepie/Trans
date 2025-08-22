<?php

declare(strict_types=1);

namespace Litepie\Trans;

use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use Locale;

/**
 * Class LanguageNegotiator
 *
 * Handles automatic language detection and negotiation based on:
 * - Accept-Language HTTP headers
 * - Browser preferences
 * - Regional settings
 * - Fallback mechanisms
 *
 * @package Litepie\Trans
 */
class LanguageNegotiator
{
    /**
     * Configuration repository instance.
     */
    protected Repository $configRepository;

    /**
     * HTTP request instance.
     */
    protected Request $request;

    /**
     * Default locale fallback.
     */
    protected string $defaultLocale;

    /**
     * Supported languages configuration.
     */
    protected array $supportedLanguages;

    /**
     * Whether to use PHP Intl extension.
     */
    protected bool $useIntl = false;

    /**
     * Initialize the LanguageNegotiator.
     *
     * @param string $defaultLocale
     * @param array $supportedLanguages
     * @param Request $request
     * @param Repository|null $configRepository
     */
    public function __construct(
        string $defaultLocale,
        array $supportedLanguages,
        Request $request,
        ?Repository $configRepository = null
    ) {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLanguages = $supportedLanguages;
        $this->request = $request;
        $this->configRepository = $configRepository ?? app('config');

        $this->initializeIntlSupport();
    }

    /**
     * Initialize Intl extension support if available.
     */
    protected function initializeIntlSupport(): void
    {
        if (class_exists('Locale')) {
            $this->useIntl = true;

            foreach ($this->supportedLanguages as $key => $supportedLanguage) {
                if (!isset($supportedLanguage['lang'])) {
                    $supportedLanguage['lang'] = Locale::canonicalize($key);
                } else {
                    $supportedLanguage['lang'] = Locale::canonicalize($supportedLanguage['lang']);
                }

                if (isset($supportedLanguage['regional'])) {
                    $supportedLanguage['regional'] = Locale::canonicalize($supportedLanguage['regional']);
                }

                $this->supportedLanguages[$key] = $supportedLanguage;
            }
        }
    }

    /**
     * Negotiate the best language based on Accept-Language header.
     *
     * This method uses HTTP content negotiation to determine the best
     * language match from the client's Accept-Language header.
     *
     * Quality factors are supported:
     * Accept-Language: en-US;q=0.7, en-UK;q=0.6, fr;q=0.8, de;q=0.5
     *
     * @return string The negotiated language or default locale
     */
    public function negotiateLanguage(): string
    {
        $matches = $this->getMatchesFromAcceptedLanguages();

        foreach ($matches as $key => $quality) {
            // Check for locale mapping
            $key = $this->configRepository->get('trans.localesMapping')[$key] ?? $key;

            if (!empty($this->supportedLanguages[$key])) {
                return $key;
            }

            // Use Intl extension for better matching
            if ($this->useIntl && function_exists('locale_canonicalize')) {
                $key = Locale::canonicalize($key);
            }

            // Search for acceptable locale by regional or lang match
            foreach ($this->supportedLanguages as $keySupported => $locale) {
                $regional = $locale['regional'] ?? '';
                $lang = $locale['lang'] ?? '';

                if (($regional && $regional === $key) || ($lang && $lang === $key)) {
                    return $keySupported;
                }
            }
        }

        // If any language is acceptable (wildcard), return the first supported
        if (isset($matches['*'])) {
            reset($this->supportedLanguages);
            return key($this->supportedLanguages);
        }

        // Try PHP Intl extension's acceptFromHttp method
        if ($this->useIntl && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $httpAcceptLanguage = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            if (!empty($this->supportedLanguages[$httpAcceptLanguage])) {
                return $httpAcceptLanguage;
            }
        }

        // Try to get locale from remote host (rarely used)
        if ($this->request->server('REMOTE_HOST')) {
            $remoteHost = explode('.', $this->request->server('REMOTE_HOST'));
            $lang = strtolower(end($remoteHost));

            if (!empty($this->supportedLanguages[$lang])) {
                return $lang;
            }
        }

        return $this->defaultLocale;
    }

    /**
     * Get all accepted languages from the Accept-Language header.
     *
     * @return array Associative array of language => quality
     */
    protected function getMatchesFromAcceptedLanguages(): array
    {
        $matches = [];

        $acceptLanguages = $this->request->header('Accept-Language');
        if (!$acceptLanguages) {
            return $matches;
        }

        $acceptLanguages = explode(',', $acceptLanguages);
        $genericMatches = [];

        foreach ($acceptLanguages as $option) {
            $option = array_map('trim', explode(';', $option));
            $language = $option[0];

            if (isset($option[1])) {
                $quality = (float) str_replace('q=', '', $option[1]);
            } else {
                $quality = null;
                // Assign default low weight for generic values
                if ($language === '*/*') {
                    $quality = 0.01;
                } elseif (strpos($language, '/*') !== false) {
                    $quality = 0.02;
                }
            }

            // Handle quality factor defaults
            if ($quality === null) {
                $quality = 1.0;
            }

            // Language subtags (e.g., en-US)
            if (strpos($language, '-') !== false) {
                [$primaryLanguage] = explode('-', $language);
                if (!isset($genericMatches[$primaryLanguage]) || $genericMatches[$primaryLanguage] < $quality) {
                    $genericMatches[$primaryLanguage] = $quality;
                }
            }

            $matches[$language] = $quality;
        }

        // Merge generic matches with lower priority
        foreach ($genericMatches as $language => $quality) {
            if (!isset($matches[$language])) {
                $matches[$language] = $quality;
            }
        }

        // Sort by quality (highest first)
        arsort($matches);

        return $matches;
    }

    /**
     * Get the default locale.
     *
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Get supported languages.
     *
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * Set supported languages.
     *
     * @param array $supportedLanguages
     * @return void
     */
    public function setSupportedLanguages(array $supportedLanguages): void
    {
        $this->supportedLanguages = $supportedLanguages;
        $this->initializeIntlSupport();
    }

    /**
     * Check if a language is supported.
     *
     * @param string $language
     * @return bool
     */
    public function isLanguageSupported(string $language): bool
    {
        return isset($this->supportedLanguages[$language]);
    }

    /**
     * Get the best match for a given language.
     *
     * @param string $language
     * @return string|null
     */
    public function getBestMatch(string $language): ?string
    {
        if ($this->isLanguageSupported($language)) {
            return $language;
        }

        // Try primary language if it's a subtag
        if (strpos($language, '-') !== false) {
            [$primaryLanguage] = explode('-', $language);
            if ($this->isLanguageSupported($primaryLanguage)) {
                return $primaryLanguage;
            }
        }

        return null;
    }
}
