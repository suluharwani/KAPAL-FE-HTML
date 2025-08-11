<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Blog Posts</h5>
        <a href="<?= base_url('admin/blogs/create') ?>" class="btn btn-primary btn-sm" id="addBlogBtn">
            <i class="bi bi-plus"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="blogsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit -->
<div class="modal fade" id="blogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="blogForm">
                    <input type="hidden" name="blog_id" id="blog_id">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this blog post?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load blogs data
    function loadBlogs() {
        $.ajax({
            url: '<?= base_url('admin/blogs') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let html = '';
                response.data.forEach((blog, index) => {
                    html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(blog.title)}</td>
                        <td>${escapeHtml(blog.category_name || 'Uncategorized')}</td>
                        <td>${blog.author_id}</td>
                        <td>
                            <span class="badge bg-${blog.status == 'published' ? 'success' : (blog.status == 'draft' ? 'warning text-dark' : 'secondary')}">
                                ${blog.status.charAt(0).toUpperCase() + blog.status.slice(1)}
                            </span>
                        </td>
                        <td>${blog.published_at ? formatDate(blog.published_at) : '-'}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button onclick="editBlog(${blog.blog_id})" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="showDeleteModal(${blog.blog_id})" class="btn btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                });
                $('#blogsTable tbody').html(html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'Failed to load blogs');
            }
        });
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    // Escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Show toast notification
    function showToast(type, message) {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
        $('body').append(toast);
        $('.toast').toast('show');
        setTimeout(() => $('.toast').remove(), 3000);
    }

    // Add new blog button click
    $('#addBlogBtn').click(function(e) {
        e.preventDefault();
        $('#modalTitle').text('Add New Blog Post');
        $('#blogForm')[0].reset();
        $('#blog_id').val('');
        
        // Load categories
        $.ajax({
            url: '<?= base_url('admin/blogs/create') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">Select Category</option>';
                response.categories.forEach(category => {
                    options += `<option value="${category.category_id}">${escapeHtml(category.category_name)}</option>`;
                });
                $('#category_id').html(options);
                $('#blogModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'Failed to load categories');
            }
        });
    });

    // Edit blog
    window.editBlog = function(blogId) {
        $('#modalTitle').text('Edit Blog Post');
        
        // Load blog data and categories
        $.ajax({
            url: `<?= base_url('admin/blogs/edit/') ?>${blogId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#blog_id').val(response.blog.blog_id);
                $('#title').val(response.blog.title);
                $('#content').val(response.blog.content);
                $('#excerpt').val(response.blog.excerpt);
                $('#status').val(response.blog.status);
                
                let options = '<option value="">Select Category</option>';
                response.categories.forEach(category => {
                    options += `<option value="${category.category_id}" ${category.category_id == response.blog.category_id ? 'selected' : ''}>${escapeHtml(category.category_name)}</option>`;
                });
                $('#category_id').html(options);
                
                $('#blogModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'Failed to load blog data');
            }
        });
    };

    // Form submission
    $('#blogForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const blogId = $('#blog_id').val();
        const url = blogId ? `<?= base_url('admin/blogs/update/') ?>${blogId}` : '<?= base_url('admin/blogs/store') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#blogModal').modal('hide');
                    showToast('success', response.message);
                    loadBlogs();
                    if (response.redirect) {
                        setTimeout(() => window.location.href = response.redirect, 1000);
                    }
                } else if (response.errors) {
                    // Show validation errors
                    Object.keys(response.errors).forEach(field => {
                        const input = $(`#${field}`);
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(response.errors[field]);
                    });
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'An error occurred');
            }
        });
    });

    // Reset form validation on modal hide
    $('#blogModal').on('hidden.bs.modal', function() {
        $('#blogForm input, #blogForm select, #blogForm textarea').removeClass('is-invalid');
    });

    // Show delete confirmation modal
    window.showDeleteModal = function(blogId) {
        $('#deleteModal').data('blog-id', blogId).modal('show');
    };

    // Confirm delete
    $('#confirmDeleteBtn').click(function() {
        const blogId = $('#deleteModal').data('blog-id');
        
        $.ajax({
            url: `<?= base_url('admin/blogs/delete/') ?>${blogId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#deleteModal').modal('hide');
                if (response.success) {
                    showToast('success', response.message);
                    loadBlogs();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'Failed to delete blog');
            }
        });
    });

    // Initial load
    loadBlogs();
});
</script>

<?= $this->include('templates/admin_footer') ?>