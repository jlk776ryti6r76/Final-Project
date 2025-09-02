<?php include "../includes/navbar.php"; ?>

<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/forgot.css">

<div class="forgot-container">
    <h2>Forgot Password</h2>
    <p>Please enter your email address to reset your password.</p>

    <form id="forgotForm" action="#" method="post">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <button type="submit" class="btn">Send Reset Link</button>
    </form>
</div>

<!-- Basic Validation -->
<script>
    document.getElementById("forgotForm").addEventListener("submit", function(e) {
        let email = document.getElementById("email").value.trim();

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert("Please enter a valid email address.");
            e.preventDefault();
        }
    });
</script>
