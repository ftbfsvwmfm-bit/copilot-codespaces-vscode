<?php
/*
 * Authentication Handler
 * SIMS - Student Information Management System
 */

include 'config.php';

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = escapeInput($_POST['username']);
    $email = escapeInput($_POST['email']);
    $password = escapeInput($_POST['password']);
    $confirmPassword = escapeInput($_POST['confirmPassword']);
    $firstName = escapeInput($_POST['firstName']);
    $lastName = escapeInput($_POST['lastName']);
    $phone = escapeInput($_POST['phone'] ?? '');
    $role = 'student'; // Default role is student

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if (empty($firstName) || empty($lastName)) {
        $errors[] = 'First and last names are required';
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // Check if username or email already exists
    $check_query = "SELECT user_id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $errors[] = 'Username or email already exists';
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification code
        $verification_code = bin2hex(random_bytes(16));

        // Insert into users table
        $insert_query = "INSERT INTO users (username, password, email, role, verification_code, is_verified) 
                        VALUES ('$username', '$hashed_password', '$email', '$role', '$verification_code', false)";

        if ($conn->query($insert_query) === TRUE) {
            $user_id = $conn->insert_id;

            // Insert into students table
            $insert_student = "INSERT INTO students (user_id, first_name, last_name, phone) 
                              VALUES ($user_id, '$firstName', '$lastName', '$phone')";

            if ($conn->query($insert_student) === TRUE) {
                $_SESSION['message'] = 'Registration successful! A verification email has been sent.';
                $_SESSION['message_type'] = 'success';
                header('Location: login.php');
                exit();
            } else {
                $errors[] = 'Error creating student record: ' . $conn->error;
            }
        } else {
            $errors[] = 'Error registering user: ' . $conn->error;
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: register.php');
        exit();
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = escapeInput($_POST['username']);
    $password = escapeInput($_POST['password']);

    $errors = [];

    if (empty($username) || empty($password)) {
        $errors[] = 'Username and password are required';
    }

    if (empty($errors)) {
        // Query user
        $query = "SELECT user_id, password, role, is_verified FROM users WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                if ($row['is_verified']) {
                    // Set session variables
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $row['role'];

                    // Set login time
                    $conn->query("UPDATE users SET updated_at = NOW() WHERE user_id = {$row['user_id']}");

                    // Redirect based on role
                    switch ($row['role']) {
                        case 'administrator':
                            redirect('admin/dashboard.php');
                            break;
                        case 'faculty':
                            redirect('pages/faculty_dashboard.php');
                            break;
                        case 'exam_section':
                            redirect('pages/exam_dashboard.php');
                            break;
                        case 'placement_cell':
                            redirect('pages/placement_dashboard.php');
                            break;
                        case 'student':
                        default:
                            redirect('pages/student_dashboard.php');
                            break;
                    }
                } else {
                    $errors[] = 'Please verify your email before logging in';
                }
            } else {
                $errors[] = 'Invalid username or password';
            }
        } else {
            $errors[] = 'Invalid username or password';
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: login.php');
        exit();
    }
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    redirect('login.php');
}

// Redirect to login if not authenticated
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'register.php') {
    redirect('login.php');
}
?>
