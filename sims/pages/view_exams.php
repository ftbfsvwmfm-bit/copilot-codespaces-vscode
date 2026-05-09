<?php
$page_title = 'View Exams';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student
$student_query = "SELECT student_id FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get exam timetables for student's subjects
$exams = $conn->query("SELECT et.*, s.subject_code, s.subject_name, ra.room_number, ra.seat_number
                      FROM exam_timetables et
                      JOIN subjects s ON et.subject_id = s.subject_id
                      LEFT JOIN room_allocations ra ON et.timetable_id = ra.timetable_id 
                      AND ra.student_id = $student_id
                      WHERE s.subject_id IN (
                        SELECT subject_id FROM enrollments WHERE student_id = $student_id
                      )
                      ORDER BY et.exam_date, et.start_time");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_exams.php" class="active">Exams</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Exam Schedule</h1>

        <div class="card card-success">
            <h3>Your Exam Schedule (Total: <?php echo $exams->num_rows; ?>)</h3>
            <?php if ($exams->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Exam Type</th>
                            <th>Exam Date</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Seat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($exam = $exams->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($exam['subject_name']); ?></td>
                                <td>
                                    <span style="background-color: <?php echo $exam['exam_type'] === 'internal' ? '#3498db' : '#e74c3c'; ?>; 
                                           color: white; padding: 2px 6px; border-radius: 3px;">
                                        <?php echo ucfirst(htmlspecialchars($exam['exam_type'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($exam['exam_date'])); ?></td>
                                <td><?php echo substr($exam['start_time'], 0, 5) . ' - ' . substr($exam['end_time'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($exam['room_number'] ?? 'TBA'); ?></td>
                                <td><?php echo htmlspecialchars($exam['seat_number'] ?? 'TBA'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No exam schedules available yet.</p>
            <?php endif; ?>
        </div>

        <div class="alert alert-info" style="margin-top: 2rem;">
            <strong>Important:</strong> Please note your exam dates, times, room and seat numbers. 
            Report to exam center 15 minutes before the scheduled time.
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
