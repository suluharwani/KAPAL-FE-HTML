<?php namespace App\Controllers;

use App\Models\ContactModel;

class Contact extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Kontak Kami - Raja Ampat Boat Services',
            'active' => 'contact'
        ];
        
        return $this->render('contact/index', $data);
    }
    
    public function submit()
    {
        $model = new ContactModel();
        
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'subject' => 'required',
            'message' => 'required|min_length[10]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status' => 'unread'
        ];
        
        $model->save($data);
        
        return redirect()->to('/contact')->with('message', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
    }
}