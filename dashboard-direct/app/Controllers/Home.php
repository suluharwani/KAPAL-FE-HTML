<?php namespace App\Controllers;

use App\Models\BoatModel;
use App\Models\BlogModel;
use App\Models\TestimonialModel;
use App\Models\GalleryModel;
use App\Models\FaqModel;
use App\Models\IslandModel;

class Home extends BaseController
{
    public function index()
    {
        $boatModel = new BoatModel();
        $blogModel = new BlogModel();
        $testimonialModel = new TestimonialModel();
        $galleryModel = new GalleryModel();
        $islandModel = new IslandModel();

        $data = [
            'featuredBoats' => $boatModel->orderBy('created_at', 'DESC')->findAll(3),
            'latestBlogs' => $blogModel->where('status', 'published')
                ->where('published_at <=', date('Y-m-d H:i:s'))
                ->orderBy('published_at', 'DESC')
                ->findAll(3),
            'testimonials' => $testimonialModel->where('status', 'approved')
                ->orderBy('created_at', 'DESC')
                ->findAll(5),
            'gallery' => $galleryModel->where('is_featured', 1)
                ->orderBy('created_at', 'DESC')
                ->findAll(8),
            'islands' => $islandModel->findAll(6)
        ];

        return view('home', $data);
    }

    public function boats()
    {
        $boatModel = new BoatModel();
        $data = [
            'boats' => $boatModel->findAll()
        ];

        return view('boats', $data);
    }

    public function boatDetail($id)
    {
        $boatModel = new BoatModel();
        $boat = $boatModel->find($id);

        if (!$boat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('boat_detail', ['boat' => $boat]);
    }

    public function schedules()
    {
        $scheduleModel = new ScheduleModel();
        $islandModel = new IslandModel();

        $data = [
            'schedules' => $scheduleModel->getSchedulesWithDetails(),
            'islands' => $islandModel->findAll()
        ];

        return view('schedules', $data);
    }

    public function blogs()
    {
        $blogModel = new BlogModel();
        $blogCategoryModel = new BlogCategoryModel();

        $category = $this->request->getGet('category') ?? null;
        $search = $this->request->getGet('search') ?? null;

        $builder = $blogModel->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'DESC');

        if ($category) {
            $builder->where('category_id', $category);
        }

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('content', $search)
                ->groupEnd();
        }

        $data = [
            'blogs' => $builder->paginate(6),
            'pager' => $blogModel->pager,
            'categories' => $blogCategoryModel->findAll(),
            'categoryFilter' => $category,
            'searchQuery' => $search
        ];

        return view('blogs', $data);
    }

    public function blogDetail($slug)
    {
        $blogModel = new BlogModel();
        $blogCategoryModel = new BlogCategoryModel();

        $blog = $blogModel->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->first();

        if (!$blog) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'blog' => $blog,
            'categories' => $blogCategoryModel->findAll(),
            'recentBlogs' => $blogModel->where('status', 'published')
                ->where('published_at <=', date('Y-m-d H:i:s'))
                ->where('blog_id !=', $blog['blog_id'])
                ->orderBy('published_at', 'DESC')
                ->findAll(3)
        ];

        return view('blog_detail', $data);
    }

    public function gallery()
    {
        $galleryModel = new GalleryModel();
        $category = $this->request->getGet('category') ?? null;

        $builder = $galleryModel->orderBy('created_at', 'DESC');

        if ($category) {
            $builder->where('category', $category);
        }

        $data = [
            'gallery' => $builder->paginate(12),
            'pager' => $galleryModel->pager,
            'categoryFilter' => $category
        ];

        return view('gallery', $data);
    }

    public function faqs()
    {
        $faqModel = new FaqModel();
        $category = $this->request->getGet('category') ?? null;

        $builder = $faqModel->orderBy('display_order', 'ASC');

        if ($category) {
            $builder->where('category', $category);
        }

        $data = [
            'faqs' => $builder->findAll(),
            'categories' => $faqModel->getCategories(),
            'categoryFilter' => $category
        ];

        return view('faqs', $data);
    }

    public function contact()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email',
                'subject' => 'required|min_length[5]|max_length[255]',
                'message' => 'required|min_length[10]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $contactModel = new ContactModel();
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'subject' => $this->request->getPost('subject'),
                'message' => $this->request->getPost('message'),
                'status' => 'unread'
            ];

            if ($contactModel->save($data)) {
                return redirect()->to('/contact')->with('success', 'Your message has been sent. We will contact you soon.');
            } else {
                return redirect()->back()->with('error', 'Failed to send message. Please try again.');
            }
        }

        return view('contact');
    }

    public function about()
    {
        $testimonialModel = new TestimonialModel();
        $data = [
            'testimonials' => $testimonialModel->where('status', 'approved')
                ->orderBy('created_at', 'DESC')
                ->findAll(6)
        ];

        return view('about', $data);
    }
}