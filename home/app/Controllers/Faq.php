<?php namespace App\Controllers;

use App\Models\FaqModel;

class Faq extends BaseController
{
    public function index()
    {
        $model = new FaqModel();
        
        $data = [
            'title' => 'FAQ - Raja Ampat Boat Services',
            'faqs' => $model->orderBy('display_order', 'ASC')->findAll(),
            'active' => 'faq'
        ];
        
        return $this->render('faq/index', $data);
    }
}