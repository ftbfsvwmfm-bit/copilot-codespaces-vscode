<?php
$page_title = 'View Attendance';
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

// Get enrolled subjects
$subjects = $conn->query("SELECT DISTINCT s.subject_id, s.subject_code, s.subject_name FROM enrollments e
                         JOIN subjects s ON e.subject_id = s.subject_id
                         WHERE e.student_id = $student_id");

// Get attendance records
$attendance_query = "SELECT a.*, s.subject_name, s.subject_code FROM attendance a
                    JOIN subjects s ON a.subject_id = s.subject_id
                    WHERE a.student_id = $student_id";

if ($subject_id > 0) {
    $attendance_query .= " AND a.subject_id = $subject_id";
}

$attendance_query .= " ORDER BY a.attendance_date DESC";
$attendance = $conn->query($attendance_query);

// Calculate attendance percentage per subject
$attendance_stats = $conn->query("SELECT 
                                s.subject_id,
                                s.subject_code,
                                s.subject_name,
                                COUNT(*) as total_classes,
                                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                                ROUND(SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100 / COUNT(*), 2) as percentage
                                FROM enrollments e
                                JOIN subjects s ON e.subject_id = s.subject_id
                                LEFT JOIN attendance a ON e.student_id = a.student_id 
                                AND e.subject_id = a.subject_id
                                WHERE e.student_id = $student_id
                                GROUP BY s.subject_id");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_attendance.php" class="active">Attendance</a></li>
            <li><a href="view_marks.php">Internal Marks</a></li>
            <li><a href="view_results.php">Results</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Attendance Records</h1>

        <div class="card card-success">
            <h3>Attendance Summary by Subject</h3>
            <table>
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Total Classes</th>
                        <th>Present</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_classes = 0;
                    $total_present = 0;
                    while ($stat = $attendance_stats->fetch_assoc()): 
                        $total_classes += $stat['total_classes'];
                        $total_present += $stat['present_days'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($stat['subject_name']); ?></td>
                            <td><?php echo $stat['total_classes']; ?></td>
                            <td><?php echo $stat['present_days']; ?></td>
                            <td>
                                <strong style="color: <?php echo $stat['percentage'] >= 75 ? '#27ae60' : '#e74c3c'; ?>">
                                    <?php echo number_format($stat['percentage'], 2); ?>%
                                </strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="background-color: #ecf0f1; font-weight: bold;">
                        <td colspan="2">Overall</td>
                        <td><?php echo $total_classes; ?></td>
                        <td><?php echo $total_present; ?></td>
                        <td style="color: <?php echo ($total_classes > 0 && ($total_present * 100 / $total_classes) >= 75) ? '#27ae60' : '#e74c3c'; ?>">
                            <?php echo $total_classes > 0 ? number_format(($total_present * 100 / $total_classes), 2) : 0; ?>%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card card-primary">
            <h3>Filter Attendance Records</h3>
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <select name="subject_id" onchange="this.form.submit();">
                    <option value="">All Subjects</option>
                    <?php 
                    // Reset subjects query for dropdown
                    $subjects = $conn->query("SELECT DISTINCT s.subject_id, s.subject_code, s.subject_name FROM enrollments e
                                            JOIN subjects s ON e.subject_id = s.subject_id
                                            WHERE e.student_id = $student_id");
                    while ($subject = $subjects->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $subject['subject_id']; ?>" 
                                <?php echo $subject_id === $subject['subject_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                        </option>
                    <?php endwhile; ?>
                    </select>
                    <a href="view_attendance.php" class="btn btn-secondary" style="background-color: #95a5a6;">Clear</a>
                </form>
        </div>

        <div class="card">
            <h3>Attendance Details (Total Records: <?php echo $attendance->num_rows; ?>)</h3>
            <?php if ($attendance->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = $attendance->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['subject_code'] . ' - ' . $record['subject_name']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($record['attendance_date'])); ?></td>
                                <td>
                                    <?php 
                                    $status_color = [
                                        'present' => '#27ae60',
                                        'absent' => '#e74c3c',
                                        'leave' => '#f39c12'
                                    ];
                                    $color = $status_color[$record['status']] ?? '#95a5a6';
                                    ?>
                                    <span style="background-color: <?php echo $color; ?>; color: white; padding: 4px 8px; border-radius: 3px;">
                                        <?php echo ucfirst(htmlspecialchars($record['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No attendance records found.</p>
            <?php endif; ?>
        </div>

        <div class="alert alert-info" style="margin-top: 2rem;">
            <strong>Note:</strong> Attendance below 75% may affect your academic performance. 
            Please consult with your faculty if you have concerns.
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
