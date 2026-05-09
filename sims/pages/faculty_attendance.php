<?php
$page_title = 'Faculty Attendance';
include '../includes/header.php';

if (!hasRole('faculty')) {
    redirect('login.php');
}

// Get faculty details
$faculty_query = "SELECT faculty_id FROM faculty WHERE user_id = $_SESSION[user_id]";
$faculty_result = $conn->query($faculty_query);
$faculty = $faculty_result->fetch_assoc();
$faculty_id = $faculty['faculty_id'];

// Get subjects handled
$subjects = $conn->query("SELECT * FROM subjects WHERE faculty_id = $faculty_id");

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = (int)$_POST['subject_id'];
    $attendance_date = escapeInput($_POST['attendance_date']);
    
    if (isset($_POST['attendance'])) {
        foreach ($_POST['attendance'] as $student_id => $status) {
            if ($status) {
                $student_id = (int)$student_id;
                
                // Check if already marked
                $check = $conn->query("SELECT attendance_id FROM attendance 
                                     WHERE student_id = $student_id 
                                     AND subject_id = $subject_id 
                                     AND faculty_id = $faculty_id
                                     AND attendance_date = '$attendance_date'");
                
                if ($check->num_rows > 0) {
                    // Update
                    $conn->query("UPDATE attendance SET status = '$status' 
                                WHERE student_id = $student_id 
                                AND subject_id = $subject_id 
                                AND faculty_id = $faculty_id
                                AND attendance_date = '$attendance_date'");
                } else {
                    // Insert
                    $conn->query("INSERT INTO attendance (student_id, subject_id, faculty_id, attendance_date, status)
                                VALUES ($student_id, $subject_id, $faculty_id, '$attendance_date', '$status')");
                }
            }
        }
        $_SESSION['message'] = 'Attendance marked successfully';
        $_SESSION['message_type'] = 'success';
    }
}

// Load students for selected subject
$selected_subject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$selected_date = isset($_GET['date']) ? escapeInput($_GET['date']) : date('Y-m-d');
$students_list = [];

if ($selected_subject > 0) {
    $students_list = $conn->query("SELECT DISTINCT e.student_id, s.first_name, s.last_name, s.roll_number, a.status
                                  FROM enrollments e
                                  JOIN students s ON e.student_id = s.student_id
                                  LEFT JOIN attendance a ON e.student_id = a.student_id 
                                  AND e.subject_id = a.subject_id
                                  AND a.faculty_id = $faculty_id
                                  AND a.attendance_date = '$selected_date'
                                  WHERE e.subject_id = $selected_subject");
}
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Faculty Menu</h3>
        <ul>
            <li><a href="faculty_dashboard.php">Dashboard</a></li>
            <li><a href="faculty_marks.php">Update Marks</a></li>
            <li><a href="faculty_attendance.php" class="active">Attendance</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Mark Student Attendance</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <div class="card card-primary">
            <h3>Select Subject and Date</h3>
            <form method="GET">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="subject_id">Subject *</label>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">-- Select Subject --</option>
                            <?php 
                            // Reload subjects for dropdown
                            $subjects = $conn->query("SELECT * FROM subjects WHERE faculty_id = $faculty_id");
                            while ($subject = $subjects->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $subject['subject_id']; ?>" 
                                        <?php echo $selected_subject === $subject['subject_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </option>
                            <?php endwhile; ?>
                            </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="date">Attendance Date *</label>
                        <input type="date" id="date" name="date" value="<?php echo $selected_date; ?>" required>
                    </div>
                    <div style="align-self: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Load Students</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($selected_subject > 0 && $students_list->num_rows > 0): ?>
            <div class="card card-success">
                <h3>Mark Attendance for <?php echo htmlspecialchars($selected_date); ?></h3>
                <form method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo $selected_subject; ?>">
                    <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $students_list->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td>
                                        <select name="attendance[<?php echo $student['student_id']; ?>]" style="width: 150px;">
                                            <option value="">-- Not Marked --</option>
                                            <option value="present" <?php echo $student['status'] === 'present' ? 'selected' : ''; ?>>Present</option>
                                            <option value="absent" <?php echo $student['status'] === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                            <option value="leave" <?php echo $student['status'] === 'leave' ? 'selected' : ''; ?>>Leave</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <div class="btn-group mt-2">
                        <button type="submit" class="btn btn-success">Save Attendance</button>
                    </div>
                </form>
            </div>
        <?php elseif ($selected_subject > 0): ?>
            <div class="alert alert-info">No students enrolled in this subject for the selected date.</div>
        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
