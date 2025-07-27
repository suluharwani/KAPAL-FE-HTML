<?php namespace App\Controllers\Api;

/**
 * @OA\Info(title="Raja Ampat Boats API", version="1.0")
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class Docs extends BaseApiController
{
    public function index()
    {
        $openapi = \OpenApi\scan(APPPATH . 'Controllers/Api');
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }
}