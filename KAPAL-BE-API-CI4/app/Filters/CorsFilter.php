<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Set header CORS
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN'] ?? '*');
        $response->setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        // Handle preflight request
        if ($request->getMethod() === 'options') {
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}