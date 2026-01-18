-- Database schema for Jan Suraksha Portal
CREATE DATABASE IF NOT EXISTS jan_suraksha DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jan_suraksha;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY(email),
  UNIQUE KEY(mobile)
) ENGINE=InnoDB;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id VARCHAR(100) NOT NULL UNIQUE,
  admin_name VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT 0,
  complaint_code VARCHAR(100) NOT NULL UNIQUE,
  complainant_name VARCHAR(255) DEFAULT NULL,
  mobile VARCHAR(50) DEFAULT NULL,
  crime_type VARCHAR(100),
  date_filed DATETIME DEFAULT CURRENT_TIMESTAMP,
  location TEXT,
  description TEXT,
  evidence VARCHAR(255),
  status VARCHAR(50) DEFAULT 'Submitted',
  is_anonymous TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Flag: 1 = Anonymous, 0 = Regular',
  anonymous_tracking_id VARCHAR(100) DEFAULT NULL COMMENT 'Unique tracking ID for anonymous complaints',
  updated_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  UNIQUE KEY unique_anonymous_tracking_id (anonymous_tracking_id),
  INDEX idx_is_anonymous (is_anonymous),
  INDEX idx_anonymous_lookup (is_anonymous, anonymous_tracking_id)
) ENGINE=InnoDB;

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  excerpt TEXT,
  content LONGTEXT,
  image VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  subject VARCHAR(255),
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Criminals table: stores persons linked to complaints/cases
CREATE TABLE criminals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  complaint_id INT DEFAULT NULL,
  full_name VARCHAR(255) NOT NULL,
  fathers_name VARCHAR(255),
  aliases VARCHAR(255),
  dob DATE DEFAULT NULL,
  age INT DEFAULT NULL,
  physical_description TEXT,
  last_known_address TEXT,
  mugshot VARCHAR(255),
  current_status VARCHAR(100),
  punishment_section VARCHAR(255),
  punishment_description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Case diary entries for internal notes by admins
CREATE TABLE case_diary (
  id INT AUTO_INCREMENT PRIMARY KEY,
  complaint_id INT NOT NULL,
  admin_id INT DEFAULT NULL,
  note_text TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
