<?php
$page_title = 'Student Dashboard';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student details
$query = "SELECT s.*, u.email FROM students s 
          JOIN users u ON s.user_id = u.user_id 
          WHERE u.user_id = $_SESSION[user_id]";
$result = $conn->query($query);
$student = $result->fetch_assoc();

// Get enrolled subjects count
$subjects_query = "SELECT COUNT(*) as count FROM enrollments 
                   WHERE student_id = {$student['student_id']} AND status = 'active'";
$subjects_result = $conn->query($subjects_query);
$subjects_count = $subjects_result->fetch_assoc()['count'];

// Get attendance
$attendance_query = "SELECT COUNT(*) as present FROM attendance 
                    WHERE student_id = {$student['student_id']} AND status = 'present'";
$attendance_result = $conn->query($attendance_query);
$attendance_data = $attendance_result->fetch_assoc();

// Get recent results
$results_query = "SELECT r.*, sub.subject_name FROM results r 
                 JOIN subjects sub ON r.subject_id = sub.subject_id 
                 WHERE r.student_id = {$student['student_id']} 
                 LIMIT 5";
$results = $conn->query($results_query);
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="view_courses.php">My Courses</a></li>
            <li><a href="view_subjects.php">My Subjects</a></li>
            <li><a href="view_attendance.php">Attendance</a></li>
            <li><a href="view_marks.php">Internal Marks</a></li>
            <li><a href="view_results.php">Results</a></li>
            <li><a href="view_placements.php">Placements</a></li>
            <li><a href="view_exams.php">Exams</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Student Dashboard</h1>

        <div class="alert alert-info">
            <strong>Welcome!</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $subjects_count; ?></h3>
                <p>Active Courses</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <h3><?php echo $attendance_data['present']; ?></h3>
                <p>Days Present</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                <h3 id="gpaDisplay">-</h3>
                <p>CGPA</p>
            </div>
        </div>

        <div class="card card-success">
            <h3>Personal Information</h3>
            <table>
                <tr>
                    <td><strong>Roll Number:</strong></td>
                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                </tr>
                <tr>
                    <td><strong>University Number:</strong></td>
                    <td><?php echo htmlspecialchars($student['university_number'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Department:</strong></td>
                    <td><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Year:</strong></td>
                    <td><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>

        <div class="card card-primary">
            <h3>Recent Results</h3>
            <?php if ($results->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Internal</th>
                            <th>External</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['internal_marks']); ?></td>
                                <td><?php echo htmlspecialchars($row['external_marks']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_marks']); ?></td>
                                <td><?php echo htmlspecialchars($row['grade']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No results available yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
