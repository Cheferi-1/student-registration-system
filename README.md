# LAB 01 - Student Registration

## Description
A student registration system developed using HTML, CSS, PHP and MySQL.

## Requirements
- XAMPP
- PHP
- MySQL/MariaDB
- Web browser

## Installation
1. Copy the project folder into C:\xampp\htdocs.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Import database.sql.
5. Visit http://localhost/LAB01_Student_Registration/.

## Database
Database: lab01_student_registration
Table: students
MySQL port: 3307

## Validation rules
- Name: 3–80 characters.
- Email: valid and unique.
- Telephone: 9 digits beginning with 6.
- Date of birth: valid past date.
- Programme: required.
- Level: required.
- Gender: required.
- Policy acceptance: required.

## Security
- POST method.
- Server-side validation.
- PDO prepared statements.
- Unique email constraint.
- HTML output escaping.

## Author
Software Engineer
name:Doh Finley
level:2.