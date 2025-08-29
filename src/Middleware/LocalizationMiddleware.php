<?php

declare(strict_types=1);

namespace Litepie\Trans\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Litepie\Trans\Trans;
use Illuminate\Support\Facades\Redirect;

/**
 * Class LocalizationMiddleware
 *
 * Laravel 12 compatible middleware for handling automatic locale detection and setting.
 * This middleware should be applied to routes that need localization.
 *
 * @package Litepie\Trans\Middleware
 */
class LocalizationMiddleware
{
    /**
     * Trans instance.
     */
    protected Trans $trans;

    /**
     * Create a new LocalizationMiddleware instance.
     *
     * @param Trans $trans
     */
    public function __construct(Trans $trans)
    {
        $this->trans = $trans;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return SymfonyResponse
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Set the locale based on the URL or request
        $locale = $this->determineLocale($request);
        
        // Set the application locale
        $this->trans->setLocale($locale);
        
        // Check if we need to redirect to add/remove locale from URL
        if ($redirectResponse = $this->getLocaleRedirectResponse($request, $locale)) {
            return $redirectResponse;
        }

        return $next($request);
    }

    /**
     * Determine the appropriate locale for the request.
     *
     * @param Request $request
     * @return string|null
     */
    protected function determineLocale(Request $request): ?string
    {
        // First, try to get locale from URL segment
        $urlLocale = $request->segment(1);
        
        if ($urlLocale && $this->trans->checkLocaleInSupportedLocales($urlLocale)) {
            return $urlLocale;
        }

        // Check session for saved locale preference
        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get(\config('trans.localeSessionKey', 'locale'));
            if ($sessionLocale && $this->trans->checkLocaleInSupportedLocales($sessionLocale)) {
                return $sessionLocale;
            }
        }

        // Check cookie for saved locale preference
        $cookieName = \config('trans.localeCookie.name', 'locale');
        $cookieLocale = $request->cookie($cookieName);
        if ($cookieLocale && $this->trans->checkLocaleInSupportedLocales($cookieLocale)) {
            return $cookieLocale;
        }

        // Use language negotiation if enabled
        if (\config('trans.useAcceptLanguageHeader', true)) {
            return null; // Let Trans handle negotiation
        }

        return $this->trans->getDefaultLocale();
    }

    /**
     * Check if we need to redirect to properly localized URL.
     *
     * @param Request $request
     * @param string|null $locale
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function getLocaleRedirectResponse(Request $request, ?string $locale)
    {
        $currentUrl = $request->fullUrl();
        $locale = $locale ?? $this->trans->getCurrentLocale();

        // Don't redirect for AJAX requests or if already redirecting
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return null;
        }

        // Check if auto-redirect is enabled
        if (!\config('trans.autoDetectLocale', true)) {
            return null;
        }

        // Get the properly localized URL
        $localizedUrl = $this->trans->getLocalizedURL($locale, $currentUrl);

        // Redirect if URLs don't match
        if ($localizedUrl !== $currentUrl) {
            $response = Redirect::to($localizedUrl, 302);
            
            // Set locale cookie if configured
            if (\config('trans.localeCookie.name')) {
                $cookie = $this->createLocaleCookie($locale);
                $response->withCookie($cookie);
            }

            return $response;
        }

        return null;
    }

    /**
     * Create a locale cookie.
     *
     * @param string $locale
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function createLocaleCookie(string $locale)
    {
        $config = \config('trans.localeCookie');

        return \cookie(
            $config['name'],
            $locale,
            $config['minutes'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            $config['httpOnly'],
            false,
            $config['sameSite']
        );
    }
}
