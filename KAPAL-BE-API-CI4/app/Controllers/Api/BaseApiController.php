<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class BaseApiController extends BaseController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        helper('jwt');
    }

    protected function failValidationErrors($errors)
    {
        return $this->respond([
            'status' => 422,
            'errors' => $errors,
            'message' => 'Validation failed'
        ], 422);
    }

    protected function respondCreated($data = null, string $message = '')
    {
        $response = [
            'status' => 201,
            'message' => $message ?: 'Resource created successfully'
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return $this->respond($response, 201);
    }

    protected function respondUpdated($data = null, string $message = '')
    {
        $response = [
            'status' => 200,
            'message' => $message ?: 'Resource updated successfully'
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return $this->respond($response);
    }

    protected function respondDeleted(string $message = '')
    {
        return $this->respond([
            'status' => 200,
            'message' => $message ?: 'Resource deleted successfully'
        ]);
    }

    protected function respondNotFound(string $message = '')
    {
        return $this->respond([
            'status' => 404,
            'message' => $message ?: 'Resource not found'
        ], 404);
    }

    protected function getPaginationParams()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort');
        $order = $this->request->getGet('order') ?? 'asc';

        return [
            'page' => (int) $page,
            'per_page' => (int) $perPage,
            'search' => $search,
            'sort' => $sort,
            'order' => $order
        ];
    }
}