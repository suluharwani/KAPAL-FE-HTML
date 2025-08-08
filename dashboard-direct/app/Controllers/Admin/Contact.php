<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactModel;

class Contact extends BaseController
{
    protected $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? null;
        
        $data = [
            'contacts' => $this->contactModel->getContacts($status),
            'statusFilter' => $status,
            'unreadCount' => $this->contactModel->getUnreadCount()
        ];

        return view('admin/contact/index', $data);
    }

    public function view($id)
    {
        $contact = $this->contactModel->find($id);
        if (!$contact) {
            return redirect()->to('/admin/contact')->with('error', 'Contact message not found');
        }

        // Mark as read if currently unread
        if ($contact['status'] === 'unread') {
            $this->contactModel->updateStatus($id, 'read');
            $contact['status'] = 'read';
        }

        return view('admin/contact/view', ['contact' => $contact]);
    }

    public function markAsReplied($id)
    {
        if ($this->contactModel->updateStatus($id, 'replied')) {
            return redirect()->to('/admin/contact')->with('success', 'Marked as replied');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function markAsSpam($id)
    {
        if ($this->contactModel->updateStatus($id, 'spam')) {
            return redirect()->to('/admin/contact')->with('success', 'Marked as spam');
        } else {
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function delete($id)
    {
        if ($this->contactModel->delete($id)) {
            return redirect()->to('/admin/contact')->with('success', 'Contact message deleted');
        } else {
            return redirect()->to('/admin/contact')->with('error', 'Failed to delete message');
        }
    }
}