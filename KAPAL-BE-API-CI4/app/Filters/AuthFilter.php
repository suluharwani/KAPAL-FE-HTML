<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Config\Jwt as JwtConfig; // Renamed this import
use Firebase\JWT\JWT; // Keep this as is
use Firebase\JWT\Key;
use Exception;

class AuthFilter implements FilterInterface
{
    protected $jwtConfig;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig(); // Using the renamed class
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');
        $token = null;

        // Extract token from header
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }
        
        // Try to get token from cookie if not in header
        if (!$token && $request->hasHeader('Cookie')) {
            $cookies = [];
            parse_str(strtr($request->getHeader('Cookie')->getValue(), ['&' => '%26', '+' => '%2B', ';' => '&']), $cookies);
            $token = $cookies['jwt_token'] ?? null;
        }

        if (!$token) {
            return $this->unauthorizedResponse('Authorization token is required');
        }

        // Validate token
        $decodedToken = $this->validateJWTToken($token);

        if (!$decodedToken) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // Store user data in request
        $request->user = (object) $decodedToken;

        // Check for required roles if specified
        if (!empty($arguments)) {
            if (!in_array($request->user->role, $arguments)) {
                return $this->forbiddenResponse('You are not authorized to access this resource');
            }
        }

        // Check if token needs refreshing
        $this->checkTokenRefresh($request, $token);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    protected function validateJWTToken(string $token): ?array
    {
        try {
            $secretKey = $this->jwtConfig->getSecretKey();
            $decoded = JWT::decode($token, new Key($secretKey, $this->jwtConfig->algorithm));
            return (array) $decoded;
        } catch (Exception $e) {
            log_message('error', 'JWT Validation Error: ' . $e->getMessage());
            return null;
        }
    }

    protected function unauthorizedResponse($message)
    {
        return Services::response()
            ->setJSON([
                'status'  => 401,
                'message' => $message,
                'error'   => 'Unauthorized'
            ])
            ->setStatusCode(401);
    }

    protected function forbiddenResponse($message)
    {
        return Services::response()
            ->setJSON([
                'status'  => 403,
                'message' => $message,
                'error'   => 'Forbidden'
            ])
            ->setStatusCode(403);
    }

    protected function checkTokenRefresh(RequestInterface $request, $currentToken)
    {
        $decoded = $this->validateJWTToken($currentToken);
        
        if (!$decoded || !isset($decoded['iat'])) {
            return;
        }
        
        $issuedAt = $decoded['iat'];
        $refreshThreshold = time() - (int) ($this->jwtConfig->expiration / 2);
        
        if ($issuedAt < $refreshThreshold) {
            $newToken = $this->refreshJWTToken($currentToken);
            
            if ($newToken) {
                Services::response()->setHeader('X-New-Token', $newToken);
                
                Services::response()->setCookie(
                    'jwt_token',
                    $newToken,
                    [
                        'expires'  => time() + $this->jwtConfig->expiration,
                        'path'     => '/',
                        'domain'   => '',
                        'secure'   => ENVIRONMENT === 'production',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
            }
        }
    }

    protected function refreshJWTToken(string $token): ?string
    {
        try {
            $decoded = $this->validateJWTToken($token);
            if (!$decoded) {
                return null;
            }

            $decoded['iat'] = time();
            $decoded['exp'] = time() + $this->jwtConfig->expiration;

            return JWT::encode($decoded, $this->jwtConfig->getSecretKey(), $this->jwtConfig->algorithm);
        } catch (Exception $e) {
            log_message('error', 'JWT Refresh Error: ' . $e->getMessage());
            return null;
        }
    }
}