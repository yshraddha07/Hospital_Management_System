<?php
require "config.php";
require "header.php";

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $old = $conn->query("SELECT name FROM patients WHERE id=$id")->fetch_assoc();
    if ($old) {
        if (!isset($_SESSION["undo_stack"])) $_SESSION["undo_stack"] = [];
        $_SESSION["undo_stack"][] = [
            "type" => "delete_patient",
            "id" => $id,
            "name" => $old["name"]
        ];
    }
    $conn->query("DELETE FROM patients WHERE id=$id");
    header("Location: patients.php");
    exit();
}

$result = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>
<div class="page-title">
    <div><h1>All Patients</h1><p class="subtitle">Manage hospital patient records</p></div>
    <a class="button" href="register_patient.php">+ Register Patient</a>
</div>

<table>
<tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Status</th><th>Action</th></tr>
<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td>P<?php echo $row["id"]; ?></td>
    <td><?php echo htmlspecialchars($row["name"]); ?></td>
    <td><?php echo $row["age"]; ?></td>
    <td><?php echo htmlspecialchars($row["gender"]); ?></td>
    <td><?php echo htmlspecialchars($row["phone"]); ?></td>
    <td><span class="status">Active</span></td>
    <td>
        <a class="small-btn" href="history.php?id=<?php echo $row["id"]; ?>">History</a>
        <a class="delete-btn" onclick="return confirm('Delete this patient?')" href="patients.php?delete=<?php echo $row["id"]; ?>">Delete</a>
    </td>
</tr>
<?php } ?>
</table>
<?php require "footer.php"; ?>