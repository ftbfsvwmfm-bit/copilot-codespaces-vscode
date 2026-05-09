<?php
include 'includes/config.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect('pages/student_dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">SIMS - Student Information Management System</div>
        </div>
    </header>

    <div class="login-container" style="max-width: 500px;">
        <h1 class="login-title">Register</h1>

        <?php
        if (isset($_SESSION['errors'])):
        ?>
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php
        endif;
        ?>

        <form method="POST" action="includes/auth.php" class="login-form" onsubmit="return validateRegistrationForm()">
            <input type="hidden" name="action" value="register">

            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>

            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number (10 digits)</label>
                <input type="tel" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="password">Password (minimum 6 characters) *</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password *</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script src="assets/js/validation.js"></script>
</body>
</html>
