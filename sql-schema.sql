-- 1. Alter users table to add role/admin field
ALTER TABLE users 
ADD COLUMN is_admin TINYINT(1) DEFAULT 0 COMMENT 'Admin role: 0=user, 1=admin',
ADD COLUMN role ENUM('user', 'admin', 'editor') DEFAULT 'user' COMMENT 'User role',
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 2. Create categories table for blog organization
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Create blog_posts table
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT COMMENT 'Short description/summary',
    content_file_path VARCHAR(500) COMMENT 'Path to .md file on server',
    featured_image VARCHAR(500) COMMENT 'Path to main blog image',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0 COMMENT 'Featured post flag',
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_slug (slug)
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

-- 7. Create comments table (optional - for user engagement)
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blog_post_id INT NOT NULL,
    user_id INT,
    author_name VARCHAR(100) COMMENT 'For guest comments',
    author_email VARCHAR(255) COMMENT 'For guest comments',
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    parent_id INT COMMENT 'For reply comments',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (blog_post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_blog_post_id (blog_post_id),
    INDEX idx_status (status),
    INDEX idx_parent_id (parent_id)
);

-- 8. Insert sample categories
INSERT INTO categories (name, slug, description) VALUES
('Technology', 'technology', 'Posts about technology and programming'),
('Lifestyle', 'lifestyle', 'Lifestyle and personal development posts'),
('Business', 'business', 'Business and entrepreneurship content'),
('Travel', 'travel', 'Travel experiences and guides');

-- 9. Insert sample tags
INSERT INTO tags (name, slug) VALUES
('JavaScript', 'javascript'),
('Web Development', 'web-development'),
('Tutorial', 'tutorial'),
('Tips', 'tips'),
('Review', 'review');

-- 10. Useful queries for blog management

-- Get all published posts with author and category info
SELECT 
    bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image,
    bp.published_at, bp.view_count,
    u.firstname, u.lastname,
    c.name as category_name
FROM blog_posts bp
LEFT JOIN users u ON bp.user_id = u.id
LEFT JOIN categories c ON bp.category_id = c.id
WHERE bp.status = 'published'
ORDER BY bp.published_at DESC;

-- Get posts by specific user
SELECT * FROM blog_posts 
WHERE user_id = ? 
ORDER BY created_at DESC;

-- Get post with tags
SELECT 
    bp.*,
    GROUP_CONCAT(t.name) as tags
FROM blog_posts bp
LEFT JOIN blog_post_tags bpt ON bp.id = bpt.blog_post_id
LEFT JOIN tags t ON bpt.tag_id = t.id
WHERE bp.id = ?
GROUP BY bp.id;

-- Update view count
UPDATE blog_posts 
SET view_count = view_count + 1 
WHERE id = ?;
