-- LAB 01: Student Registration Database

-- Create the database
CREATE DATABASE IF NOT EXISTS lab01_student_registration;

-- Select the database
USE lab01_student_registration;

-- Create the students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(80) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telephone CHAR(9) NOT NULL,
    date_of_birth DATE NOT NULL,
    programme VARCHAR(100) NOT NULL,
    level VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);