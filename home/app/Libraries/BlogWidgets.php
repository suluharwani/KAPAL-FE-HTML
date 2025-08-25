<?php
namespace App\Libraries;

class BlogWidgets
{
    // Categories widget
    public function categories($params)
    {
        $categories = $params['categories'];
        
        $html = '<div class="card mb-4">';
        $html .= '<div class="card-header bg-primary text-white">';
        $html .= '<h5 class="mb-0"><i class="fas fa-folder me-2"></i>Kategori</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<ul class="list-unstyled mb-0">';
        
        foreach ($categories as $category) {
            $html .= '<li class="mb-2">';
            $html .= '<a href="' . base_url('blog/category/' . $category['slug']) . '" class="text-decoration-none d-flex justify-content-between align-items-center">';
            $html .= '<span>' . $category['category_name'] . '</span>';
            $html .= '<span class="badge bg-primary rounded-pill">' . $category['post_count'] . '</span>';
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    // Recent posts widget
    public function recentPosts($params)
    {
        $recentPosts = $params['recentPosts'];
        
        $html = '<div class="card mb-4">';
        $html .= '<div class="card-header bg-primary text-white">';
        $html .= '<h5 class="mb-0"><i class="fas fa-clock me-2"></i>Postingan Terbaru</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<ul class="list-unstyled mb-0">';
        
        foreach ($recentPosts as $post) {
            $html .= '<li class="mb-3 pb-2 border-bottom">';
            $html .= '<a href="' . base_url('blog/' . $post['slug']) . '" class="text-decoration-none">';
            $html .= '<h6 class="mb-1">' . $post['title'] . '</h6>';
            $html .= '</a>';
            $html .= '<small class="text-muted">' . date('d M Y', strtotime($post['published_at'])) . '</small>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    // Newsletter widget
    public function newsletter()
    {
        $html = '<div class="card mb-4">';
        $html .= '<div class="card-header bg-primary text-white">';
        $html .= '<h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Berlangganan Newsletter</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<p class="card-text">Dapatkan update terbaru tentang promo dan informasi wisata Raja Ampat.</p>';
        $html .= '<form>';
        $html .= '<div class="mb-3">';
        $html .= '<input type="email" class="form-control" placeholder="Alamat email Anda" required>';
        $html .= '</div>';
        $html .= '<button type="submit" class="btn btn-primary w-100">Berlangganan</button>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}