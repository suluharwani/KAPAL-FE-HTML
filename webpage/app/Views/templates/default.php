<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Raja Ampat Boat Services' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <style>
        /* Global Styles */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-primary {
            background: var(--secondary-color);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/images/raja-ampat-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        /* Booking Form */
        .booking-form {
            background: var(--light-color);
            padding: 3rem 0;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* Schedule Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background: var(--primary-color);
            color: white;
        }
        
        /* Features */
        .features {
            padding: 3rem 0;
            background: white;
        }
        
        .feature-item {
            text-align: center;
            padding: 20px;
            flex: 1;
            min-width: 250px;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .form-group {
                flex: 100%;
            }
        }
        /* Global Styles */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --accent-color: #e74c3c;
    --light-color: #ecf0f1;
    --dark-color: #2c3e50;
}

/* ... (CSS sebelumnya) ... */

/* Destination Styles */
.destination-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.destination-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.destination-card:hover {
    transform: translateY(-5px);
}

.destination-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.destination-info {
    padding: 1.5rem;
}

/* Blog Styles */
.blog-posts {
    display: grid;
    gap: 2rem;
}

.post-card {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1.5rem;
    align-items: center;
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.post-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
}

.post-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    color: #666;
}

/* FAQ Styles */
.faq-item {
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.faq-question {
    width: 100%;
    padding: 1rem;
    text-align: left;
    background: var(--light-color);
    border: none;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-answer {
    padding: 0 1rem;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-answer.show {
    padding: 1rem;
    max-height: 500px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .post-card {
        grid-template-columns: 1fr;
    }
    
    .contact-container {
        grid-template-columns: 1fr;
    }
}
    </style>
</head>
<body>
    <?= $this->include('partials/header') ?>
    
    <main class="container my-5">
        <?= $this->renderSection('content') ?>
    </main>
    
    <?= $this->include('partials/footer') ?>
    
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/6281234567890" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp whatsapp-float-icon"></i>
    </a>
    
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>