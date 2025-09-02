<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
}
include "../db/connection.php";
include "../includes/user-sidebar.php";

$user_id = $_SESSION['user_id'];
$success = '';
$errors = [];

// Handle cancel
if(isset($_GET['cancel_id'])) {
    $appointment_id = $_GET['cancel_id'];

    // Verify this appointment belongs to the logged-in user
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE id=? AND user_id=?");
    $stmt->execute([$appointment_id, $user_id]);

    if($stmt->rowCount() > 0){
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
        $stmt->execute([$appointment_id]);
        $success = "Appointment canceled successfully.";
    } else {
        $errors[] = "Appointment not found or you don't have permission to cancel it.";
    }
}

// Fetch upcoming appointments
$stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.appointment_time, 
                               u.first_name, u.last_name, d.position, d.department
                        FROM appointments a
                        JOIN doctors d2 ON a.doctor_id = d2.id
                        JOIN users u ON d2.user_id = u.id
                        JOIN doctors d ON a.doctor_id = d.id
                        WHERE a.user_id = ? 
                        ORDER BY a.appointment_date ASC, a.appointment_time ASC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/patient-dashboard.css">
<link rel="stylesheet" href="../css/navbar.css">

<div class="dashboard-container">
    <h1>Cancel Appointments</h1>

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

    <div class="cards">
        <?php if(count($appointments) > 0): ?>
            <?php foreach($appointments as $app): ?>
                <div class="card">
                    <h3><?= $app['first_name'] ?> <?= $app['last_name'] ?></h3>
                    <p><strong>Position:</strong> <?= $app['position'] ?></p>
                    <p><strong>Department:</strong> <?= $app['department'] ?></p>
                    <p><strong>Date:</strong> <?= $app['appointment_date'] ?></p>
                    <p><strong>Time:</strong> <?= $app['appointment_time'] ?></p>
                    <a href="?cancel_id=<?= $app['id'] ?>" class="btn" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No upcoming appointments.</p>
        <?php endif; ?>
    </div>
</div>
