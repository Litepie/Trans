<?php

declare(strict_types=1);

namespace Litepie\Trans\Traits;

use Illuminate\Support\Facades\App;

/**
 * Trait Translatable
 *
 * Add translation capabilities to Eloquent models.
 * This trait allows models to store and retrieve translated content.
 *
 * @package Litepie\Trans\Traits
 */
trait Translatable
{
    /**
     * List of translatable attributes.
     * 
     * Define this property in your model:
     * protected $translatable = ['title', 'content', 'description'];
     */
    protected array $translatable = [];

    /**
     * Boot the Translatable trait.
     */
    public static function bootTranslatable(): void
    {
        static::saving(function ($model) {
            $model->setTranslatableAttributes();
        });

        static::retrieved(function ($model) {
            $model->getTranslatableAttributes();
        });

        static::saved(function ($model) {
            $model->syncTranslatableAttributes();
        });
    }

    /**
     * Set translatable attributes before saving.
     */
    protected function setTranslatableAttributes(): void
    {
        foreach ($this->getTranslatableAttributes() as $key) {
            if (isset($this->attributes[$key])) {
                $this->setTranslation($key, $this->attributes[$key]);
            }
        }
    }

    /**
     * Get translatable attributes after retrieval.
     */
    protected function getTranslatableAttributes(): void
    {
        foreach ($this->getTranslatableAttributeNames() as $key) {
            $this->attributes[$key] = $this->getTranslation($key);
        }
    }

    /**
     * Sync translatable attributes after saving.
     */
    protected function syncTranslatableAttributes(): void
    {
        foreach ($this->getTranslatableAttributeNames() as $key) {
            $this->attributes[$key] = $this->getTranslation($key);
        }
    }

    /**
     * Set translation for a specific attribute and locale.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $locale
     * @return $this
     */
    public function setTranslation(string $key, $value, ?string $locale = null): self
    {
        $locale = $locale ?? $this->getCurrentLocale();
        
        $translations = $this->getTranslations($key);
        $translations[$locale] = $value;
        
        $this->attributes[$key] = $this->encodeTranslations($translations);
        
        return $this;
    }

    /**
     * Get translation for a specific attribute and locale.
     *
     * @param string $key
     * @param string|null $locale
     * @return mixed
     */
    public function getTranslation(string $key, ?string $locale = null)
    {
        $locale = $locale ?? $this->getCurrentLocale();
        $translations = $this->getTranslations($key);
        
        return $translations[$locale] ?? $translations[$this->getFallbackLocale()] ?? $this->attributes[$key] ?? null;
    }

    /**
     * Get all translations for a specific attribute.
     *
     * @param string $key
     * @return array
     */
    public function getTranslations(string $key): array
    {
        $value = $this->attributes[$key] ?? '';
        return $this->decodeTranslations($value);
    }

    /**
     * Check if attribute has translation for locale.
     *
     * @param string $key
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? $this->getCurrentLocale();
        $translations = $this->getTranslations($key);
        
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }

    /**
     * Remove translation for specific locale.
     *
     * @param string $key
     * @param string $locale
     * @return $this
     */
    public function removeTranslation(string $key, string $locale): self
    {
        $translations = $this->getTranslations($key);
        unset($translations[$locale]);
        
        $this->attributes[$key] = $this->encodeTranslations($translations);
        
        return $this;
    }

    /**
     * Get available locales for attribute.
     *
     * @param string $key
     * @return array
     */
    public function getAvailableLocales(string $key): array
    {
        return array_keys($this->getTranslations($key));
    }

    /**
     * Get translatable attribute names.
     *
     * @return array
     */
    public function getTranslatableAttributeNames(): array
    {
        return $this->translatable ?? [];
    }

    /**
     * Check if attribute is translatable.
     *
     * @param string $key
     * @return bool
     */
    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributeNames());
    }

    /**
     * Encode translations to JSON.
     *
     * @param array $translations
     * @return string
     */
    protected function encodeTranslations(array $translations): string
    {
        return json_encode($translations, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Decode translations from JSON.
     *
     * @param string $value
     * @return array
     */
    protected function decodeTranslations(string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Fallback: treat as single locale value
        return [$this->getCurrentLocale() => $value];
    }

    /**
     * Get current locale.
     *
     * @return string
     */
    protected function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get fallback locale.
     *
     * @return string
     */
    protected function getFallbackLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Override getAttribute to handle translations.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($this->isTranslatableAttribute($key)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Override setAttribute to handle translations.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatableAttribute($key)) {
            return $this->setTranslation($key, $value);
        }

        return parent::setAttribute($key, $value);
    }
}
