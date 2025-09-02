<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include "../includes/admin-sidebar.php";
?>
<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/admin-dashboard.css">

<div class="admin-content">
        <h1>Welcome, Admin!</h1>
        <p>Here you can manage hospital activities.</p>

        <div class="cards">
            <div class="card">
                <h3>Total Doctors</h3>
                <p>15</p>
            </div>
            <div class="card">
                <h3>Total Patients</h3>
                <p>120</p>
            </div>
            <div class="card">
                <h3>Lab Tests</h3>
                <p>45</p>
            </div>
            <div class="card">
                <h3>Appointments</h3>
                <p>32</p>
            </div>
        </div>
</div>