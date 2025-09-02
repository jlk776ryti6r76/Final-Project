<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

include "../includes/doctor-navbar.php";
include "../db/connection.php";

$doctor_id = $_SESSION['user_id'];

// Total patients
$stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) AS total_patients FROM appointments WHERE doctor_id = ?");
$stmt->execute([$doctor_id]);
$totalPatients = $stmt->fetch(PDO::FETCH_ASSOC)['total_patients'] ?? 0;

// Today's appointments
$stmt = $conn->prepare("SELECT COUNT(*) AS todays_appointments FROM appointments WHERE doctor_id = ? AND appointment_date = CURDATE()");
$stmt->execute([$doctor_id]);
$todaysAppointments = $stmt->fetch(PDO::FETCH_ASSOC)['todays_appointments'] ?? 0;

// Pending reports placeholder
$pendingReports = 0;

// Messages placeholder (if you don’t have messages table yet)
$unreadMessages = 0;
?>

<link rel="stylesheet" href="../css/doctor-navbar.css">
<link rel="stylesheet" href="../css/doctor-dashboard.css">

<div class="doctor-content">
    <h1>Welcome, Dr. <?= $_SESSION['username'] ?>!</h1>
    <p>Here you can view your schedule and manage your patients.</p>

    <div class="doctor-cards">
        <div class="doctor-card">
            <h3>Today's Appointments</h3>
            <p><?= $todaysAppointments ?></p>
        </div>
        <div class="doctor-card">
            <h3>Total Patients</h3>
            <p><?= $totalPatients ?></p>
        </div>
        <div class="doctor-card">
            <h3>Pending Reports</h3>
            <p><?= $pendingReports ?></p>
        </div>
        <div class="doctor-card">
            <h3>Messages</h3>
            <p><?= $unreadMessages ?></p>
        </div>
    </div>
</div>
