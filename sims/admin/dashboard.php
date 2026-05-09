<?php
$page_title = 'Admin Dashboard';
include '../includes/header.php';

if (!hasRole('administrator')) {
    redirect('login.php');
}

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_faculty = $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$total_subjects = $conn->query("SELECT COUNT(*) as count FROM subjects")->fetch_assoc()['count'];

// Get recent registrations
$recent_students = $conn->query("SELECT s.*, u.created_at FROM students s 
                                JOIN users u ON s.user_id = u.user_id 
                                ORDER BY u.created_at DESC LIMIT 5");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_faculty.php">Manage Faculty</a></li>
            <li><a href="manage_courses.php">Manage Courses</a></li>
            <li><a href="manage_subjects.php">Manage Subjects</a></li>
            <li><a href="manage_exams.php">Manage Exams</a></li>
            <li><a href="manage_placements.php">Manage Placements</a></li>
            <li><a href="view_notices.php">Notices</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Administrator Dashboard</h1>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $total_students; ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <h3><?php echo $total_faculty; ?></h3>
                <p>Total Faculty</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                <h3><?php echo $total_courses; ?></h3>
                <p>Total Courses</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <h3><?php echo $total_subjects; ?></h3>
                <p>Total Subjects</p>
            </div>
        </div>

        <div class="card card-primary">
            <h3>Recent Student Registrations</h3>
            <?php if ($recent_students->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Roll Number</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $recent_students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></td>
                                <td><?php echo date('y-m-d H:i', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-primary">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No students registered yet.</p>
            <?php endif; ?>
        </div>

        <div class="card card-success">
            <h3>Quick Actions</h3>
            <div class="btn-group">
                <a href="manage_students.php" class="btn btn-primary">Add New Student</a>
                <a href="manage_faculty.php" class="btn btn-success">Add New Faculty</a>
                <a href="manage_courses.php" class="btn btn-warning">Add New Course</a>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
