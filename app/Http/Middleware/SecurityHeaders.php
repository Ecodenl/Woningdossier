<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @throws RandomException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate nonce per request for inline <script> tags
        $nonce = Vite::useCspNonce();
        $request->attributes->set('cspNonce', $nonce);
        // Make it available in views via $csp-nonce
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // X-Frame-Options (legacy) -> frame-ancestors in CSP.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Referrer-Policy
        $response->headers->set('Referrer-Policy', config('hoomdossier.security.referrer_policy', 'strict-origin-when-cross-origin'));
        // Permissions-Policy (previously Feature-Policy)
        $response->headers->set('Permissions-Policy', config('hoomdossier.security.permissions_policy', "accelerometer=(), camera=(), microphone=(), geolocation=(), fullscreen=(self), usb=(), payment=()"));

        // HSTS: only for HTTPS, not on dev environment
        if ($request->isSecure() && ! app()->environment(['local', 'testing'])) {
            $response->headers->set(
                'Strict-Transport-Security',
                config('hoomdossier.security.hsts', 'max-age=31536000; includeSubDomains; preload')
            );
        }

        // Use different policies for production and development:
        if (! app()->environment(['local', 'testing'])) {
            $csp = $this->productionCsp($nonce);
        } else {
            $csp = $this->developmentCsp($nonce);
        }

        // Allow reporting (e.g. on accept) if needed
        if (config('hoomdossier.security.csp_report_only', false)) {
            $response->headers->set('Content-Security-Policy-Report-Only', $csp);
        } else {
            $response->headers->set('Content-Security-Policy', $csp);
        }

        if ($response->isClientError() || $response->isServerError()) {
            $content = $response->getContent();
            $contentWithNonce = $this->addNonceToInlineScripts($content, $nonce);

            $response->setContent($contentWithNonce);
        }

        return $response;
    }

    protected function addNonceToInlineScripts(string $content, string $nonce) : string
    {
        return preg_replace_callback('/<script(.*?)>(.*?)<\/script>/is', function ($match) use ($nonce) {
            $attributes = $match[1];
            $scriptContent = $match[2];

            if (! str_contains($attributes, 'nonce')) {
                $attributes .= " nonce=\"$nonce\"";
            }

            return "<script$attributes>$scriptContent</script>";
        }, $content);
    }

    /**
     * Returns composed CSP string for production environment.
     * @param string $nonce
     * @return string
     */
    protected function productionCsp(string $nonce): string
    {
        // Add CDN, assets / other domains here if needed
        // We prefer not to use 'unsafe-inline' whenever possible and to use nonce + 'strict-dynamic' (for modern browsers) or trusted domains
        // to narrow down the allowed sources.
        $scriptSrc = ["'self'", "'nonce-{$nonce}'", "https://cdn.ravenjs.com/", "'strict-dynamic'"];
        $styleSrc = ["'self'", "'unsafe-inline'"];
        // Use 'unsafe-inline' for inline <style> tags or style attributes of libraries.
        if (config('hoomdossier.security.allow_unsafe_inline_styles', false)) {
            $styleSrc[] = "'unsafe-inline'";
        }

        return implode(
            ' ',
            [
                "default-src 'self';",
                "base-uri 'self';",
                "frame-ancestors 'self';",
                "object-src 'none';",
                "img-src 'self' data: https:;",
                "font-src 'self' data:;",
                // https: for Livewire fetch/XHR. Note we also allow websockets here (wss:) already for future use.
                "connect-src 'self' https: wss:;",
                'script-src ' . implode(' ', $scriptSrc) . ';',
                'style-src ' . implode(' ', $styleSrc) . ';',
                'upgrade-insecure-requests',
            ]
        );
    }

    /**
     * Returns composed CSP string for development environment.
     * @param string $nonce
     * @return string
     */
    protected function developmentCsp(string $nonce): string
    {
        // Vite dev server host (with port). Look at the VITE_PORT in the .env file (if you changed it).
        $viteHost = rtrim(config('hoomdossier.security.vite_host', 'http://localhost:5173'), '/');
        $viteWs   = preg_replace('~^http~', 'ws', $viteHost);

        // Vite (http) and WebSocket (ws) are required for development.
        $scriptSrc = ["'self'", "'nonce-{$nonce}'", "https://cdn.ravenjs.com", "'unsafe-inline'",  $viteHost];
        if (app()->environment(['local', 'testing'])) {
            $scriptSrc[] = "'unsafe-eval'"; // for eval() in some dev tools
        }
        $styleSrc = ["'self'", "'unsafe-inline'", "https://fonts.bunny.net", $viteHost];
        $connectSrc = ["'self'", "https:", $viteHost, $viteWs];
        $imgSrc     = ["'self'", "data:", "https:", $viteHost];
        $fontSrc    = ["'self'", "data:", "https://fonts.bunny.net", $viteHost];
        $workerSrc  = ["'self'", "blob:"];

        return implode(
            ' ',
            [
                "default-src 'self';",
                "base-uri 'self';",
                "frame-ancestors 'self';",
                "object-src 'none';",
                'img-src ' . implode(' ', $imgSrc) . ';',
                'font-src ' . implode(' ', $fontSrc) . ';',
                'connect-src ' . implode(' ', $connectSrc) . ';',
                'script-src ' . implode(' ', $scriptSrc) . ';',
                'style-src ' . implode(' ', $styleSrc) . ';',
                'worker-src ' . implode(' ', $workerSrc) . ';',
                'style-src-elem ' . implode(' ', $styleSrc) . ';',
            ]
        );
    }
}
