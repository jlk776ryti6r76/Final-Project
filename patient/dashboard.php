<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

include "../includes/user-sidebar.php";
$patient_id = $_SESSION['user_id'];
?>
<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/patient-dashboard.css">

<div class="dashboard-container">
    <p>Manage your appointments, view lab results, and update your profile here.</p>

    <div class="cards">
        <div class="card">
            <h3>Upcoming Appointments</h3>
            <p>3</p>
        </div>
        <div class="card">
            <h3>Completed Appointments</h3>
            <p>5</p>
        </div>
        <div class="card">
            <h3>Lab Results</h3>
            <p>2</p>
        </div>
    </div>
</div>
