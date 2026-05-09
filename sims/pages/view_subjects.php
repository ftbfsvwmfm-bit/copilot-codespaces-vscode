<?php
$page_title = 'View Subjects';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student
$student_query = "SELECT student_id FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get enrolled subjects with faculty details
$subjects = $conn->query("SELECT s.*, f.first_name, f.last_name, c.course_name,
                         (SELECT AVG((COALESCE(test1,0) + COALESCE(test2,0) + COALESCE(assignment,0))/3)
                          FROM internal_marks im 
                          WHERE im.student_id = $student_id AND im.subject_id = s.subject_id) as avg_marks
                         FROM enrollments e
                         JOIN subjects s ON e.subject_id = s.subject_id
                         JOIN courses c ON s.course_id = c.course_id
                         LEFT JOIN faculty f ON s.faculty_id = f.faculty_id
                         WHERE e.student_id = $student_id AND e.status = 'active'
                         ORDER BY s.semester, s.subject_name");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_courses.php">My Courses</a></li>
            <li><a href="view_subjects.php" class="active">My Subjects</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">My Subjects</h1>

        <div class="card card-success">
            <h3>Enrolled Subjects (Total: <?php echo $subjects->num_rows; ?>)</h3>
            <?php if ($subjects->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Course</th>
                            <th>Faculty</th>
                            <th>Semester</th>
                            <th>Credits</th>
                            <th>Avg Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $subjects->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['first_name'] . ' ' . $subject['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['semester']); ?></td>
                                <td><?php echo htmlspecialchars($subject['credits']); ?></td>
                                <td>
                                    <?php 
                                    $avg = $subject['avg_marks'];
                                    $color = $avg >= 75 ? '#27ae60' : ($avg >= 60 ? '#f39c12' : '#e74c3c');
                                    ?>
                                    <span style="color: <?php echo $color; ?>; font-weight: bold;">
                                        <?php echo $avg ? number_format($avg, 2) : 'N/A'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You are not enrolled in any subjects yet.</p>
            <?php endif; ?>
        </div>

        <div class="card card-info" style="border-left-color: #3498db;">
            <h3>Subject Information</h3>
            <p>For each subject, you can:</p>
            <ul style="margin-left: 2rem;">
                <li>View complete syllabus and course outline</li>
                <li>Access faculty details and office hours</li>
                <li>Check your internal marks</li>
                <li>View attendance percentage</li>
                <li>See exam schedules</li>
                <li>Submit assignments or queries</li>
            </ul>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
