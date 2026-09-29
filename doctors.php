<?php
require "config.php";

if (isset($_POST["add_doctor"])) {
    $name = trim($_POST["name"]);
    $specialization = trim($_POST["specialization"]);
    $phone = trim($_POST["phone"]);
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $specialization, $phone);
    $stmt->execute();
}

require "header.php";
$result = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
?>
<div class="page-title"><div><h1>Doctors</h1><p class="subtitle">Available hospital doctors</p></div></div>

<form method="post" class="inline-form">
    <input name="name" placeholder="Doctor name" required>
    <input name="specialization" placeholder="Specialization" required>
    <input name="phone" placeholder="Phone" required>
    <button name="add_doctor">+ Add Doctor</button>
</form>

<table>
<tr><th>ID</th><th>Name</th><th>Specialization</th><th>Phone</th><th>Status</th></tr>
<?php while ($row = $result->fetch_assoc()) { ?>
<tr><td>D<?php echo $row["id"]; ?></td><td><?php echo htmlspecialchars($row["name"]); ?></td><td><?php echo htmlspecialchars($row["specialization"]); ?></td><td><?php echo htmlspecialchars($row["phone"]); ?></td><td><span class="status">Available</span></td></tr>
<?php } ?>
</table>
<?php require "footer.php"; ?>