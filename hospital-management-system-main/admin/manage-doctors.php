<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include "../db/connection.php";
include "../includes/admin-sidebar.php";

// ------------------ Handle Add ------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_doctor'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, 'doctor')");
    $stmt->execute([$first_name, $last_name, $username, $email, $password]);
    $user_id = $conn->lastInsertId();

    // Insert into doctors table
    $stmt = $conn->prepare("INSERT INTO doctors (user_id, department, position) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $department, $position]);

    header("Location: manage-doctors.php");
    exit;
}

// ------------------ Handle Update ------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doctor_id'])) {
    $doctor_id = $_POST['doctor_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $position = $_POST['position'];

    $sql_update = "UPDATE users u
                   JOIN doctors d ON u.id = d.user_id
                   SET u.first_name = ?, u.last_name = ?, u.username = ?, u.email = ?,
                       d.department = ?, d.position = ?
                   WHERE d.id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute([$first_name, $last_name, $username, $email, $department, $position, $doctor_id]);

    header("Location: manage-doctors.php");
    exit;
}

// ------------------ Handle Delete ------------------
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // First get user_id
    $stmt = $conn->prepare("SELECT user_id FROM doctors WHERE id = ?");
    $stmt->execute([$delete_id]);
    $user_id = $stmt->fetchColumn();

    // Delete from doctors
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$delete_id]);

    // Delete from users
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    header("Location: manage-doctors.php");
    exit;
}

// ------------------ Fetch Doctors ------------------
$sql = "SELECT d.id, u.first_name, u.last_name, u.username, u.email, d.department, d.position
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        WHERE u.role = 'doctor'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/admin-dashboard.css">

<div class="admin-content">
    <h2>Manage Doctors</h2>
    <a href="#" class="btn add-btn" id="openAddModal">Add Doctor</a>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Department</th>
            <th>Position</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($doctors) > 0): ?>
            <?php foreach($doctors as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['position'] ?></td>
                    <td>
                        <a href="#" class="edit-btn">Edit</a> |
                        <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8">No doctors found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ------------------ Add Modal ------------------ -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add Doctor</h3>
        <form method="post">
            <input type="hidden" name="add_doctor" value="1">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" required>
            </div>
            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" required>
            </div>
            <button type="submit" class="btn">Add Doctor</button>
        </form>
    </div>
</div>

<!-- ------------------ Edit Modal ------------------ -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Doctor</h3>
        <form method="post">
            <input type="hidden" name="doctor_id" id="doctor_id">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" id="department" required>
            </div>
            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" id="position" required>
            </div>
            <button type="submit" class="btn">Update Doctor</button>
        </form>
    </div>
</div>

<!-- ------------------ JS ------------------ -->
<script>
    // Edit Modal
    const editModal = document.getElementById("editModal");
    const editClose = editModal.querySelector(".close");
    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const row = this.closest("tr");
            document.getElementById("doctor_id").value = row.cells[0].innerText;
            document.getElementById("first_name").value = row.cells[1].innerText;
            document.getElementById("last_name").value = row.cells[2].innerText;
            document.getElementById("username").value = row.cells[3].innerText;
            document.getElementById("email").value = row.cells[4].innerText;
            document.getElementById("department").value = row.cells[5].innerText;
            document.getElementById("position").value = row.cells[6].innerText;
            editModal.style.display = "block";
        });
    });
    editClose.onclick = () => editModal.style.display = "none";

    // Add Modal
    const addModal = document.getElementById("addModal");
    const openAddModalBtn = document.getElementById("openAddModal");
    const addClose = addModal.querySelector(".close");
    openAddModalBtn.onclick = () => addModal.style.display = "block";
    addClose.onclick = () => addModal.style.display = "none";

    // Close modals on outside click
    window.onclick = function(event) {
        if(event.target == editModal) editModal.style.display = "none";
        if(event.target == addModal) addModal.style.display = "none";
    };
</script>