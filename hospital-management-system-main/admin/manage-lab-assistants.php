<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../db/connection.php";
include "../includes/admin-sidebar.php";

// ------------------ Handle Add ------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lab'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $position   = $_POST['position'];

    // Insert patient
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, 'lab')");
    $stmt->execute([$first_name, $last_name, $username, $email, $password]);
    $user_id = $conn->lastInsertId();

    // Insert lab assistant
    $stmt = $conn->prepare("INSERT INTO lab_assistants (user_id, position) VALUES (?, ?)");
    $stmt->execute([$user_id, $position]);

    header("Location: manage-lab-assistants.php");
    exit;
}

// ------------------ Handle Delete ------------------
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete from lab_assistants first
    $stmt = $conn->prepare("DELETE FROM lab_assistants WHERE id = ?");
    $stmt->execute([$delete_id]);

    // Delete from users
    $stmt = $conn->prepare("DELETE u FROM users u
                            LEFT JOIN lab_assistants l ON u.id = l.user_id
                            WHERE l.id = ?");
    $stmt->execute([$delete_id]);

    header("Location: manage-lab-assistants.php");
    exit;
}

// ------------------ Handle Update ------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lab_id'])) {
    $lab_id     = $_POST['lab_id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $position   = $_POST['position'];

    $stmt = $conn->prepare("UPDATE users u
                            JOIN lab_assistants l ON u.id = l.user_id
                            SET u.first_name = ?, u.last_name = ?, u.username = ?, u.email = ?, l.position = ?
                            WHERE l.id = ?");
    $stmt->execute([$first_name, $last_name, $username, $email, $position, $lab_id]);

    header("Location: manage-lab-assistants.php");
    exit;
}

// ------------------ Fetch Lab Assistants ------------------
$sql = "SELECT l.id, u.first_name, u.last_name, u.username, u.email, l.position
        FROM lab_assistants l
        JOIN users u ON l.user_id = u.id
        WHERE u.role = 'lab'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$labs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/admin-dashboard.css">

<div class="admin-content">
    <h2>Manage Lab Assistants</h2>
    <a href="#" class="btn add-btn" id="openAddModal">Add Lab Assistant</a>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Position</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($labs) > 0): ?>
            <?php foreach($labs as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['position'] ?></td>
                    <td>
                        <a href="#" class="edit-btn">Edit</a> |
                        <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No lab assistants found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add Lab Assistant</h3>
        <form method="post">
            <input type="hidden" name="add_lab" value="1">

            <div class="form-group"><label>First Name</label><input type="text" name="first_name" required></div>
            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" required></div>
            <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Position</label><input type="text" name="position" required></div>

            <button type="submit" class="btn">Add Lab Assistant</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Lab Assistant</h3>
        <form method="post">
            <input type="hidden" name="lab_id" id="lab_id">

            <div class="form-group"><label>First Name</label><input type="text" name="first_name" id="first_name" required></div>
            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" id="last_name" required></div>
            <div class="form-group"><label>Username</label><input type="text" name="username" id="username" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" id="email" required></div>
            <div class="form-group"><label>Position</label><input type="text" name="position" id="position" required></div>

            <button type="submit" class="btn">Update Lab Assistant</button>
        </form>
    </div>
</div>

<script>
    // Add Modal
    const addModal = document.getElementById("addModal");
    document.getElementById("openAddModal").onclick = () => addModal.style.display = "block";
    addModal.querySelector(".close").onclick = () => addModal.style.display = "none";

    // Edit Modal
    const editModal = document.getElementById("editModal");
    editModal.querySelector(".close").onclick = () => editModal.style.display = "none";

    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const row = btn.closest("tr");
            document.getElementById("lab_id").value = row.cells[0].innerText;
            document.getElementById("first_name").value = row.cells[1].innerText;
            document.getElementById("last_name").value = row.cells[2].innerText;
            document.getElementById("username").value = row.cells[3].innerText;
            document.getElementById("email").value = row.cells[4].innerText;
            document.getElementById("position").value = row.cells[5].innerText;
            editModal.style.display = "block";
        }
    });

    window.onclick = (e) => {
        if(e.target == addModal) addModal.style.display = "none";
        if(e.target == editModal) editModal.style.display = "none";
    };
</script>