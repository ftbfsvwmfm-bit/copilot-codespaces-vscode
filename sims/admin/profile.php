<?php
$page_title = 'Admin Profile';
include '../includes/header.php';

if (!hasRole('administrator')) {
    redirect('login.php');
}

// Get admin user details
$query = "SELECT * FROM users WHERE user_id = $_SESSION[user_id]";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile.php" class="active">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Administrator Profile</h1>

        <div class="card card-primary">
            <h3>Profile Information</h3>
            <table>
                <tr>
                    <td><strong>Username:</strong></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <td><strong>Role:</strong></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Account Created:</strong></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($user['created_at'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Last Updated:</strong></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($user['updated_at'])); ?></td>
                </tr>
            </table>
        </div>

        <div class="card card-success">
            <h3>System Overview</h3>
            <p>As an administrator, you have full access to:</p>
            <ul style="margin-left: 2rem;">
                <li>Student management and records</li>
                <li>Faculty management</li>
                <li>Course and subject configuration</li>
                <li>Exam scheduling and management</li>
                <li>Placement management</li>
                <li>System reports and analytics</li>
                <li>User role management</li>
                <li>System settings and configuration</li>
            </ul>
        </div>

        <div class="alert alert-warning">
            <strong>Security Reminder:</strong> As administrator, please ensure:
            <ul style="margin-left: 2rem;">
                <li>Passwords are kept confidential</li>
                <li>Regular backups are taken</li>
                <li>User activity is monitored</li>
                <li>System updates are applied regularly</li>
            </ul>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
