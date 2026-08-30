<?php
require "config.php";

if (isset($_POST["add_emergency"])) {
    $name = trim($_POST["patient_name"]);
    $condition = trim($_POST["condition"]);
    $priority = (int)$_POST["priority"];

    $stmt = $conn->prepare(
        "INSERT INTO emergency_queue 
        (patient_name, patient_condition, priority) 
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "ssi",
        $name,
        $condition,
        $priority
    );

    $stmt->execute();
}


// Process highest priority patient
if (isset($_GET["process"])) {

    $row = $conn->query(
        "SELECT id FROM emergency_queue 
        WHERE status='Waiting' 
        ORDER BY priority ASC, id ASC 
        LIMIT 1"
    )->fetch_assoc();

    if ($row) {
        $conn->query(
            "UPDATE emergency_queue 
            SET status='Processed' 
            WHERE id=" . $row["id"]
        );
    }

    header("Location: emergency.php");
    exit();
}


require "header.php";

$result = $conn->query(
    "SELECT * FROM emergency_queue 
    WHERE status='Waiting' 
    ORDER BY priority ASC, id ASC"
);
?>

<h1>Emergency</h1>


<form method="post" class="inline-form">

    <input
        name="patient_name"
        placeholder="Patient name"
        required
    >

    <input
        name="condition"
        placeholder="Condition"
        required
    >

    <select name="priority">
        <option value="1">High</option>
        <option value="2">Medium</option>
        <option value="3">Low</option>
    </select>

    <button name="add_emergency">
        Add Emergency
    </button>

    <a
        class="button secondary-link"
        href="emergency.php?process=1"
    >
        Process Highest Priority
    </a>

</form>


<table>

    <tr>
        <th>Priority</th>
        <th>Patient</th>
        <th>Condition</th>
    </tr>


    <?php
    while ($row = $result->fetch_assoc()) {

        if ($row["priority"] == 1) {
            $text = "High";
        } elseif ($row["priority"] == 2) {
            $text = "Medium";
        } else {
            $text = "Low";
        }
    ?>

        <tr>

            <td>
                <?php echo $text; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["patient_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["patient_condition"]); ?>
            </td>

        </tr>

    <?php } ?>

</table>


<?php require "footer.php"; ?>