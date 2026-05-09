 <?php
include 'includes/config.php';

// If logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    switch ($role) {
        case 'administrator':
            redirect('admin/dashboard.php');
            break;
        case 'faculty':
            redirect('pages/faculty_dashboard.php');
            break;
        case 'exam_section':
            redirect('pages/exam_dashboard.php');
            break;
        case 'placement_cell':
            redirect('pages/placement_dashboard.php');
            break;
        case 'student':
        default:
            redirect('pages/student_dashboard.php');
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMS - Student Information Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero .btn {
            margin: 0 0.5rem;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .feature-card h3 {
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">SIMS - Student Information Management System</div>
            <nav>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="hero">
        <h1>Welcome to SIMS</h1>
        <p>A comprehensive Student Information Management System for educational institutions</p>
        <div>
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-success">Register</a>
        </div>
    </div>

    <div class="features">
        <div class="feature-card">
            <div class="feature-icon">👨‍🎓</div>
            <h3>For Students</h3>
            <p>Access your course details, view marks, check attendance, and track your academic progress.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">👨‍🏫</div>
            <h3>For Faculty</h3>
            <p>Manage student marks, attendance, and communicate with students about their performance.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>For Administration</h3>
            <p>Complete management of students, faculty, courses, exams, and institutional operations.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🏛️</div>
            <h3>Exam Management</h3>
            <p>Create exam schedules, allocate rooms, and publish results efficiently.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💼</div>
            <h3>Placement Management</h3>
            <p>Track recruitment drives and manage placement activities for students.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Reports & Analytics</h3>
            <p>Generate comprehensive reports and track institutional performance metrics.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 SIMS - Student Information Management System. All rights reserved.</p>
    </footer>
</body>
</html>
