<?php
$page_title = 'View Courses';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student
$student_query = "SELECT student_id FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get enrolled courses
$courses = $conn->query("SELECT DISTINCT c.*, COUNT(DISTINCT s.subject_id) as subject_count 
                        FROM enrollments e
                        JOIN subjects s ON e.subject_id = s.subject_id
                        JOIN courses c ON s.course_id = c.course_id
                        WHERE e.student_id = $student_id AND e.status = 'active'
                        GROUP BY c.course_id");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_courses.php" class="active">My Courses</a></li>
            <li><a href="view_subjects.php">My Subjects</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">My Courses</h1>

        <div class="card card-success">
            <h3>Enrolled Courses (Total: <?php echo $courses->num_rows; ?>)</h3>
            <?php if ($courses->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Credits</th>
                            <th>Subjects</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = $courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['department']); ?></td>
                                <td><?php echo htmlspecialchars($course['credits']); ?></td>
                                <td><?php echo htmlspecialchars($course['subject_count']); ?></td>
                                <td>
                                    <a href="view_subjects.php" class="btn btn-primary">View Subjects</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You are not enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>

        <div class="card card-info" style="border-left-color: #3498db;">
            <h3>Course Information</h3>
            <p>Click on "View Subjects" to see the subjects offered in each course, including:</p>
            <ul style="margin-left: 2rem;">
                <li>Subject details and syllabus</li>
                <li>Faculty handling the subject</li>
                <li>Your marks and attendance</li>
                <li>Exam schedules</li>
            </ul>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
