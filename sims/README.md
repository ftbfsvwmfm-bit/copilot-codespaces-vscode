# Student Information Management System (SIMS)

A comprehensive web-based Student Information Management System built with HTML, CSS, PHP, JavaScript, and MySQL. This system provides an easy interface for managing student information, academic records, faculty data, exam schedules, and placement information.

## Features

### Core Features
- **User Authentication**: Secure login and registration with role-based access control
- **Student Dashboard**: Personal dashboard with course details, marks, attendance, and results
- **Faculty Interface**: Mark entry, attendance management, student query handling
- **Admin Panel**: Complete management of students, faculty, courses, subjects, and exams
- **Exam Management**: Timetable creation, room allocation, result management
- **Placement Management**: Company information, recruitment drives, student eligibility tracking
- **Notices & Announcements**: College-wide communication system
- **Reports & Analytics**: Comprehensive reporting and data analysis

### User Roles
1. **Student**: View courses, marks, attendance, results, and placement information
2. **Faculty**: Manage marks, attendance, and handle student queries
3. **Exam Section**: Create exam schedules, allocate rooms, and publish results
4. **Placement Cell**: Manage company recruitment and placement tracking
5. **Administrator**: Complete system management and data control

## System Requirements

- **Web Server**: Apache/Nginx with PHP support
- **PHP Version**: 7.4 or higher
- **Database**: MySQL 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled (Chrome, Firefox, Safari, Edge)
- **RAM**: 512 MB minimum
- **Storage**: 500 MB minimum

## Installation Guide

### Step 1: Prerequisites Installation

#### On Linux/Ubuntu:
```bash
# Install Apache, PHP, and MySQL
sudo apt update
sudo apt install apache2 php php-mysql mysql-server
sudo service apache2 start
sudo service mysql start
```

#### On Windows:
- Download and install XAMPP from https://www.apachefriends.org/
- Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Download/Clone the Project

```bash
cd /var/www/html  # Linux
# or
cd C:\xampp\htdocs  # Windows

# Clone or extract the SIMS project
git clone <repository-url> sims
cd sims
```

### Step 3: Database Setup

#### Create the database:
```bash
mysql -u root -p
```

Then in MySQL shell:
```sql
-- Import the database schema
SOURCE /path/to/sims/db/sims.sql;
```

Or manually:
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `sims`
3. Import the file `db/sims.sql` into the `sims` database

### Step 4: Configure Database Connection

Edit `includes/config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'sims');
```

### Step 5: Set File Permissions (Linux)

```bash
chmod 755 /var/www/html/sims
chmod 644 /var/www/html/sims/*.php
chmod -R 755 /var/www/html/sims/assets
chmod -R 755 /var/www/html/sims/admin
chmod -R 755 /var/www/html/sims/pages
chmod -R 755 /var/www/html/sims/includes
```

### Step 6: Access the Application

Open your browser and navigate to:
- **Local Access**: http://localhost/sims/login.php
- **Remote Access**: http://your-server-ip/sims/login.php

## Default Test Credentials

### Administrator
- **Username**: admin
- **Password**: admin123

### Sample Student
- **Username**: student001
- **Password**: student123

### Sample Faculty
- **Username**: faculty001
- **Password**: faculty123

**Note**: Change credentials immediately after first login for security purposes.

## Project Structure

```
sims/
├── assets/
│   ├── css/
│   │   └── style.css              # Main stylesheet
│   └── js/
│       └── validation.js          # Client-side validations
├── db/
│   └── sims.sql                   # Database schema
├── admin/
│   ├── dashboard.php              # Admin dashboard
│   ├── manage_students.php        # Manage student records
│   ├── manage_faculty.php         # Manage faculty records
│   ├── manage_courses.php         # Manage courses
│   ├── manage_subjects.php        # Manage subjects
│   ├── manage_exams.php           # Manage exams
│   └── manage_placements.php      # Manage placements
├── pages/
│   ├── student_dashboard.php      # Student dashboard
│   ├── faculty_dashboard.php      # Faculty dashboard
│   ├── faculty_marks.php          # Mark entry by faculty
│   ├── faculty_attendance.php     # Attendance management
│   ├── view_results.php           # View exam results
│   ├── view_marks.php             # View internal marks
│   ├── view_attendance.php        # View attendance records
│   ├── view_placements.php        # View placement info
│   └── view_exams.php             # View exam schedules
├── includes/
│   ├── config.php                 # Database configuration
│   ├── auth.php                   # Authentication handler
│   ├── header.php                 # Page header template
│   └── footer.php                 # Page footer template
├── login.php                       # Login page
├── register.php                    # Registration page
├── index.php                       # Home page
└── README.md                       # This file
```

