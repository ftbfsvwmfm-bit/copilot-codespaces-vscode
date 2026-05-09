<?php
$page_title = 'Update Marks';
include '../includes/header.php';

if (!hasRole('faculty')) {
    redirect('login.php');
}

// Get faculty details
$faculty_query = "SELECT faculty_id FROM faculty WHERE user_id = $_SESSION[user_id]";
$faculty_result = $conn->query($faculty_query);
$faculty = $faculty_result->fetch_assoc();
$faculty_id = $faculty['faculty_id'];

// Get subjects handled by this faculty
$subjects = $conn->query("SELECT * FROM subjects WHERE faculty_id = $faculty_id");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $subject_id = (int)$_POST['subject_id'];
    $test1 = $_POST['test1'] ?? null;
    $test2 = $_POST['test2'] ?? null;
    $assignment = $_POST['assignment'] ?? null;
    
    // Validate marks
    $errors = [];
    if ($test1 !== '' && ($test1 < 0 || $test1 > 100)) {
        $errors[] = 'Test 1 marks must be between 0 and 100';
    }
    if ($test2 !== '' && ($test2 < 0 || $test2 > 100)) {
        $errors[] = 'Test 2 marks must be between 0 and 100';
    }
    if ($assignment !== '' && ($assignment < 0 || $assignment > 100)) {
        $errors[] = 'Assignment marks must be between 0 and 100';
    }
    
    if (empty($errors)) {
        // Check if record exists
        $check = $conn->query("SELECT marks_id FROM internal_marks 
                             WHERE student_id = $student_id AND subject_id = $subject_id 
                             AND faculty_id = $faculty_id");
        
        if ($check->num_rows > 0) {
            // Update
            $marks_id = $check->fetch_assoc()['marks_id'];
            $update = "UPDATE internal_marks SET test1 = " . ($test1 !== '' ? $test1 : 'NULL') . ", 
                     test2 = " . ($test2 !== '' ? $test2 : 'NULL') . ",
                     assignment = " . ($assignment !== '' ? $assignment : 'NULL') . "
                     WHERE marks_id = $marks_id";
            $conn->query($update);
        } else {
            // Insert new
            $insert = "INSERT INTO internal_marks (student_id, subject_id, faculty_id, test1, test2, assignment, semester, year)
                      VALUES ($student_id, $subject_id, $faculty_id, " . 
                      ($test1 !== '' ? $test1 : 'NULL') . ", " .
                      ($test2 !== '' ? $test2 : 'NULL') . ", " .
                      ($assignment !== '' ? $assignment : 'NULL') . ", 1, 2024)";
            $conn->query($insert);
        }
        
        $_SESSION['message'] = 'Marks updated successfully';
        $_SESSION['message_type'] = 'success';
    }
}

// Load students for selected subject
$selected_subject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$students_list = [];

if ($selected_subject > 0) {
    $students_list = $conn->query("SELECT DISTINCT e.student_id, s.first_name, s.last_name, s.roll_number,
                                  im.test1, im.test2, im.assignment
                                  FROM enrollments e
                                  JOIN students s ON e.student_id = s.student_id
                                  LEFT JOIN internal_marks im ON e.student_id = im.student_id 
                                  AND e.subject_id = im.subject_id
                                  WHERE e.subject_id = $selected_subject");
}
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Faculty Menu</h3>
        <ul>
            <li><a href="faculty_dashboard.php">Dashboard</a></li>
            <li><a href="faculty_marks.php" class="active">Update Marks</a></li>
            <li><a href="faculty_attendance.php">Attendance</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Update Student Marks</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <div class="card card-primary">
            <h3>Select Subject</h3>
            <form method="GET">
                <div class="form-group">
                    <label for="subject_id">Subject *</label>
                    <select id="subject_id" name="subject_id" onchange="this.form.submit();" required>
                        <option value="">-- Select Subject --</option>
                        <?php 
                        // Reload subjects
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
            </form>
        </div>

        <?php if ($selected_subject > 0 && $students_list->num_rows > 0): ?>
            <div class="card card-success">
                <h3>Enter Marks for Students</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Test 1 (out of 100)</th>
                            <th>Test 2 (out of 100)</th>
                            <th>Assignment (out of 100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students_list->fetch_assoc()): ?>
                            <tr>
                                <form method="POST" style="contents;">
                                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                    <input type="hidden" name="subject_id" value="<?php echo $selected_subject; ?>">
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td>
                                        <input type="number" name="test1" min="0" max="100" step="0.01" 
                                               value="<?php echo $student['test1'] ?? ''; ?>" style="width: 100px;">
                                    </td>
                                    <td>
                                        <input type="number" name="test2" min="0" max="100" step="0.01" 
                                               value="<?php echo $student['test2'] ?? ''; ?>" style="width: 100px;">
                                    </td>
                                    <td>
                                        <input type="number" name="assignment" min="0" max="100" step="0.01" 
                                               value="<?php echo $student['assignment'] ?? ''; ?>" style="width: 100px;">
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="btn-group mt-2">
                    <button type="submit" class="btn btn-success" onclick="saveMarks()">Save All Marks</button>
                </div>
            </div>
        <?php elseif ($selected_subject > 0): ?>
            <div class="alert alert-info">No students enrolled in this subject.</div>
        <?php endif; ?>
    </main>
</div>

<script>
function saveMarks() {
    const form = document.querySelector('form');
    showLoadingSpinner(true);
    form.submit();
}
</script>

<?php include '../includes/footer.php'; ?>
