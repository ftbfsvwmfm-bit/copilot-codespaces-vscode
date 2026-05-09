<?php
$page_title = 'View Placements';
include '../includes/header.php';

if (!hasRole('student')) {
    redirect('login.php');
}

// Get student details
$student_query = "SELECT student_id, year, department FROM students WHERE user_id = $_SESSION[user_id]";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
$student_id = $student['student_id'];

// Get available placements
$placements = $conn->query("SELECT p.*, c.company_name, c.address, c.recruiter_name, 
                           (SELECT COUNT(*) FROM placement_registrations pr 
                            WHERE pr.placement_id = p.placement_id 
                            AND pr.student_id = $student_id) as is_registered
                           FROM placements p
                           JOIN companies c ON p.company_id = c.company_id
                           WHERE p.arrival_date >= CURDATE()
                           ORDER BY p.arrival_date");

// Handle registration
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'register') {
    $placement_id = (int)$_POST['placement_id'];
    
    // Check if already registered
    $check = $conn->query("SELECT registration_id FROM placement_registrations 
                         WHERE placement_id = $placement_id AND student_id = $student_id");
    
    if ($check->num_rows === 0) {
        $insert = "INSERT INTO placement_registrations (placement_id, student_id, registered_date, status)
                  VALUES ($placement_id, $student_id, CURDATE(), 'applied')";
        
        if ($conn->query($insert) === TRUE) {
            $_SESSION['message'] = 'Registered successfully for placement drive';
            $_SESSION['message_type'] = 'success';
        }
    }
}
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Student Menu</h3>
        <ul>
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="view_placements.php" class="active">Placements</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Placement Opportunities</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Available Placements</h3>
            <p><strong>Your Details:</strong> Year: <?php echo $student['year']; ?> | Department: <?php echo htmlspecialchars($student['department']); ?></p>
        </div>

        <?php if ($placements->num_rows > 0): ?>
            <?php while ($placement = $placements->fetch_assoc()): ?>
                <div class="card card-primary">
                    <h3><?php echo htmlspecialchars($placement['company_name']); ?></h3>
                    <table>
                        <tr>
                            <td><strong>Job Title:</strong></td>
                            <td><?php echo htmlspecialchars($placement['job_title']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Salary Package:</strong></td>
                            <td><?php echo htmlspecialchars('₹ ' . number_format($placement['salary'], 2)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Available Positions:</strong></td>
                            <td><?php echo htmlspecialchars($placement['positions']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Arrival Date:</strong></td>
                            <td><?php echo date('d-m-Y', strtotime($placement['arrival_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Interview Date:</strong></td>
                            <td><?php echo date('d-m-Y', strtotime($placement['interview_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Eligibility Criteria:</strong></td>
                            <td><?php echo nl2br(htmlspecialchars($placement['eligibility_criteria'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td><?php echo htmlspecialchars($placement['address']); ?></td>
                        </tr>
                    </table>

                    <?php if (!$placement['is_registered']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="register">
                            <input type="hidden" name="placement_id" value="<?php echo $placement['placement_id']; ?>">
                            <button type="submit" class="btn btn-success" style="margin-top: 1rem;">Register for Drive</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success" style="margin-top: 1rem;">✓ You have registered for this placement drive</div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No placement drives scheduled at the moment.</div>
        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
