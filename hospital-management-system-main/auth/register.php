<?php
include "../db/connection.php";
include "../includes/navbar.php";

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $first_name = trim($_POST['firstname']);
    $last_name = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'patient'; // default role

    // Server-side validation
    if (strlen($first_name) < 2) $errors[] = "First name must be at least 2 characters.";
    if (strlen($last_name) < 2) $errors[] = "Last name must be at least 2 characters.";
    if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) $errors[] = "Username must contain only letters and numbers.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    // Check if username/email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) $errors[] = "Username or Email already exists.";

    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $username, $email, $hashed_password, $role]);
        $success = "Registration successful! You can now <a href='login.php'>login</a>.";
    }
}
?>

<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/register.css">

<div class="register-container">
    <h2>Register</h2>

    <?php if(!empty($errors)): ?>
        <div class="error">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success">
            <p><?= $success ?></p>
        </div>
    <?php endif; ?>

    <form id="registerForm" action="" method="post">
        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname" placeholder="Enter first name" required>
        </div>

        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" placeholder="Enter last name" required>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>
</div>

<script>
    document.getElementById("registerForm").addEventListener("submit", function(e) {
        let first = document.getElementById("firstname").value.trim();
        let last = document.getElementById("lastname").value.trim();
        let user = document.getElementById("username").value.trim();
        let email = document.getElementById("email").value.trim();
        let pass = document.getElementById("password").value;
        let confirm = document.getElementById("confirm_password").value;

        if (first.length < 2) { alert("First name must be at least 2 characters."); e.preventDefault(); return; }
        if (last.length < 2) { alert("Last name must be at least 2 characters."); e.preventDefault(); return; }
        if (!/^[a-zA-Z0-9]+$/.test(user)) { alert("Username must contain only letters and numbers."); e.preventDefault(); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert("Invalid email address."); e.preventDefault(); return; }
        if (pass !== confirm) { alert("Passwords do not match."); e.preventDefault(); return; }
        if (pass.length < 6) { alert("Password must be at least 6 characters."); e.preventDefault(); return; }
    });
</script>