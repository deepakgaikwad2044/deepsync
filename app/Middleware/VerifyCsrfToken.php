<?php

namespace App\Middleware;

class VerifyCsrfToken
{
    /**
     * URIs that should be excluded from CSRF verification
     */
    protected array $except = [
        '/api/*',
        '/webhook/*'
    ];

   public function handle()
{
    if ($this->isReadRequest()) {
        return;
    }

    $this->verifyToken();
}


    protected function isReadRequest(): bool
    {
        return in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD', 'OPTIONS']);
    }

    protected function verifyToken(): void
    {
        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (!$token || !$this->tokensMatch($token)) {
            http_response_code(419);
            exit('CSRF token mismatch.');
        }
    }

    protected function tokensMatch(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
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
        // Convert /api/* to regex
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match("#^{$pattern}$#", $uri);
    }
}
