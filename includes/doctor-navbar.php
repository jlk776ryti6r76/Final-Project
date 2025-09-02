<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<nav class="doctor-navbar">
    <div class="doctor-logo">Doctor Panel</div>
    <ul class="doctor-nav-links">
        <li><a href="../doctor/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'doctor-dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="../doctor/doctor-patients.php" class="<?= basename($_SERVER['PHP_SELF']) == 'doctor-patients.php' ? 'active' : '' ?>">My Patients</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</nav>
