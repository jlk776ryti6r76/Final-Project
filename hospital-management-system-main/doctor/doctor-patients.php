<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

include "../includes/doctor-navbar.php";
include "../db/connection.php";

$doctor_id = $_SESSION['user_id'];

// ------------------ Handle Cancel Appointment ------------------
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$cancel_id, $doctor_id]);
    header("Location: doctor-patients.php");
    exit;
}

// ------------------ Handle Mark Completed ------------------
if (isset($_GET['complete_id'])) {
    $complete_id = $_GET['complete_id'];
    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$complete_id, $doctor_id]);
    header("Location: doctor-patients.php");
    exit;
}

// ------------------ Fetch Doctor's Patients ------------------
$sql = "SELECT a.id AS appointment_id, u.first_name, u.last_name, u.email, u.username, 
        a.appointment_date, a.appointment_time, a.status
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date, a.appointment_time";
$stmt = $conn->prepare($sql);
$stmt->execute([$doctor_id]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/doctor-navbar.css">
<link rel="stylesheet" href="../css/doctor-dashboard.css">

<div class="doctor-content">
    <h2>My Patients</h2>

    <?php if(count($patients) > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Patient Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Add Test</th>
                <th>Mark Completed</th>
                <th>Cancel</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($patients as $p): ?>
                <tr>
                    <td><?= $p['first_name'] . ' ' . $p['last_name'] ?></td>
                    <td><?= $p['username'] ?></td>
                    <td><?= $p['email'] ?></td>
                    <td><?= date("d M Y", strtotime($p['appointment_date'])) ?></td>
                    <td><?= date("h:i A", strtotime($p['appointment_time'])) ?></td>
                    <td><?= ucfirst($p['status']) ?></td>

                    <!-- Add Test Button -->
                    <td>
                        <?php if($p['status'] != 'cancelled'): ?>
                            <button class="btn-add-test" onclick="alert('Add Test functionality coming soon!')">Add Test</button>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <!-- Mark Completed -->
                    <td>
                        <?php if($p['status'] != 'completed' && $p['status'] != 'cancelled'): ?>
                            <a href="?complete_id=<?= $p['appointment_id'] ?>" class="btn-complete" onclick="return confirm('Mark this appointment as completed?')">Complete</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <!-- Cancel -->
                    <td>
                        <?php if($p['status'] != 'completed' && $p['status'] != 'cancelled'): ?>
                            <a href="?cancel_id=<?= $p['appointment_id'] ?>" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No patients found.</p>
    <?php endif; ?>
</div>