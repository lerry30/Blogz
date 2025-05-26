-- ============================================================================
-- ENHANCED BLOG DATABASE SCHEMA WITH APPROVAL SYSTEM
-- ============================================================================

-- 1. Alter users table to add role/admin fields
ALTER TABLE users 
ADD COLUMN is_admin TINYINT(1) DEFAULT 0 COMMENT 'Admin role: 0=user, 1=admin',
ADD COLUMN role ENUM('user', 'admin', 'editor', 'moderator') DEFAULT 'user' COMMENT 'User role',
ADD COLUMN status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

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

-- ============================================================================
-- SAMPLE DATA INSERTION
-- ============================================================================

-- Insert sample users with different roles
INSERT INTO users (id, firstname, lastname, email, is_admin, role, status) VALUES
(1, 'Admin', 'User', 'admin@blog.com', 1, 'admin', 'active'),
(2, 'John', 'Editor', 'editor@blog.com', 0, 'editor', 'active'),
(3, 'Sarah', 'Moderator', 'moderator@blog.com', 0, 'moderator', 'active'),
(4, 'Alice', 'Johnson', 'alice@example.com', 0, 'user', 'active'),
(5, 'Bob', 'Smith', 'bob@example.com', 0, 'user', 'active'),
(6, 'Carol', 'Davis', 'carol@example.com', 0, 'user', 'active'),
(7, 'David', 'Wilson', 'david@example.com', 0, 'user', 'inactive'),
(8, 'Eva', 'Brown', 'eva@example.com', 0, 'user', 'banned');

-- Insert categories
INSERT INTO categories (name, slug, description, is_active, post_count) VALUES
('Technology', 'technology', 'Posts about technology, programming, and software development', 1, 0),
('Lifestyle', 'lifestyle', 'Lifestyle tips, personal development, and wellness', 1, 0),
('Business', 'business', 'Business strategies, entrepreneurship, and marketing', 1, 0),
('Travel', 'travel', 'Travel experiences, guides, and destination reviews', 1, 0),
('Food', 'food', 'Recipes, restaurant reviews, and culinary experiences', 1, 0),
('Health', 'health', 'Health tips, fitness, and medical insights', 1, 0),
('Education', 'education', 'Learning resources, tutorials, and educational content', 1, 0);

-- Insert tags
INSERT INTO tags (name, slug, usage_count) VALUES
('JavaScript', 'javascript', 0),
('Web Development', 'web-development', 0),
('Tutorial', 'tutorial', 0),
('Tips', 'tips', 0),
('Review', 'review', 0),
('Beginner', 'beginner', 0),
('Advanced', 'advanced', 0),
('React', 'react', 0),
('Node.js', 'nodejs', 0),
('Database', 'database', 0),
('Career', 'career', 0),
('Productivity', 'productivity', 0),
('Travel Guide', 'travel-guide', 0),
('Recipe', 'recipe', 0),
('Fitness', 'fitness', 0);

-- Insert sample blog posts with various statuses
INSERT INTO blog_posts (user_id, category_id, title, slug, excerpt, content, featured_image, status, admin_status, approved_by, approved_at, is_featured, view_count, like_count, published_at) VALUES

-- Published and approved posts
(4, 1, 'Getting Started with React Hooks', 'getting-started-react-hooks', 
 'Learn the fundamentals of React Hooks and how they can simplify your components.', 
 'React Hooks revolutionized how we write React components. In this comprehensive guide, we\'ll explore useState, useEffect, and custom hooks...', 
 '/images/react-hooks-featured.jpg', 'published', 'approved', 1, '2024-01-15 10:30:00', 1, 2500, 45, '2024-01-15 12:00:00'),

(5, 1, 'Building RESTful APIs with Node.js', 'building-restful-apis-nodejs', 
 'A complete guide to creating robust REST APIs using Node.js and Express.', 
 'REST APIs are the backbone of modern web applications. This tutorial covers everything from basic routing to advanced authentication...', 
 '/images/nodejs-api-featured.jpg', 'published', 'approved', 1, '2024-01-20 09:15:00', 0, 1800, 32, '2024-01-20 14:00:00'),

