<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();
        $ipAddress = $request->getIPAddress();
        
        // Sanitize the IP address for cache key safety
        $cacheKey = $this->sanitizeCacheKey($ipAddress);
        
        // Limit to 100 requests per minute per IP
        if ($throttler->check($cacheKey, 100, MINUTE) === false) {
            return Services::response()
                ->setStatusCode(429)
                ->setJSON([
                    'status' => 429,
                    'error' => 429,
                    'messages' => [
                        'error' => 'Too many requests. Please try again later.'
                    ]
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
    
    /**
     * Sanitizes a string to be used as a cache key
     * 
     * @param string $key The original key
     * @return string The sanitized key
     */
    protected function sanitizeCacheKey(string $key): string
    {
        // Replace reserved characters with underscores
        $reservedChars = ['{', '}', '(', ')', '/', '\\', '@', ':'];
        return str_replace($reservedChars, '_', $key);
    }
}