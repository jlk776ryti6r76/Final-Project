<aside class="sidebar">
    <h2>User Panel</h2>
    <ul>
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="book-appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'book-appointment.php' ? 'active' : '' ?>">Book Appointment</a></li>
        <li><a href="cancel-appointment.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cancel-appointment.php' ? 'active' : '' ?>">Cancel Appointment</a></li>
        <li><a href="lab-results.php" class="<?= basename($_SERVER['PHP_SELF']) == 'lab-results.php' ? 'active' : '' ?>">Lab Results</a></li>
        <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">Profile</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>