(6, 2, '10 Morning Habits for Better Productivity', 'morning-habits-productivity', 
 'Transform your mornings with these science-backed habits that boost productivity.', 
 'Your morning routine sets the tone for the entire day. These 10 habits have been proven to increase focus, energy, and overall productivity...', 
 '/images/morning-routine-featured.jpg', 'published', 'approved', 2, '2024-01-25 08:45:00', 1, 3200, 78, '2024-01-25 10:00:00'),

-- Posts pending admin approval
(4, 1, 'Advanced JavaScript Patterns', 'advanced-javascript-patterns', 
 'Explore advanced JavaScript design patterns and their practical applications.', 
 'Design patterns in JavaScript help create maintainable and scalable code. This article covers Singleton, Observer, Factory patterns...', 
 '/images/js-patterns-featured.jpg', 'pending_review', 'pending', NULL, NULL, 0, 0, 0, NULL),

(5, 3, 'Startup Funding: A Complete Guide', 'startup-funding-complete-guide', 
 'Everything you need to know about raising funds for your startup.', 
 'Securing funding is one of the biggest challenges for startups. This comprehensive guide covers angel investors, VCs, crowdfunding...', 
 '/images/startup-funding-featured.jpg', 'pending_review', 'pending', NULL, NULL, 0, 0, 0, NULL),

-- Rejected posts
(6, 4, 'My Trip to Mars', 'my-trip-to-mars', 
 'An incredible journey to the red planet (not really).', 
 'Last week I took a trip to Mars and met some aliens. They were really friendly and taught me their language...', 
 '/images/mars-trip-featured.jpg', 'rejected', 'rejected', 1, '2024-01-22 16:30:00', 0, 0, 0, NULL),

-- Draft posts
(4, 1, 'Database Optimization Techniques', 'database-optimization-techniques', 
 'Learn how to optimize your database queries for better performance.', 
 'Database performance is crucial for web applications. This article covers indexing, query optimization, caching strategies...', 
 NULL, 'draft', 'pending', NULL, NULL, 0, 0, 0, NULL),

(5, 2, 'Work-Life Balance in Tech', 'work-life-balance-tech', 
 'Maintaining healthy work-life balance in the demanding tech industry.', 
 'The tech industry is known for its demanding pace. Here are strategies to maintain mental health and productivity...', 
 NULL, 'draft', 'pending', NULL, NULL, 0, 0, 0, NULL);

-- Update rejection reason for rejected post
UPDATE blog_posts 
SET rejection_reason = 'Content appears to be fictional and misleading. Please submit factual, well-researched content.'
WHERE slug = 'my-trip-to-mars';

-- Insert blog post tags relationships
INSERT INTO blog_post_tags (blog_post_id, tag_id) VALUES
(1, 1), (1, 2), (1, 6), -- React Hooks: JavaScript, Web Development, Beginner
(2, 2), (2, 9), (2, 3), -- Node.js API: Web Development, Node.js, Tutorial
(3, 11), (3, 12), (3, 4), -- Morning Habits: Career, Productivity, Tips
(4, 1), (4, 7), -- Advanced JS: JavaScript, Advanced
(5, 11), (5, 4), -- Startup Guide: Career, Tips
(7, 10), (7, 7), -- Database: Database, Advanced
(8, 11), (8, 12); -- Work-Life: Career, Productivity

