CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    email VARCHAR(255),
    picture VARCHAR(500),
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    session_token VARCHAR(255) DEFAULT NULL,
    last_seen DATETIME DEFAULT NULL,
    is_blocked TINYINT(1) DEFAULT 0, 
    blocked_until DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE credits_buy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    credits_added DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    status ENUM('processing','succeeded','failed'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE credits_used (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    credit_used DECIMAL(10,2) DEFAULT 1.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(500)
);

CREATE TABLE book_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    type ENUM(
    'introduction',
    'title',
    'chapter',
    'article',
    'sub_article',
    'subject',
    'section',
    'paragraph',
    'item',
    'sub_item'
    ) NOT NULL,
    parent_id INT DEFAULT NULL,
    position INT DEFAULT 0,
    title VARCHAR(255) DEFAULT NULL,
    content TEXT DEFAULT NULL
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_messages_created_at (created_at),
    INDEX idx_messages_user_id (user_id)
);

CREATE TABLE message_reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    reaction VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reaction (message_id, user_id), -- one reaction per user per message
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE banned_words (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pattern VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `users`;


DROP TABLE IF EXISTS `verbal`;
DROP TABLE IF EXISTS `numerical`;
DROP TABLE IF EXISTS `analytical`;
DROP TABLE IF EXISTS `general`;

CREATE TABLE `verbal` (
    `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category` VARCHAR(100) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `sub_type` VARCHAR(100) DEFAULT NULL,
    `sub_sub_type` VARCHAR(100) DEFAULT NULL,  
    `instruction` TEXT DEFAULT NULL,  
    `word` VARCHAR(100) DEFAULT NULL, 
    `question` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `chart_data` JSON DEFAULT NULL,
    `correct_answer` VARCHAR(255) NOT NULL,
    `wrong_answer1` VARCHAR(255) NOT NULL,
    `wrong_answer2` VARCHAR(255) NOT NULL,
    `wrong_answer3` VARCHAR(255) NOT NULL,
    `explanation` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id`),
    KEY `idx_category_type_word` (`category`, `type`, `word`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `numerical` (
    `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category` VARCHAR(100) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `sub_type` VARCHAR(100) DEFAULT NULL,
    `sub_sub_type` VARCHAR(100) DEFAULT NULL,
    `instruction` TEXT DEFAULT NULL,  
    `word` VARCHAR(100) DEFAULT NULL,
    `question` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `chart_data` JSON DEFAULT NULL,
    `correct_answer` VARCHAR(255) NOT NULL,
    `wrong_answer1` VARCHAR(255) NOT NULL,
    `wrong_answer2` VARCHAR(255) NOT NULL,
    `wrong_answer3` VARCHAR(255) NOT NULL,
    `explanation` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `analytical` (
    `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category` VARCHAR(100) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `sub_type` VARCHAR(100) DEFAULT NULL,
    `sub_sub_type` VARCHAR(100) DEFAULT NULL,
    `instruction` TEXT DEFAULT NULL,    
    `word` VARCHAR(100) DEFAULT NULL,
    `question` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `chart_data` JSON DEFAULT NULL,
    `correct_answer` VARCHAR(255) NOT NULL,
    `wrong_answer1` VARCHAR(255) NOT NULL,
    `wrong_answer2` VARCHAR(255) NOT NULL,
    `wrong_answer3` VARCHAR(255) NOT NULL,
    `explanation` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `general` (
    `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category` VARCHAR(100) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `sub_type` VARCHAR(100) DEFAULT NULL,   
    `sub_sub_type` VARCHAR(100) DEFAULT NULL, 
    `instruction` TEXT DEFAULT NULL,  
    `word` VARCHAR(100) NOT NULL,
    `question` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `chart_data` JSON DEFAULT NULL,
    `correct_answer` VARCHAR(255) NOT NULL,
    `wrong_answer1` VARCHAR(255) NOT NULL,
    `wrong_answer2` VARCHAR(255) NOT NULL,
    `wrong_answer3` VARCHAR(255) NOT NULL,
    `explanation` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





DELETE bw1
FROM banned_words bw1
JOIN banned_words bw2
  ON bw1.pattern = bw2.pattern
  AND bw1.id > bw2.id;

ALTER TABLE banned_words
DROP COLUMN id,
DROP COLUMN created_at;





DELETE v1
FROM analytical v1
JOIN analytical v2
  ON v1.category = v2.category
  AND v1.type = v2.type
  AND v1.sub_type = v2.sub_type
  AND v1.question = v2.question
  AND v1.correct_answer = v2.correct_answer
  AND v1.id > v2.id;


ALTER TABLE `analytical`
DROP COLUMN `id`,
DROP COLUMN `image`,
DROP COLUMN `word`,
DROP COLUMN `chart_data`,
DROP COLUMN `created_at`;


