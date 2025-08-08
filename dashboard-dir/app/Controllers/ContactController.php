<?php namespace App\Controllers;

use App\Models\ContactModel;

class ContactController extends BaseController
{
    protected $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        
        $data = [
            'title' => 'Contact Messages',
            'contacts' => $this->contactModel->getContacts($status),
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/contacts/index', $data);
    }

    public function show($id)
    {
        $contact = $this->contactModel->find($id);
        if (!$contact) {
            return redirect()->to('/admin/contacts')->with('error', 'Message not found');
        }

        // Mark as read
        if ($contact['status'] == 'unread') {
            $this->contactModel->update($id, ['status' => 'read']);
        }

        $data = [
            'title' => 'Message Details',
            'contact' => $contact,
            'user' => [
                'name' => $this->session->get('full_name'),
                'role' => $this->session->get('role')
            ]
        ];
        return view('admin/contacts/show', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $validStatuses = ['unread', 'read', 'replied', 'spam'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->contactModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Status updated');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function delete($id)
    {
        if ($this->contactModel->delete($id)) {
            return redirect()->to('/admin/contacts')->with('success', 'Message deleted');
        } else {
            return redirect()->to('/admin/contacts')->with('error', 'Failed to delete message');
        }
    }
}