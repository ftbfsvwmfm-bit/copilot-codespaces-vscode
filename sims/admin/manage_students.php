<?php
$page_title = 'Manage Students';
include '../includes/header.php';

if (!hasRole('administrator')) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $student_id = (int)$_GET['id'];
    
    // Get user_id
    $user_result = $conn->query("SELECT user_id FROM students WHERE student_id = $student_id");
    $user_row = $user_result->fetch_assoc();
    
    if ($user_row) {
        // Delete user (which cascades to delete student)
        $conn->query("DELETE FROM users WHERE user_id = {$user_row['user_id']}");
        $_SESSION['message'] = 'Student deleted successfully';
        $_SESSION['message_type'] = 'success';
    }
    
    header('Location: manage_students.php');
    exit();
}

// Search/Filter
$search = isset($_GET['search']) ? escapeInput($_GET['search']) : '';
$filter_dept = isset($_GET['dept']) ? escapeInput($_GET['dept']) : '';
$filter_year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

// Build query
$query = "SELECT s.*, u.username, u.email, u.created_at FROM students s 
          JOIN users u ON s.user_id = u.user_id WHERE 1=1";

if ($search) {
    $query .= " AND (s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' 
               OR s.roll_number LIKE '%$search%' OR u.email LIKE '%$search%')";
}

if ($filter_dept) {
    $query .= " AND s.department = '$filter_dept'";
}

if ($filter_year) {
    $query .= " AND s.year = $filter_year";
}

$query .= " ORDER BY s.first_name";
$students = $conn->query($query);

// Get departments for filter
$departments = $conn->query("SELECT DISTINCT department FROM students WHERE department IS NOT NULL");
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_students.php" class="active">Manage Students</a></li>
            <li><a href="manage_faculty.php">Manage Faculty</a></li>
            <li><a href="manage_courses.php">Manage Courses</a></li>
            <li><a href="manage_subjects.php">Manage Subjects</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Manage Students</h1>

        <?php
        if (isset($_SESSION['message'])):
        ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php
        endif;
        ?>

        <div class="card card-primary">
            <h3>Search & Filter</h3>
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <input type="text" name="search" placeholder="Search by name, roll number, or email" value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="dept">
                    <option value="">All Departments</option>
                    <?php while ($dept = $departments->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($dept['department']); ?>" <?php echo $filter_dept === $dept['department'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['department']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="year">
                    <option value="">All Years</option>
                    <option value="1" <?php echo $filter_year === 1 ? 'selected' : ''; ?>>Year 1</option>
                    <option value="2" <?php echo $filter_year === 2 ? 'selected' : ''; ?>>Year 2</option>
                    <option value="3" <?php echo $filter_year === 3 ? 'selected' : ''; ?>>Year 3</option>
                    <option value="4" <?php echo $filter_year === 4 ? 'selected' : ''; ?>>Year 4</option>
                </select>
                
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="manage_students.php" class="btn btn-secondary" style="background-color: #95a5a6;">Clear</a>
            </form>
        </div>

        <div class="btn-group mb-2">
            <a href="add_student.php" class="btn btn-success">Add New Student</a>
        </div>

        <div class="card card-success">
            <h3>Students List (Total: <?php echo $students->num_rows; ?>)</h3>
            <?php if ($students->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Roll Number</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo date('y-m-d', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="manage_students.php?action=delete&id=<?php echo $student['student_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No students found.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
