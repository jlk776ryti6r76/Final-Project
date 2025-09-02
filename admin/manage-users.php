<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../db/connection.php";
include "../includes/admin-sidebar.php";

// ------------------ Handle Update ------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute([$first_name, $last_name, $username, $email, $role, $user_id]);

    header("Location: manage-users.php");
    exit;
}

// ------------------ Handle Delete ------------------
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete patient
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);

    header("Location: manage-users.php");
    exit;
}

// ------------------ Fetch Users ------------------
$sql = "SELECT * FROM users WHERE role != 'admin'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/admin-dashboard.css">

<div class="admin-content">
    <h2>Manage Users</h2>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($users) > 0): ?>
            <?php foreach($users as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td>
                        <a href="#" class="edit-btn">Edit</a> |
                        <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ------------------ Edit Modal ------------------ -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit User</h3>
        <form method="post" id="editUserForm">
            <input type="hidden" name="user_id" id="user_id">

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
                <label>Role</label>
                <select name="role" id="role" required>
                    <option value="doctor">Doctor</option>
                    <option value="patient">Patient</option>
                    <option value="lab">Lab Assistant</option>
                </select>
            </div>

            <button type="submit" class="btn">Update User</button>
        </form>
    </div>
</div>

<!-- ------------------ JS ------------------ -->
<script>
    const modal = document.getElementById("editModal");
    const span = modal.querySelector(".close");

    // Open modal and populate fields
    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const row = this.closest("tr");

            document.getElementById("user_id").value = row.cells[0].innerText;
            document.getElementById("first_name").value = row.cells[1].innerText;
            document.getElementById("last_name").value = row.cells[2].innerText;
            document.getElementById("username").value = row.cells[3].innerText;
            document.getElementById("email").value = row.cells[4].innerText;
            document.getElementById("role").value = row.cells[5].innerText.toLowerCase();

            modal.style.display = "block";
        });
    });

    // Close modal
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) modal.style.display = "none";
    }
</script>