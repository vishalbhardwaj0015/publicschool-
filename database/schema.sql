-- =====================================================
-- HIM PUBLIC SCHOOL - Database schema
-- Run this in phpMyAdmin (or mysql client) against your
-- MySQL database, then edit the DATABASE_* env vars in Render.
-- =====================================================

CREATE DATABASE IF NOT EXISTS publicschool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE publicschool;

-- -----------------------------------------------------
-- Admin users
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  photo VARCHAR(255) DEFAULT 'img/admin/default.png',
  role VARCHAR(50) DEFAULT 'superadmin',
  status VARCHAR(20) DEFAULT 'active',
  last_login DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Classes
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(50) NOT NULL,
  section VARCHAR(20) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO classes (class_name, section) VALUES
('Nursery',''),('LKG',''),('UKG',''),
('Class 1',''),('Class 2',''),('Class 3',''),('Class 4',''),('Class 5',''),
('Class 6',''),('Class 7',''),('Class 8',''),
('Class 9',''),('Class 10',''),('Class 11',''),('Class 12','');

-- -----------------------------------------------------
-- Users  (role_id: 1 = student, 2 = teacher)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS userform (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(15) DEFAULT '',
  gender VARCHAR(10) DEFAULT '',
  dob DATE DEFAULT NULL,
  student_class INT DEFAULT NULL,
  photo VARCHAR(255) DEFAULT '',
  role VARCHAR(20) DEFAULT 'student',
  role_id INT DEFAULT 1,
  status VARCHAR(20) DEFAULT 'active',
  password VARCHAR(255) NOT NULL,
  admission_date DATE DEFAULT NULL,
  teacher_subject VARCHAR(100) DEFAULT NULL,
  hobbies VARCHAR(255) DEFAULT NULL,
  bio TEXT,
  CONSTRAINT fk_user_class FOREIGN KEY (student_class) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Admissions
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS admissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_id INT DEFAULT NULL,
  admission_date DATE DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'active',
  CONSTRAINT fk_admission_student FOREIGN KEY (student_id) REFERENCES userform(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Fees
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS fees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  total_fee DECIMAL(10,2) DEFAULT 0,
  paid_fee DECIMAL(10,2) DEFAULT 0,
  pending_fee DECIMAL(10,2) DEFAULT 0,
  status VARCHAR(20) DEFAULT 'pending',
  last_payment DATETIME DEFAULT NULL,
  CONSTRAINT fk_fee_student FOREIGN KEY (student_id) REFERENCES userform(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Payments
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  amount DECIMAL(10,2) DEFAULT 0,
  method VARCHAR(50) DEFAULT 'cash',
  note VARCHAR(255) DEFAULT '',
  created_by VARCHAR(100) DEFAULT NULL,
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payment_student FOREIGN KEY (student_id) REFERENCES userform(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Marks (one student per subject per exam)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS marks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_id INT DEFAULT NULL,
  subject VARCHAR(100) DEFAULT '',
  exam VARCHAR(50) DEFAULT '',
  marks INT DEFAULT 0,
  total_marks INT DEFAULT 100,
  teacher_id INT DEFAULT NULL,
  date DATE DEFAULT NULL,
  UNIQUE KEY uq_marks (student_id, subject, exam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Attendance (one row per student per day)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_id INT DEFAULT NULL,
  date DATE DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'Present',
  UNIQUE KEY uq_attendance (student_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- First admin: go to https://YOUR-SITE/admin/admin_create.php
-- the very first time — the app will walk you through
-- creating the admin account.
-- -----------------------------------------------------