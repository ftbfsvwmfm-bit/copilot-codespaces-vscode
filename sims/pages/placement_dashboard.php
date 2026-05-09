<?php
$page_title = 'Placement Cell Dashboard';
include '../includes/header.php';

if (!hasRole('placement_cell')) {
    redirect('login.php');
}

// Get statistics
$total_companies = $conn->query("SELECT COUNT(*) as count FROM companies")->fetch_assoc()['count'];
$total_placements = $conn->query("SELECT COUNT(*) as count FROM placements")->fetch_assoc()['count'];
$total_registered = $conn->query("SELECT COUNT(*) as count FROM placement_registrations WHERE status = 'selected'")->fetch_assoc()['count'];
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Placement Cell Menu</h3>
        <ul>
            <li><a href="placement_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="#">Add Company</a></li>
            <li><a href="#">Create Drive</a></li>
            <li><a href="#">Manage Registrations</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Placement Cell Dashboard</h1>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $total_companies; ?></h3>
                <p>Companies Registered</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <h3><?php echo $total_placements; ?></h3>
                <p>Active Drives</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                <h3><?php echo $total_registered; ?></h3>
                <p>Students Placed</p>
            </div>
        </div>

        <div class="card card-primary">
            <h3>Quick Actions</h3>
            <div class="btn-group">
                <a href="#" class="btn btn-primary">Add Company</a>
                <a href="#" class="btn btn-success">Create Drive</a>
                <a href="#" class="btn btn-warning">View Statistics</a>
            </div>
        </div>

        <div class="card">
            <h3>Ongoing Placements</h3>
            <p>Features for managing companies, recruitment drives, and student placements will be displayed here.</p>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
