-- ============================================================
-- Azaxm Portfolio CMS - Database Setup
-- Compatible with MySQL 5.7+
-- ============================================================

CREATE DATABASE IF NOT EXISTS azaxm_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE azaxm_portfolio;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    last_ip VARCHAR(45),
    failed_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: profile
-- ============================================================
CREATE TABLE IF NOT EXISTS profile (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL DEFAULT 'Azaxm',
    title VARCHAR(200) DEFAULT 'Full-stack Developer',
    bio TEXT,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    email VARCHAR(100),
    phone VARCHAR(20),
    location VARCHAR(100),
    experience_years INT DEFAULT 0,
    projects_completed INT DEFAULT 0,
    github_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: skill_categories
-- ============================================================
CREATE TABLE IF NOT EXISTS skill_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    display_order INT DEFAULT 0
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: skills
-- ============================================================
CREATE TABLE IF NOT EXISTS skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    level INT DEFAULT 0,
    display_order INT DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: social_links
-- ============================================================
CREATE TABLE IF NOT EXISTS social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    username VARCHAR(100),
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: contact_messages
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: website_settings
-- ============================================================
CREATE TABLE IF NOT EXISTS website_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text','textarea','boolean','image') DEFAULT 'text'
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: login_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success','failed') DEFAULT 'failed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: page_visits
-- ============================================================
CREATE TABLE IF NOT EXISTS page_visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page VARCHAR(100),
    ip_address VARCHAR(45),
    user_agent TEXT,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Admin user
-- Password: Admin@123
-- Generated with: password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$12$LxFMmMdT9pNM6vHJ5e.2ZupYfNtHm/zN8y3QzLvTwW5oN2NiJqHCy', 'admin@azaxm.com');

-- Profile default
INSERT INTO profile (full_name, title, bio, email, location, experience_years, projects_completed) VALUES
('Azaxm',
 'Full-stack Developer | Bot Developer | Mobile Developer',
 'Saya adalah seorang developer berpengalaman yang bersemangat dalam menciptakan solusi digital inovatif. Spesialisasi saya meliputi pengembangan website full-stack, pembuatan bot cerdas untuk berbagai platform (WhatsApp, Telegram, Discord), serta pengembangan aplikasi mobile cross-platform. Dengan pengalaman lebih dari 5 tahun di industri teknologi, saya selalu berkomitmen untuk menghadirkan kode yang bersih, aman, dan efisien.',
 'azaxm@example.com',
 'Indonesia',
 5,
 50);

-- Skill categories
INSERT INTO skill_categories (name, display_order) VALUES
('Programming Languages', 1),
('Frameworks & Tools', 2),
('Bot & Mobile', 3);

-- Skills - Programming Languages
INSERT INTO skills (category_id, name, icon, level, display_order, is_visible) VALUES
(1, 'JavaScript', 'fab fa-js', 90, 1, 1),
(1, 'PHP', 'fab fa-php', 85, 2, 1),
(1, 'Python', 'fab fa-python', 80, 3, 1),
(1, 'TypeScript', 'fas fa-code', 75, 4, 1),
(1, 'Java', 'fab fa-java', 70, 5, 1),
(1, 'Kotlin', 'fab fa-android', 65, 6, 1);

-- Skills - Frameworks
INSERT INTO skills (category_id, name, icon, level, display_order, is_visible) VALUES
(2, 'Node.js', 'fab fa-node-js', 85, 1, 1),
(2, 'Express.js', 'fas fa-server', 80, 2, 1),
(2, 'React.js', 'fab fa-react', 75, 3, 1),
(2, 'Laravel', 'fab fa-laravel', 70, 4, 1),
(2, 'Flutter', 'fas fa-mobile-alt', 65, 5, 1),
(2, 'Docker', 'fab fa-docker', 60, 6, 1);

-- Skills - Bot & Mobile
INSERT INTO skills (category_id, name, icon, level, display_order, is_visible) VALUES
(3, 'WhatsApp Bot', 'fab fa-whatsapp', 90, 1, 1),
(3, 'Telegram Bot', 'fab fa-telegram', 85, 2, 1),
(3, 'Discord Bot', 'fab fa-discord', 80, 3, 1),
(3, 'Android Dev', 'fab fa-android', 70, 4, 1);

-- Social links
INSERT INTO social_links (platform, username, url, icon, display_order, is_active) VALUES
('Telegram', '@azaxm', 'https://t.me/azaxm', 'fab fa-telegram', 1, 1),
('GitHub', 'azaxm', 'https://github.com/azaxm', 'fab fa-github', 2, 1),
('LinkedIn', 'azaxm', 'https://linkedin.com/in/azaxm', 'fab fa-linkedin', 3, 1),
('Instagram', '@azaxm', 'https://instagram.com/azaxm', 'fab fa-instagram', 4, 1),
('TikTok', '@azaxm', 'https://tiktok.com/@azaxm', 'fab fa-tiktok', 5, 1),
('Email', 'azaxm@example.com', 'mailto:azaxm@example.com', 'fas fa-envelope', 6, 1);

-- Website settings
INSERT INTO website_settings (setting_key, setting_value, setting_type) VALUES
('site_title', 'Azaxm | Portfolio', 'text'),
('site_description', 'Portfolio Azaxm - Full-stack Developer, Bot Developer, Mobile Developer', 'textarea'),
('footer_text', '© 2024 Azaxm. All rights reserved.', 'text'),
('maps_embed_url', '', 'text'),
('maintenance_mode', '0', 'boolean');
