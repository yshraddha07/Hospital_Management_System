<?php
require "config.php";

if (isset($_POST["add_bill"])) {
    $patient = trim($_POST["patient_name"]);
    $amount = (float)$_POST["amount"];
    $stmt = $conn->prepare("INSERT INTO bills (patient_name, amount) VALUES (?, ?)");
    $stmt->bind_param("sd", $patient, $amount);
    $stmt->execute();
}

require "header.php";
$result = $conn->query("SELECT * FROM bills ORDER BY id DESC");
?>
<h1>Billing</h1>
<form method="post" class="inline-form">
<input name="patient_name" placeholder="Patient name" required>
<input type="number" step="0.01" name="amount" placeholder="Amount" required>
<button name="add_bill">Add Bill</button>
</form>
<table><tr><th>Bill ID</th><th>Patient</th><th>Amount</th><th>Status</th></tr>
<?php while ($row=$result->fetch_assoc()) { ?><tr><td>B<?php echo $row["id"]; ?></td><td><?php echo htmlspecialchars($row["patient_name"]); ?></td><td>₹ <?php echo $row["amount"]; ?></td><td><?php echo htmlspecialchars($row["status"]); ?></td></tr><?php } ?>
</table>
<?php require "footer.php"; ?>