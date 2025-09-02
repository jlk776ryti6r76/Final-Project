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

// Handle booking
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Basic validation
    if(empty($appointment_date) || empty($appointment_time)) {
        $errors[] = "Please select a date and time.";
    }

    // Check if user already has appointment with same doctor at same time
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE user_id=? AND doctor_id=? AND appointment_date=? AND appointment_time=?");
    $stmt->execute([$user_id, $doctor_id, $appointment_date, $appointment_time]);
    if($stmt->rowCount() > 0){
        $errors[] = "You already have an appointment with this doctor at that time.";
    }

    // Insert appointment
    if(empty($errors)){
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $doctor_id, $appointment_date, $appointment_time]);
        $success = "Appointment booked successfully!";
    }
}

// Fetch all doctors
$stmt = $conn->prepare("SELECT d.id, u.first_name, u.last_name, d.position, d.department
                        FROM doctors d
                        JOIN users u ON d.user_id = u.id");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/patient-dashboard.css">
<link rel="stylesheet" href="../css/navbar.css">

<div class="dashboard-container">
    <h1>Book Appointment</h1>

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
        <?php foreach($doctors as $doc): ?>
            <div class="card">
                <h3><?= $doc['first_name'] ?> <?= $doc['last_name'] ?></h3>
                <p><strong>Position:</strong> <?= $doc['position'] ?></p>
                <p><strong>Department:</strong> <?= $doc['department'] ?></p>

                <!-- Booking form -->
                <form method="post">
                    <input type="hidden" name="doctor_id" value="<?= $doc['id'] ?>">
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="appointment_date" required>
                    </div>
                    <div class="form-group">
                        <label>Time:</label>
                        <input type="time" name="appointment_time" required>
                    </div>
                    <button type="submit" name="book_appointment" class="btn">Book</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
