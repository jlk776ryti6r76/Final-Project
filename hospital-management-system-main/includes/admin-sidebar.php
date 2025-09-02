<aside class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="manage-users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : '' ?>">Manage Users</a></li>
        <li><a href="manage-doctors.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage-doctors.php' ? 'active' : '' ?>">Manage Doctors</a></li>
        <li><a href="manage-lab-assistants.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage-lab-assistants.php' ? 'active' : '' ?>">Manage Lab Assistants</a></li>
        <li><a href="manage-appointments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage-appointments.php' ? 'active' : '' ?>">Manage Appointments</a></li>
        <li><a href="manage-reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage-reports.php' ? 'active' : '' ?>">Manage Reports</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>