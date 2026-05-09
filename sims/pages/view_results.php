<?php
$page_title = 'View Results';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student details
$student_query = "SELECT student_id FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get filter options
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

// Build query
$query = "SELECT r.*, sub.subject_name, sub.subject_code FROM results r 
          JOIN subjects sub ON r.subject_id = sub.subject_id 
          WHERE r.student_id = $student_id";

if ($semester > 0) {
    $query .= " AND r.semester = $semester";
}
if ($year > 0) {
    $query .= " AND r.year = $year";
}

$query .= " ORDER BY r.year DESC, r.semester DESC, sub.subject_name";
$results = $conn->query($query);

// Calculate CGPA
$gpa_query = "SELECT AVG(
    CASE 
        WHEN total_marks >= 90 THEN 4.0
        WHEN total_marks >= 80 THEN 3.5
        WHEN total_marks >= 70 THEN 3.0
        WHEN total_marks >= 60 THEN 2.5
        WHEN total_marks >= 50 THEN 2.0
        ELSE 0.0
    END
) as cgpa FROM results WHERE student_id = $student_id";
$cgpa_result = $conn->query($gpa_query);
$cgpa = $cgpa_result->fetch_assoc()['cgpa'];
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_results.php" class="active">Results</a></li>
            <li><a href="view_marks.php">Internal Marks</a></li>
            <li><a href="view_attendance.php">Attendance</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Exam Results</h1>

        <div class="card card-primary">
            <h3>CGPA: <span style="color: #3498db; font-size: 1.5em;"><?php echo number_format($cgpa ?? 0, 2); ?></span></h3>
        </div>

        <div class="card">
            <h3>Filter Results</h3>
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <select name="semester">
                    <option value="">All Semesters</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $semester === $i ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                
                <select name="year">
                    <option value="">All Years</option>
                    <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $year === $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_results.php" class="btn btn-secondary" style="background-color: #95a5a6;">Clear</a>
            </form>
        </div>

        <div class="card card-success">
            <h3>Results (Total: <?php echo $results->num_rows; ?>)</h3>
            <?php if ($results->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Internal</th>
                            <th>External</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Semester</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($result = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($result['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['internal_marks']); ?></td>
                                <td><?php echo htmlspecialchars($result['external_marks']); ?></td>
                                <td><strong><?php echo htmlspecialchars($result['total_marks']); ?></strong></td>
                                <td>
                                    <span style="background-color: #3498db; color: white; padding: 2px 6px; border-radius: 3px;">
                                        <?php echo htmlspecialchars($result['grade']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($result['semester']); ?></td>
                                <td><?php echo htmlspecialchars($result['year']); ?></td>
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
