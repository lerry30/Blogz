<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.5rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            color: #666;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .page-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 2rem;
            color: #333;
            font-weight: 600;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }

        /* Content Sections */
        .content-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Posts Grid */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .post-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
        }

        .post-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 3rem;
        }

        .post-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .post-meta {
            display: flex;
            gap: 1rem;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .post-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .post-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .post-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-published {
            background: rgba(34, 197, 94, 0.2);
            color: #16a34a;
        }

        .status-draft {
            background: rgba(249, 115, 22, 0.2);
            color: #ea580c;
        }

        .status-archived {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 75px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: #f5f5f5;
            color: #333;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: -100%;
                z-index: 999;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                padding: 1rem;
            }

            .posts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-blog"></i>
                    BlogManager
                </div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="posts">
                        <i class="fas fa-edit"></i>
                        My Posts
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="new-post">
                        <i class="fas fa-plus"></i>
                        New Post
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="categories">
                        <i class="fas fa-tags"></i>
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="media">
                        <i class="fas fa-images"></i>
                        Media Library
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="settings">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <div class="page-header">
                    <h1 class="page-title">Dashboard</h1>
                    <button class="btn btn-primary" onclick="openModal('newPostModal')">
                        <i class="fas fa-plus"></i>
                        New Post
                    </button>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Total Posts</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Published</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Drafts</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">1,247</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>

                <div class="posts-grid">
                    <div class="post-card">
                        <div class="post-image">
                            <i class="fas fa-image"></i>
                        </div>
                        <h3 class="post-title">Getting Started with JavaScript ES6</h3>
                        <div class="post-meta">
                            <span><i class="fas fa-calendar"></i> Dec 20, 2024</span>
                            <span><i class="fas fa-eye"></i> 156 views</span>
                            <span class="status-badge status-published">Published</span>
                        </div>
                        <p class="post-excerpt">Learn the fundamentals of ES6 features including arrow functions, destructuring, and template literals...</p>
                        <div class="post-actions">
                            <button class="btn btn-secondary">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </div>
                    </div>

                    <div class="post-card">
                        <div class="post-image">
                            <i class="fas fa-image"></i>
                        </div>
                        <h3 class="post-title">Web Development Best Practices</h3>
                        <div class="post-meta">
                            <span><i class="fas fa-calendar"></i> Dec 18, 2024</span>
                            <span><i class="fas fa-eye"></i> 89 views</span>
                            <span class="status-badge status-draft">Draft</span>
                        </div>
                        <p class="post-excerpt">A comprehensive guide to modern web development practices and conventions...</p>
                        <div class="post-actions">
                            <button class="btn btn-secondary">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-upload"></i>
                                Publish
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Posts Section -->
            <section id="posts" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">My Posts</h1>
                    <button class="btn btn-primary" onclick="openModal('newPostModal')">
                        <i class="fas fa-plus"></i>
                        New Post
                    </button>
                </div>
                <div id="posts-container">
                    <!-- Posts will be loaded here -->
                    <p>Loading posts...</p>
                </div>
            </section>

            <!-- New Post Section -->
            <section id="new-post" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Create New Post</h1>
                </div>
                <div style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 20px; backdrop-filter: blur(10px);">
                    <form id="newPostForm">
                        <div class="form-group">
                            <label class="form-label">Post Title</label>
                            <input type="text" class="form-input" name="title" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">Select Category</option>
                                <option value="1">Technology</option>
                                <option value="2">Lifestyle</option>
                                <option value="3">Business</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Featured Image</label>
                            <input type="file" class="form-input" name="image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Excerpt</label>
                            <textarea class="form-textarea" name="excerpt" placeholder="Brief description of your post..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-input" name="tags" placeholder="javascript, tutorial, web development">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save as Draft
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="publishPost()">
                                <i class="fas fa-upload"></i>
                                Publish Now
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Other sections (categories, media, settings) would go here -->
            <section id="categories" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Categories</h1>
                </div>
                <p>Manage your blog categories here...</p>
            </section>

            <section id="media" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Media Library</h1>
                </div>
                <p>Manage your uploaded images and files...</p>
            </section>

            <section id="settings" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Settings</h1>
                </div>
                <p>Configure your blog settings...</p>
            </section>
        </main>
    </div>

    <!-- New Post Modal -->
    <div id="newPostModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Quick Post Creation</h2>
                <button class="close-btn" onclick="closeModal('newPostModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-input" placeholder="Enter post title...">
                </div>
                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea class="form-textarea" placeholder="Start writing your post..."></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Post</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('newPostModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Navigation functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and sections
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Show corresponding section
                const sectionId = this.getAttribute('data-section');
                document.getElementById(sectionId).classList.add('active');
            });
        });

        // Modal functionality
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Form submission
        document.getElementById('newPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const postData = {
                title: formData.get('title'),
                category: formData.get('category'),
                excerpt: formData.get('excerpt'),
                tags: formData.get('tags'),
                status: 'draft'
            };
            
            console.log('Saving post:', postData);
            
            // Here you would send the data to your server
            // Example AJAX call:
            /*
            fetch('/api/posts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Post saved:', data);
                alert('Post saved successfully!');
                this.reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving post');
            });
            */
            
            alert('Post saved as draft!');
            this.reset();
        });

        // Publish post function
        function publishPost() {
            const form = document.getElementById('newPostForm');
            const formData = new FormData(form);
            
            const postData = {
                title: formData.get('title'),
                category: formData.get('category'),
                excerpt: formData.get('excerpt'),
                tags: formData.get('tags'),
                status: 'published'
            };
            
            console.log('Publishing post:', postData);
            alert('Post published successfully!');
            form.reset();
        }

        // Load posts function (simulated)
        function loadPosts() {
            // Simulate loading posts from server
            const postsContainer = document.getElementById('posts-container');
            
            setTimeout(() => {
                postsContainer.innerHTML = `
                    <div class="posts-grid">
                        <div class="post-card">
                            <div class="post-image">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3 class="post-title">Advanced JavaScript Concepts</h3>
                            <div class="post-meta">
                                <span><i class="fas fa-calendar"></i> Dec 22, 2024</span>
                                <span><i class="fas fa-eye"></i> 234 views</span>
                                <span class="status-badge status-published">Published</span>
                            </div>
                            <p class="post-excerpt">Deep dive into closures, prototypes, and async programming...</p>
                            <div class="post-actions">
                                <button class="btn btn-secondary" onclick="editPost(1)">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button class="btn btn-secondary" onclick="viewPost(1)">
                                    <i class="fas fa-eye"></i>
                                    View
                                </button>
                                <button class="btn btn-danger" onclick="deletePost(1)">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                        
                        <div class="post-card">
                            <div class="post-image">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3 class="post-title">CSS Grid vs Flexbox</h3>
                            <div class="post-meta">
                                <span><i class="fas fa-calendar"></i> Dec 20, 2024</span>
                                <span><i class="fas fa-eye"></i> 189 views</span>
                                <span class="status-badge status-published">Published</span>
                            </div>
                            <p class="post-excerpt">When to use CSS Grid and when to use Flexbox for layouts...</p>
                            <div class="post-actions">
                                <button class="btn btn-secondary" onclick="editPost(2)">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button class="btn btn-secondary" onclick="viewPost(2)">
                                    <i class="fas fa-eye"></i>
                                    View
                                </button>
                                <button class="btn btn-danger" onclick="deletePost(2)">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                        
                        <div class="post-card">
                            <div class="post-image">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3 class="post-title">React Hooks Tutorial</h3>
                            <div class="post-meta">
                                <span><i class="fas fa-calendar"></i> Dec 18, 2024</span>
                                <span><i class="fas fa-eye"></i> 45 views</span>
                                <span class="status-badge status-draft">Draft</span>
                            </div>
                            <p class="post-excerpt">Complete guide to React Hooks including useState, useEffect...</p>
                            <div class="post-actions">
                                <button class="btn btn-secondary" onclick="editPost(3)">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button class="btn btn-primary" onclick="publishDraft(3)">
                                    <i class="fas fa-upload"></i>
                                    Publish
                                </button>
                                <button class="btn btn-danger" onclick="deletePost(3)">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }, 500);
        }

        // Post management functions
        function editPost(postId) {
            console.log('Editing post:', postId);
            // Here you would redirect to edit page or open edit modal
            alert(`Editing post ${postId}`);
        }

        function viewPost(postId) {
            console.log('Viewing post:', postId);
            // Here you would open the post in a new tab/window
            window.open(`/blog/post/${postId}`, '_blank');
        }

        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                console.log('Deleting post:', postId);
                // Here you would make API call to delete post
                /*
                fetch(`/api/posts/${postId}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    if (response.ok) {
                        alert('Post deleted successfully');
                        loadPosts(); // Reload posts
                    }
                })
                .catch(error => {
                    console.error('Error deleting post:', error);
                    alert('Error deleting post');
                });
                */
                alert(`Post ${postId} deleted successfully`);
            }
        }

        function publishDraft(postId) {
            if (confirm('Are you sure you want to publish this post?')) {
                console.log('Publishing post:', postId);
                // Here you would make API call to update post status
                /*
                fetch(`/api/posts/${postId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: 'published' })
                })
                .then(response => response.json())
                .then(data => {
                    alert('Post published successfully');
                    loadPosts(); // Reload posts
                })
                .catch(error => {
                    console.error('Error publishing post:', error);
                    alert('Error publishing post');
                });
                */
                alert(`Post ${postId} published successfully`);
            }
        }

        // File upload handling
        document.addEventListener('change', function(e) {
            if (e.target.type === 'file') {
                const file = e.target.files[0];
                if (file) {
                    console.log('File selected:', file.name);
                    // Here you would handle file upload to server
                    /*
                    const formData = new FormData();
                    formData.append('image', file);
                    
                    fetch('/api/upload', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('File uploaded:', data.url);
                        // Update form with uploaded file URL
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                    });
                    */
                }
            }
        });

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadPosts();
            
            // Set up any other initialization
            console.log('Dashboard initialized');
        });

        // Mobile sidebar toggle (for responsive design)
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Add mobile menu button if needed
        if (window.innerWidth <= 768) {
            const mainContent = document.querySelector('.main-content');
            const menuButton = document.createElement('button');
            menuButton.innerHTML = '<i class="fas fa-bars"></i>';
            menuButton.style.cssText = `
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1001;
                background: rgba(255,255,255,0.9);
                border: none;
                padding: 1rem;
                border-radius: 50%;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                cursor: pointer;
            `;
            menuButton.onclick = toggleSidebar;
            document.body.appendChild(menuButton);
        }
    </script>
</body>
</html>
