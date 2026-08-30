<?php
require "config.php";

// Add new bill
if (isset($_POST["add_bill"])) {
    $patient = trim($_POST["patient_name"]);
    $amount = (float)$_POST["amount"];
    $status = $_POST["status"];

    $stmt = $conn->prepare(
        "INSERT INTO bills (patient_name, amount, status) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sds", $patient, $amount, $status);
    $stmt->execute();
}

// Change unpaid bill to paid
if (isset($_POST["mark_paid"])) {
    $bill_id = (int)$_POST["bill_id"];

    $stmt = $conn->prepare(
        "UPDATE bills SET status = 'Paid' WHERE id = ?"
    );
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
}

require "header.php";

// Get all bills
$result = $conn->query("SELECT * FROM bills ORDER BY id DESC");
?>

<h1>Billing</h1>

<form method="post" class="inline-form">

    <input 
        name="patient_name" 
        placeholder="Patient name" 
        required
    >

    <input 
        type="number" 
        step="0.01" 
        name="amount" 
        placeholder="Amount" 
        required
    >

    <select name="status">
        <option value="Unpaid">Unpaid</option>
        <option value="Paid">Paid</option>
    </select>

    <button type="submit" name="add_bill">
        Add Bill
    </button>

</form>

<table>
    <tr>
        <th>Bill ID</th>
        <th>Patient</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>
            <td>B<?php echo $row["id"]; ?></td>

            <td>
                <?php echo htmlspecialchars($row["patient_name"]); ?>
            </td>

            <td>
                ₹ <?php echo $row["amount"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["status"]); ?>
            </td>

            <td>

                <?php if ($row["status"] == "Unpaid") { ?>

                    <form method="post">
                        <input 
                            type="hidden" 
                            name="bill_id" 
                            value="<?php echo $row["id"]; ?>"
                        >

                        <button 
                            type="submit" 
                            name="mark_paid"
                        >
                            Mark as Paid
                        </button>
                    </form>

                <?php } else { ?>

                    ✓ Paid

                <?php } ?>

            </td>
        </tr>

    <?php } ?>

</table>

<?php require "footer.php"; ?>