<?php
require "config.php";

if (isset($_POST["add_patient"])) {
    $name = trim($_POST["patient_name"]);

    $stmt = $conn->prepare(
        "INSERT INTO opd_queue (patient_name) VALUES (?)"
    );

    $stmt->bind_param("s", $name);
    $stmt->execute();
}


if (isset($_GET["next"])) {

    $row = $conn->query(
        "SELECT id FROM opd_queue 
        WHERE status='Waiting' 
        ORDER BY id 
        LIMIT 1"
    )->fetch_assoc();

    if ($row) {
        $conn->query(
            "UPDATE opd_queue 
            SET status='Completed' 
            WHERE id=" . $row["id"]
        );
    }

    header("Location: opd.php");
    exit();
}


require "header.php";

$result = $conn->query(
    "SELECT * FROM opd_queue 
    WHERE status='Waiting' 
    ORDER BY id"
);
?>

<h1>OPD Queue</h1>

<form method="post" class="inline-form">

    <input
        name="patient_name"
        placeholder="Patient name"
        required
    >

    <button name="add_patient">
        Add to Queue
    </button>

    <a
        class="button secondary-link"
        href="opd.php?next=1"
    >
        Process Next
    </a>

</form>


<div class="queue-list">

    <?php
    $i = 1;

    while ($row = $result->fetch_assoc()) {
        echo "<div class='queue-item'>
                <b>$i</b>
                " . htmlspecialchars($row["patient_name"]) . "
              </div>";

        $i++;
    }

    if ($i == 1) {
        echo "<p>No patients waiting.</p>";
    }
    ?>

</div>

<?php require "footer.php"; ?>