## Database Tables

### Core Tables
- **users**: User authentication and role management
- **students**: Student personal and academic information
- **faculty**: Faculty personal and professional details
- **courses**: Course information
- **subjects**: Subject/Course details

### Academic Tables
- **enrollments**: Student course enrollments
- **internal_marks**: Internal assessment marks
- **results**: Final exam results
- **attendance**: Student attendance records

### Exam Tables
- **exam_timetables**: Exam schedules
- **room_allocations**: Exam room assignments

### Placement Tables
- **companies**: Company information
- **placements**: Placement drives
- **placement_registrations**: Student placement registrations

### Communication Tables
- **notices**: College announcements
- **student_queries**: Student questions/doubts

## Key Features Usage

### For Students
1. **Login** with your username and password
2. **View Dashboard** to see overview of courses and performance
3. **Check Courses** to see enrolled subjects and course details
4. **View Marks** to see internal assessment marks
5. **Check Results** to see final exam results with grades
6. **View Attendance** to track attendance percentage
7. **Check Exams** to see exam schedule and room allocation
8. **View Placements** to see recruitment drives and apply

### For Faculty
1. **Login** with faculty credentials
2. **Access Dashboard** to see assigned subjects
3. **Update Marks** for students in your subjects
4. **Mark Attendance** for students in each class
5. **Answer Student Queries** related to your subject
6. **View Student Performance** to understand student progress

### For Administrators
1. **Login** with admin credentials
2. **Access Dashboard** to see system overview
3. **Manage Students** - Add, edit, delete student records
4. **Manage Faculty** - Add, edit, delete faculty records
5. **Manage Courses** - Create and manage courses
6. **Manage Subjects** - Assign subjects to faculty
7. **Manage Exams** - Create timetables and allocate rooms
8. **Manage Placements** - Add companies and drives
9. **Generate Reports** - Extract various system reports

## Security Features

- Password hashing using PHP's `password_hash()` function
- SQL injection prevention through prepared statements
- XSS protection through output escaping
- CSRF token implementation (can be enhanced)
- Role-based access control
- Session timeout management
- Input validation on both client and server side

## Common Issues & Troubleshooting

### Issue: "Connection failed: Connection refused"
**Solution**: 
- Check if MySQL server is running
- Verify database credentials in `config.php`
- Ensure database name is correct

### Issue: "404 Page not found"
**Solution**:
- Check if .htaccess is enabled (for Apache)
- Verify file paths are correct
- Clear browser cache

### Issue: Files are showing as plain text
**Solution**:
- Ensure PHP is installed and enabled
- Check file extensions are `.php`
- Restart web server

### Issue: Cannot upload files
**Solution**:
- Check directory permissions (755 or 775)
- Verify upload directory exists and is writable
- Check PHP upload_max_filesize setting

## Performance Optimization

1. **Database**:
   - Use indexes on frequently queried columns (already included)
   - Regular database optimization:
     ```sql
     OPTIMIZE TABLE users;
     OPTIMIZE TABLE students;
     ```

2. **PHP**:
   - Enable PHP caching (opcache)
   - Minimize database queries

3. **Browser**:
   - Enable browser caching in .htaccess
   - Minify CSS and JavaScript

## Backup & Maintenance

### Regular Database Backup
```bash
# Backup database
mysqldump -u root -p sims > sims_backup.sql

# Restore database
mysql -u root -p sims < sims_backup.sql
```

### Scheduled Maintenance
- Weekly: Review error logs
- Monthly: Backup database
- Quarterly: Update PHP/MySQL
- Annually: Security audit

## Future Enhancements

- [ ] Mobile application (iOS/Android)
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Document management system
- [ ] Advanced analytics and dashboards
- [ ] Multi-language support
- [ ] Two-factor authentication
- [ ] API for third-party integration
- [ ] Online examination module
- [ ] Fee management module

## Support & Contribution

For issues, suggestions, or contributions:
- Report bugs through the issue tracker
- Suggest features through discussions
- Submit pull requests for improvements

## License

This project is free and open-source software. You are free to use, modify, and distribute it as per your needs.

## Author & Acknowledgments

Developed based on the research paper:
"Web Based Student Information Management System" 
by S.R.Bharamagoudar, Geeta R.B., and S.G.Totad
Published in International Journal of Advanced Research in Computer and Communication Engineering, Volume 2, Issue 6, June 2013

## Contact & Support

- **Email**: support@sims-system.com
- **Website**: www.simsystem.edu
- **Documentation**: /docs
- **FAQ**: See README_FAQ.md

---

**Last Updated**: May 2024
**Version**: 1.0.0
**Status**: Production Ready

For the latest updates and documentation, visit the project repository.
