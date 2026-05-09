<?php
$page_title = 'Faculty Dashboard';
include '../includes/header.php';

if (!hasRole('faculty')) {
    redirect('login.php');
}

// Get faculty details
$query = "SELECT f.*, u.email FROM faculty f 
          JOIN users u ON f.user_id = u.user_id 
          WHERE u.user_id = $_SESSION[user_id]";
$result = $conn->query($query);
$faculty = $result->fetch_assoc();

// Get subjects handled
$subjects_query = "SELECT COUNT(*) as count FROM subjects 
                  WHERE faculty_id = {$faculty['faculty_id']}";
$subjects_result = $conn->query($subjects_query);
$subjects_count = $subjects_result->fetch_assoc()['count'];

// Get total students
$students_query = "SELECT COUNT(DISTINCT e.student_id) as count FROM enrollments e 
                  JOIN subjects s ON e.subject_id = s.subject_id 
                  WHERE s.faculty_id = {$faculty['faculty_id']}";
$students_result = $conn->query($students_query);
$students_count = $students_result->fetch_assoc()['count'];

// Get subjects handled by faculty
$my_subjects = $conn->query("SELECT * FROM subjects WHERE faculty_id = {$faculty['faculty_id']}");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Faculty Menu</h3>
        <ul>
            <li><a href="faculty_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="faculty_marks.php">Update Marks</a></li>
            <li><a href="faculty_attendance.php">Attendance</a></li>
            <li><a href="view_students.php">My Students</a></li>
            <li><a href="student_queries.php">Student Queries</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Faculty Dashboard</h1>

        <div class="alert alert-info">
            <strong>Welcome!</strong> <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $subjects_count; ?></h3>
                <p>Subjects Handling</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <h3><?php echo $students_count; ?></h3>
                <p>Students Teaching</p>
            </div>
        </div>

        <div class="card card-primary">
            <h3>My Subjects</h3>
            <?php if ($my_subjects->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Semester</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $my_subjects->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['semester']); ?></td>
                                <td><?php echo htmlspecialchars($subject['credits']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="faculty_marks.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-primary">Enter Marks</a>
                                        <a href="faculty_attendance.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-warning">Attendance</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No subjects assigned yet.</p>
            <?php endif; ?>
        </div>

        <div class="card card-success">
            <h3>Faculty Information</h3>
            <table>
                <tr>
                    <td><strong>Department:</strong></td>
                    <td><?php echo htmlspecialchars($faculty['department']); ?></td>
                </tr>
                <tr>
                    <td><strong>Specialization:</strong></td>
                    <td><?php echo htmlspecialchars($faculty['specialization'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>E-mail:</strong></td>
                    <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td><?php echo htmlspecialchars($faculty['phone'] ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