-- Insert sample comments with different statuses
INSERT INTO comments (blog_post_id, user_id, content, status, moderated_by, moderated_at) VALUES
(1, 5, 'Great tutorial! Really helped me understand hooks better.', 'approved', 3, '2024-01-16 10:00:00'),
(1, 6, 'Could you do a follow-up on custom hooks?', 'approved', 3, '2024-01-16 10:05:00'),
(1, NULL, 'Anonymous Guest', 'guest@example.com', 'Thanks for sharing this!', 'approved', 3, '2024-01-17 14:20:00'),
(2, 4, 'Very comprehensive guide. Bookmarked for reference!', 'approved', 3, '2024-01-21 09:30:00'),
(3, 5, 'I tried these habits and my productivity increased by 50%!', 'approved', 2, '2024-01-26 11:15:00'),
(1, NULL, 'Spammer', 'spam@spam.com', 'Check out my amazing product at spam-link.com', 'spam', 3, '2024-01-18 16:45:00');

-- Insert sample post reactions
INSERT INTO post_reactions (blog_post_id, user_id, reaction_type) VALUES
(1, 5, 'like'),
(1, 6, 'helpful'),
(1, 2, 'love'),
(2, 4, 'like'),
(2, 6, 'helpful'),
(3, 4, 'love'),
(3, 5, 'like'),
(3, 6, 'insightful');

-- Insert admin activity log
INSERT INTO admin_activity_log (admin_id, action_type, target_type, target_id, details) VALUES
(1, 'approve_post', 'post', 1, '{"approved_at": "2024-01-15 10:30:00", "notes": "High quality content"}'),
(1, 'approve_post', 'post', 2, '{"approved_at": "2024-01-20 09:15:00", "notes": "Well structured tutorial"}'),
(2, 'approve_post', 'post', 3, '{"approved_at": "2024-01-25 08:45:00", "notes": "Excellent lifestyle content"}'),
(1, 'reject_post', 'post', 6, '{"rejected_at": "2024-01-22 16:30:00", "reason": "Fictional content not suitable"}'),
(3, 'moderate_comment', 'comment', 6, '{"action": "marked_as_spam", "reason": "Commercial spam content"}');

-- Update counters
UPDATE blog_posts SET like_count = (SELECT COUNT(*) FROM post_reactions WHERE blog_post_id = blog_posts.id);
UPDATE blog_posts SET comment_count = (SELECT COUNT(*) FROM comments WHERE blog_post_id = blog_posts.id AND status = 'approved');
UPDATE categories SET post_count = (SELECT COUNT(*) FROM blog_posts WHERE category_id = categories.id AND status = 'published');
UPDATE tags SET usage_count = (SELECT COUNT(*) FROM blog_post_tags WHERE tag_id = tags.id);

-- ============================================================================
-- USEFUL QUERIES FOR DIFFERENT SCENARIOS
-- ============================================================================

-- 1. Admin Dashboard: Posts pending approval
SELECT 
    bp.id, bp.title, bp.created_at,
    CONCAT(u.firstname, ' ', u.lastname) as author,
    c.name as category,
    bp.admin_status
FROM blog_posts bp
LEFT JOIN users u ON bp.user_id = u.id
LEFT JOIN categories c ON bp.category_id = c.id
WHERE bp.admin_status = 'pending'
ORDER BY bp.created_at ASC;

-- 2. Published posts for public display
SELECT 
    bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image,
    bp.published_at, bp.view_count, bp.like_count, bp.comment_count,
    CONCAT(u.firstname, ' ', u.lastname) as author,
    c.name as category_name, c.slug as category_slug
FROM blog_posts bp
LEFT JOIN users u ON bp.user_id = u.id
LEFT JOIN categories c ON bp.category_id = c.id
WHERE bp.status = 'published' AND bp.admin_status = 'approved'
ORDER BY bp.is_featured DESC, bp.published_at DESC;

-- 3. User's posts with all statuses (for author dashboard)
SELECT 
    bp.id, bp.title, bp.status, bp.admin_status, bp.rejection_reason,
    bp.view_count, bp.like_count, bp.comment_count, bp.created_at
FROM blog_posts bp
WHERE bp.user_id = 4  -- Replace with actual user ID
ORDER BY bp.created_at DESC;

