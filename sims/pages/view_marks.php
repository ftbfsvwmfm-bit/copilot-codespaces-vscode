<?php
$page_title = 'View Internal Marks';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student details
$student_query = "SELECT student_id FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get filters
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

// Get enrolled subjects
$subjects = $conn->query("SELECT DISTINCT s.subject_id, s.subject_code, s.subject_name FROM enrollments e
                         JOIN subjects s ON e.subject_id = s.subject_id
                         WHERE e.student_id = $student_id");

// Get internal marks
$marks_query = "SELECT im.*, s.subject_code, s.subject_name, f.first_name, f.last_name FROM internal_marks im
               JOIN subjects s ON im.subject_id = s.subject_id
               JOIN faculty f ON im.faculty_id = f.faculty_id
               WHERE im.student_id = $student_id";

if ($subject_id > 0) {
    $marks_query .= " AND im.subject_id = $subject_id";
}

if ($semester > 0) {
    $marks_query .= " AND im.semester = $semester";
}

$marks_query .= " ORDER BY im.semester DESC, s.subject_name";
$marks = $conn->query($marks_query);

// Calculate average internal marks
$avg_query = "SELECT AVG(COALESCE(test1, 0) + COALESCE(test2, 0) + COALESCE(assignment, 0)) / 3 as avg_marks 
             FROM internal_marks WHERE student_id = $student_id";
$avg_result = $conn->query($avg_query);
$avg_marks = $avg_result->fetch_assoc()['avg_marks'];
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_marks.php" class="active">Internal Marks</a></li>
            <li><a href="view_attendance.php">Attendance</a></li>
            <li><a href="view_results.php">Results</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Internal Assessment Marks</h1>

        <div class="card card-primary">
            <h3>Average Internal Marks: <span style="color: #e74c3c;"><?php echo number_format($avg_marks ?? 0, 2); ?>/100</span></h3>
        </div>

        <div class="card">
            <h3>Filter Marks</h3>
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <select name="subject_id" onchange="this.form.submit();">
                    <option value="">All Subjects</option>
                    <?php 
                    $subjects = $conn->query("SELECT DISTINCT s.subject_id, s.subject_code, s.subject_name FROM enrollments e
                                            JOIN subjects s ON e.subject_id = s.subject_id
                                            WHERE e.student_id = $student_id");
                    while ($subject = $subjects->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $subject['subject_id']; ?>" 
                                <?php echo $subject_id === $subject['subject_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_code']); ?>
                        </option>
                    <?php endwhile; ?>
                    </select>

                    <select name="semester" onchange="this.form.submit();">
                        <option value="">All Semesters</option>
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $semester === $i ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <a href="view_marks.php" class="btn btn-secondary" style="background-color: #95a5a6;">Clear</a>
                </form>
        </div>

        <div class="card card-success">
            <h3>Marks Details (Total Records: <?php echo $marks->num_rows; ?>)</h3>
            <?php if ($marks->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Faculty</th>
                            <th>Test 1</th>
                            <th>Test 2</th>
                            <th>Assignment</th>
                            <th>Average</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($mark = $marks->fetch_assoc()): 
                            $average = (($mark['test1'] ?? 0) + ($mark['test2'] ?? 0) + ($mark['assignment'] ?? 0)) / 3;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mark['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($mark['first_name'] . ' ' . $mark['last_name']); ?></td>
                                <td><?php echo $mark['test1'] !== null ? number_format($mark['test1'], 2) : 'N/A'; ?></td>
                                <td><?php echo $mark['test2'] !== null ? number_format($mark['test2'], 2) : 'N/A'; ?></td>
                                <td><?php echo $mark['assignment'] !== null ? number_format($mark['assignment'], 2) : 'N/A'; ?></td>
                                <td><strong><?php echo number_format($average, 2); ?></strong></td>
                                <td><?php echo htmlspecialchars($mark['semester']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No internal marks available yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
