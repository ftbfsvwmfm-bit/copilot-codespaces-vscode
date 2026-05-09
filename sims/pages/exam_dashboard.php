<?php
$page_title = 'Exam Section Dashboard';
include '../includes/header.php';

if (!hasRole('exam_section')) {
    redirect('login.php');
}

// Get statistics
$total_exams = $conn->query("SELECT COUNT(*) as count FROM exam_timetables")->fetch_assoc()['count'];
$total_allocations = $conn->query("SELECT COUNT(*) as count FROM room_allocations")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(DISTINCT room_number) as count FROM room_allocations")->fetch_assoc()['count'];
?>

<div class="sidebar-container">
    <aside class="sidebar">
        <h3>Exam Section Menu</h3>
        <ul>
            <li><a href="exam_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="#">Manage Timetables</a></li>
            <li><a href="#">Room Allocation</a></li>
            <li><a href="#">Upload Results</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Exam Section Dashboard</h1>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $total_exams; ?></h3>
                <p>Total Exams Scheduled</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <h3><?php echo $total_allocations; ?></h3>
                <p>Room Allocations</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                <h3><?php echo $total_rooms; ?></h3>
                <p>Rooms Allocated</p>
            </div>
        </div>

        <div class="card card-primary">
            <h3>Quick Actions</h3>
            <div class="btn-group">
                <a href="#" class="btn btn-primary">Create Timetable</a>
                <a href="#" class="btn btn-success">Allocate Rooms</a>
                <a href="#" class="btn btn-warning">Publish Results</a>
            </div>
        </div>

        <div class="card">
            <h3>Upcoming Exams</h3>
            <p>Features for managing exam schedules, room allocations, and result publication will be displayed here.</p>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