-- 4. Post details with tags and comments
SELECT 
    bp.*,
    CONCAT(u.firstname, ' ', u.lastname) as author,
    c.name as category_name,
    GROUP_CONCAT(DISTINCT t.name) as tags,
    (SELECT COUNT(*) FROM comments WHERE blog_post_id = bp.id AND status = 'approved') as approved_comments
FROM blog_posts bp
LEFT JOIN users u ON bp.user_id = u.id
LEFT JOIN categories c ON bp.category_id = c.id
LEFT JOIN blog_post_tags bpt ON bp.id = bpt.blog_post_id
LEFT JOIN tags t ON bpt.tag_id = t.id
WHERE bp.slug = 'getting-started-react-hooks'  -- Replace with actual slug
GROUP BY bp.id;

-- 5. Comments for moderation
SELECT 
    c.id, c.content, c.status, c.created_at,
    bp.title as post_title,
    COALESCE(CONCAT(u.firstname, ' ', u.lastname), c.author_name) as commenter_name,
    COALESCE(u.email, c.author_email) as commenter_email
FROM comments c
LEFT JOIN blog_posts bp ON c.blog_post_id = bp.id
LEFT JOIN users u ON c.user_id = u.id
WHERE c.status = 'pending'
ORDER BY c.created_at ASC;

-- 6. Popular posts by views and engagement
SELECT 
    bp.id, bp.title, bp.view_count, bp.like_count, bp.comment_count,
    (bp.view_count + bp.like_count * 5 + bp.comment_count * 3) as engagement_score
FROM blog_posts bp
WHERE bp.status = 'published' AND bp.admin_status = 'approved'
ORDER BY engagement_score DESC
LIMIT 10;

-- 7. Category-wise post statistics
SELECT 
    c.name as category,
    COUNT(bp.id) as total_posts,
    SUM(CASE WHEN bp.status = 'published' THEN 1 ELSE 0 END) as published_posts,
    SUM(CASE WHEN bp.admin_status = 'pending' THEN 1 ELSE 0 END) as pending_approval,
    AVG(bp.view_count) as avg_views
FROM categories c
LEFT JOIN blog_posts bp ON c.id = bp.category_id
GROUP BY c.id, c.name
ORDER BY total_posts DESC;

-- 8. Admin activity summary
SELECT 
    CONCAT(u.firstname, ' ', u.lastname) as admin_name,
    aal.action_type,
    COUNT(*) as action_count,
    MAX(aal.created_at) as last_action
FROM admin_activity_log aal
JOIN users u ON aal.admin_id = u.id
WHERE aal.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY aal.admin_id, aal.action_type
ORDER BY admin_name, action_count DESC;

-- 9. Search posts by title or content
SELECT 
    bp.id, bp.title, bp.excerpt, bp.published_at,
    CONCAT(u.firstname, ' ', u.lastname) as author,
    MATCH(bp.title, bp.content) AGAINST('React JavaScript' IN NATURAL LANGUAGE MODE) as relevance
FROM blog_posts bp
LEFT JOIN users u ON bp.user_id = u.id
WHERE bp.status = 'published' 
    AND bp.admin_status = 'approved'
    AND MATCH(bp.title, bp.content) AGAINST('React JavaScript' IN NATURAL LANGUAGE MODE)
ORDER BY relevance DESC;

-- 10. User engagement metrics
SELECT 
    u.firstname, u.lastname,
    COUNT(DISTINCT bp.id) as posts_count,
    COUNT(DISTINCT c.id) as comments_count,
    COUNT(DISTINCT pr.id) as reactions_count,
    AVG(bp.view_count) as avg_post_views
FROM users u
LEFT JOIN blog_posts bp ON u.id = bp.user_id
LEFT JOIN comments c ON u.id = c.user_id
LEFT JOIN post_reactions pr ON u.id = pr.user_id
WHERE u.role = 'user' AND u.status = 'active'
GROUP BY u.id
ORDER BY posts_count DESC;
