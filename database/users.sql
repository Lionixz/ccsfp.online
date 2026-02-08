
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





DROP TABLE IF EXISTS applicants;

CREATE TABLE applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    users_id VARCHAR(255) NOT NULL,
    course_first VARCHAR(100) NOT NULL,
    course_second VARCHAR(100) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    age INT,
    gender VARCHAR(20),
    dob DATE,
    birth_place VARCHAR(255),
    marital_status VARCHAR(50),
    contact VARCHAR(50),
    religion VARCHAR(50),
    email VARCHAR(100),
    home_address VARCHAR(255),
    relative_name VARCHAR(255),
    relative_address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
