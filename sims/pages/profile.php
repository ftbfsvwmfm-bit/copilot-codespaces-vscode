<?php
$page_title = 'My Profile';
include '../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$role = $_SESSION['role'];

// Get user details based on role
if ($role === 'student') {
    $query = "SELECT s.*, u.email, u.username FROM students s 
              JOIN users u ON s.user_id = u.user_id 
              WHERE u.user_id = $_SESSION[user_id]";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
} elseif ($role === 'faculty') {
    $query = "SELECT f.*, u.email, u.username FROM faculty f 
              JOIN users u ON f.user_id = u.user_id 
              WHERE u.user_id = $_SESSION[user_id]";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $phone = escapeInput($_POST['phone'] ?? '');
    $address = escapeInput($_POST['address'] ?? '');
    $city = escapeInput($_POST['city'] ?? '');
    $state = escapeInput($_POST['state'] ?? '');

    if ($role === 'student') {
        $student_id = $user['student_id'];
        $update = "UPDATE students SET phone = '$phone', address = '$address', city = '$city', state = '$state' 
                  WHERE student_id = $student_id";
    } elseif ($role === 'faculty') {
        $faculty_id = $user['faculty_id'];
        $update = "UPDATE faculty SET phone = '$phone' WHERE faculty_id = $faculty_id";
    }

    if ($conn->query($update) === TRUE) {
        $_SESSION['message'] = 'Profile updated successfully';
        $_SESSION['message_type'] = 'success';
        // Refresh user data
        header('Refresh:0');
    } else {
        $_SESSION['message'] = 'Error updating profile: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
}
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="<?php echo $role === 'student' ? 'student_dashboard.php' : 'faculty_dashboard.php'; ?>">Dashboard</a></li>
            <li><a href="profile.php" class="active">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">My Profile</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <div class="card card-primary">
            <h3>Profile Information</h3>
            <form method="POST" onsubmit="return validateStudentForm()">
                <input type="hidden" name="action" value="update_profile">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <?php if ($role === 'student'): ?>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Roll Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['roll_number']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Year</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['year'] ?? 'N/A'); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['dob'] ?? 'N/A'); ?>" disabled>
                        </div>
                    <?php elseif ($role === 'faculty'): ?>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['specialization'] ?? 'N/A'); ?>" disabled>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Contact Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <?php if ($role === 'student'): ?>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="address">Address</label>
                            <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="btn-group mt-2">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
