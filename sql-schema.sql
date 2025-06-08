
Blogz Database
-- ============================================================================
-- ENHANCED BLOG DATABASE SCHEMA WITH APPROVAL SYSTEM
-- ============================================================================

create table users(
    id int primary key auto_increment,
    firstname varchar(255) not null,
    lastname varchar(255) not null, 
    email varchar(255) not null, 
    password varchar(255) not null,
    is_admin TINYINT(1) DEFAULT 0 COMMENT 'Admin role: 0=user, 1=admin',
    role ENUM('user', 'admin', 'editor', 'moderator') DEFAULT 'user' COMMENT 'User role',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Create categories table for blog organization
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    post_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Enhanced blog_posts table with approval system
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT COMMENT 'Short description/summary',
    content LONGTEXT NOT NULL COMMENT 'Full blog post content',
    featured_image VARCHAR(500) COMMENT 'Path to main blog image',
    status ENUM('draft', 'pending_review', 'approved', 'published', 'rejected', 'archived') DEFAULT 'draft',
    admin_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' COMMENT 'Admin approval status',
    rejection_reason TEXT COMMENT 'Reason for rejection by admin',
    approved_by INT COMMENT 'Admin who approved/rejected',
    approved_at TIMESTAMP NULL,
    is_featured TINYINT(1) DEFAULT 0 COMMENT 'Featured post flag',
    view_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    comment_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_admin_status (admin_status),
    INDEX idx_published_at (published_at),
    INDEX idx_slug (slug),
    INDEX idx_featured (is_featured)
);

-- 4. Create blog_images table for managing multiple images per post
CREATE TABLE blog_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blog_post_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    display_order INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    file_size INT COMMENT 'File size in bytes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (blog_post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    INDEX idx_blog_post_id (blog_post_id),
    INDEX idx_display_order (display_order)
);

-- 5. Create tags table for blog tagging system
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Create blog_post_tags junction table (many-to-many relationship)
CREATE TABLE blog_post_tags (
    blog_post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (blog_post_id, tag_id),
    
    FOREIGN KEY (blog_post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- 7. Enhanced comments table with moderation
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blog_post_id INT NOT NULL,
    user_id INT,
    author_name VARCHAR(100) COMMENT 'For guest comments',
    author_email VARCHAR(255) COMMENT 'For guest comments',
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    moderated_by INT COMMENT 'Admin who moderated',
    moderated_at TIMESTAMP NULL,
    parent_id INT COMMENT 'For reply comments',
    like_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (blog_post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (moderated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_blog_post_id (blog_post_id),
    INDEX idx_status (status),
    INDEX idx_parent_id (parent_id)
);

-- 8. Post likes/reactions table
CREATE TABLE post_reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blog_post_id INT NOT NULL,
    user_id INT NOT NULL,
    reaction_type ENUM('like', 'love', 'helpful', 'insightful') DEFAULT 'like',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (blog_post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_post_reaction (blog_post_id, user_id),
    INDEX idx_blog_post_id (blog_post_id),
    INDEX idx_user_id (user_id)
);

-- 9. Activity log for admin actions
CREATE TABLE admin_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action_type ENUM('approve_post', 'reject_post', 'delete_post', 'moderate_comment', 'ban_user') NOT NULL,
    target_type ENUM('post', 'comment', 'user') NOT NULL,
    target_id INT NOT NULL,
    details JSON COMMENT 'Additional action details',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin_id (admin_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at)
);

