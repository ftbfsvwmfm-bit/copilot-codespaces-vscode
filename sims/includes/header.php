<?php
// Check authentication
include_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user details
$user_id = $_SESSION['user_id'];
$query = "SELECT username, role FROM users WHERE user_id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$username = $user['username'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - SIMS' : 'SIMS'; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">SIMS</div>
            <nav>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                    <?php if ($role === 'student'): ?>
                        <li><a href="<?php echo SITE_URL; ?>pages/student_dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/view_courses.php">Courses</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/view_results.php">Results</a></li>
                    <?php elseif ($role === 'faculty'): ?>
                        <li><a href="<?php echo SITE_URL; ?>pages/faculty_dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/faculty_marks.php">Update Marks</a></li>
                        <li><a href="<?php echo SITE_URL; ?>pages/faculty_attendance.php">Attendance</a></li>
                    <?php elseif ($role === 'administrator'): ?>
                        <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo SITE_URL; ?>admin/manage_students.php">Students</a></li>
                        <li><a href="<?php echo SITE_URL; ?>admin/manage_faculty.php">Faculty</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="user-info">
                <span><?php echo htmlspecialchars($username); ?> (<?php echo ucfirst(str_replace('_', ' ', $role)); ?>)</span>
                <a href="<?php echo SITE_URL; ?>includes/auth.php?action=logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
