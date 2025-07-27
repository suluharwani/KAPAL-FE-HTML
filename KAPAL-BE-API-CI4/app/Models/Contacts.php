<?php namespace App\Controllers\Api;

use App\Models\ContactModel;
use App\Libraries\NotificationService;

class Contacts extends BaseApiController
{
    protected $modelName = ContactModel::class;

    public function __construct()
    {
        $this->model = new ContactModel();
        $this->notification = new NotificationService();
    }

    public function index()
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can view contacts');
        }

        $params = $this->getPaginationParams();
        $params['status'] = $this->request->getGet('status');

        $contacts = $this->model->getPaginated($params);

        return $this->respond([
            'status' => 200,
            'data' => $contacts['data'],
            'pagination' => $contacts['pagination']
        ]);
    }

    public function show($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can view contact details');
        }

        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->respondNotFound('Contact not found');
        }

        return $this->respond([
            'status' => 200,
            'data' => $contact
        ]);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'permit_empty|max_length[20]',
            'subject' => 'required|min_length[5]|max_length[255]',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'subject' => $this->request->getVar('subject'),
            'message' => $this->request->getVar('message'),
            'status' => 'unread'
        ];

        $contactId = $this->model->insert($data);

        if ($contactId) {
            // Send notification to admin
            $this->notification->sendNewContactNotification($data);

            return $this->respondCreated(['contact_id' => $contactId]);
        } else {
            return $this->failServerError('Failed to submit contact form');
        }
    }

    public function updateStatus($id = null)
    {
        if ($this->request->user->role !== 'admin') {
            return $this->failForbidden('Only admin can update contact status');
        }

        $rules = [
            'status' => 'required|in_list[unread,read,replied,spam]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->respondNotFound('Contact not found');
        }

        $status = $this->request->getVar('status');

        if ($this->model->update($id, ['status' => $status])) {
            return $this->respondUpdated(['contact_id' => $id]);
        } else {
            return $this->failServerError('Failed to update contact status');
        }
    }
}