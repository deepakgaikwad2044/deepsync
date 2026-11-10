<?php

namespace App\Middleware;

class VerifyCsrfToken
{
    /**
     * URIs that should be excluded from CSRF verification.
     */
    protected array $except = [
        '/api/*',
        '/webhook/*'
    ];

    public function handle()
    {
        if ($this->isReadRequest() || $this->inExceptArray()) {
            return;
        }

        $this->verifyContentType();
        $this->verifyOrigin();
        $this->verifyReferer();
        $this->verifyToken();
    }

    protected function isReadRequest(): bool
    {
        return in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD', 'OPTIONS']);
    }

    protected function verifyContentType(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (!is_string($contentType) || trim($contentType) === '') {
            $this->rejectUnsupportedMediaType();
        }

        $parts = explode(';', $contentType);
        $mediaType = strtolower(trim(array_shift($parts)));

        $parameters = [];

        foreach ($parts as $part) {
            if (!preg_match(
                '/^\s*([a-zA-Z0-9!#$&^_.+-]+)\s*=\s*(?:"([^"]*)"|([^;\s]+))\s*$/D',
                $part,
                $matches
            )) {
                $this->rejectUnsupportedMediaType();
            }

            $name = strtolower($matches[1]);
            $value = $matches[2] !== ''
                ? $matches[2]
                : ($matches[3] ?? '');

            if (array_key_exists($name, $parameters)) {
                $this->rejectUnsupportedMediaType();
            }

            $parameters[$name] = $value;
        }

        if ($mediaType === 'application/x-www-form-urlencoded') {
            if (
                count($parameters) > 1 ||
                (count($parameters) === 1 &&
                    (!isset($parameters['charset']) ||
                        strtolower($parameters['charset']) !== 'utf-8'))
            ) {
                $this->rejectUnsupportedMediaType();
            }

            return;
        }

        if ($mediaType === 'multipart/form-data') {
            if (
                count($parameters) !== 1 ||
                !isset($parameters['boundary']) ||
                !preg_match(
                    "/^[-A-Za-z0-9'()+_,.\\/:=?]{1,70}$/",
                    $parameters['boundary']
                )
            ) {
                $this->rejectUnsupportedMediaType();
            }

            return;
        }

        if ($mediaType === 'application/json') {
            if (
                count($parameters) > 1 ||
                (count($parameters) === 1 &&
                    (!isset($parameters['charset']) ||
                        strtolower($parameters['charset']) !== 'utf-8'))
            ) {
                $this->rejectUnsupportedMediaType();
            }

            return;
        }

        $this->rejectUnsupportedMediaType();
    }

    protected function rejectUnsupportedMediaType(): void
    {
        http_response_code(415);
        exit('Unsupported Media Type.');
    }

    protected function verifyOrigin(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;

        // Preserve token validation when Origin is absent.
        if ($origin === null || trim($origin) === '') {
            return;
        }

        // Reject comma-joined or ambiguous Origin values.
        if (str_contains($origin, ",")) {
            $this->rejectRequest("Invalid CSRF origin.");
        }

        $appUrl = env('APP_URL');

        if (!is_string($appUrl) || trim($appUrl) === '') {
            $this->rejectRequest('CSRF origin configuration missing.');
        }

        $originParts = parse_url($origin);
        $appParts = parse_url($appUrl);

        if (
            !is_array($originParts) ||
            !is_array($appParts) ||
            empty($originParts['scheme']) ||
            empty($originParts['host']) ||
            empty($appParts['scheme']) ||
            empty($appParts['host'])
        ) {
            $this->rejectRequest('Invalid CSRF origin.');
        }

        $originScheme = strtolower($originParts['scheme']);
        $appScheme = strtolower($appParts['scheme']);

        $originHost = strtolower($originParts['host']);
        $appHost = strtolower($appParts['host']);

        $originPort = $originParts['port']
            ?? ($originScheme === 'https' ? 443 : 80);

        $appPort = $appParts['port']
            ?? ($appScheme === 'https' ? 443 : 80);

        if (
            $originScheme !== $appScheme ||
            $originHost !== $appHost ||
            $originPort !== $appPort ||
            isset($originParts['user']) ||
            isset($originParts['pass'])
        ) {
            $this->rejectRequest('Foreign origin rejected.');
        }
    }

    protected function verifyReferer(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        // Preserve token validation when Referer is absent.
        if ($referer === null || trim($referer) === '') {
            return;
        }

        $appUrl = env('APP_URL');

        if (!is_string($appUrl) || trim($appUrl) === '') {
            $this->rejectRequest('CSRF referer configuration missing.');
        }

        $refererParts = parse_url($referer);
        $appParts = parse_url($appUrl);

        if (
            !is_array($refererParts) ||
            !is_array($appParts) ||
            empty($refererParts['scheme']) ||
            empty($refererParts['host']) ||
            empty($appParts['scheme']) ||
            empty($appParts['host'])
        ) {
            $this->rejectRequest('Invalid CSRF referer.');
        }

        $refererScheme = strtolower($refererParts['scheme']);
        $appScheme = strtolower($appParts['scheme']);

        $refererHost = strtolower($refererParts['host']);
        $appHost = strtolower($appParts['host']);

        $refererPort = $refererParts['port']
            ?? ($refererScheme === 'https' ? 443 : 80);

        $appPort = $appParts['port']
            ?? ($appScheme === 'https' ? 443 : 80);

        if (
            $refererScheme !== $appScheme ||
            $refererHost !== $appHost ||
            $refererPort !== $appPort ||
            isset($refererParts['user']) ||
            isset($refererParts['pass'])
        ) {
            $this->rejectRequest('Foreign referer rejected.');
        }
    }

    protected function verifyToken(): void
    {
        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (
            !is_string($token) ||
            trim($token) === '' ||
            !$this->tokensMatch($token)
        ) {
            $this->rejectRequest('CSRF token mismatch.');
        }
    }

    protected function rejectRequest(string $message): void
    {
        http_response_code(419);
        exit($message);
    }

    protected function tokensMatch(string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (!is_string($sessionToken) || trim($sessionToken) === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    protected function inExceptArray(): bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->except as $except) {
            if ($this->matchUri($except, $uri)) {
                return true;
            }
        }

        return false;
    }

    protected function matchUri(string $pattern, string $uri): bool
    {
        $pattern = str_replace('*', '.*', $pattern);

        return preg_match("#^{$pattern}$#", $uri);
    }
}
