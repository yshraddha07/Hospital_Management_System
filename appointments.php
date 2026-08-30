<?php
require "config.php";

if (isset($_POST["add_appointment"])) {
    $patient = trim($_POST["patient_name"]);
    $doctor = trim($_POST["doctor_name"]);
    $time = trim($_POST["appointment_time"]);

    $stmt = $conn->prepare(
        "INSERT INTO appointments 
        (patient_name, doctor_name, appointment_time, status) 
        VALUES (?, ?, ?, 'Waiting')"
    );

    $stmt->bind_param("sss", $patient, $doctor, $time);
    $stmt->execute();

    header("Location: appointments.php");
    exit();
}


// Complete appointment
if (isset($_GET["complete"])) {
    $id = (int)$_GET["complete"];

    $conn->query(
        "UPDATE appointments 
        SET status = 'Completed' 
        WHERE id = $id"
    );

    header("Location: appointments.php");
    exit();
}


require "header.php";

$result = $conn->query(
    "SELECT * FROM appointments 
    WHERE status = 'Waiting' 
    ORDER BY id"
);
?>

<h1>Appointments</h1>

<div class="two-columns">

    <div class="panel">

        <h3>Add Appointment</h3>

        <form method="post">

            <input
                name="patient_name"
                placeholder="Patient name"
                required
            >

            <input
                name="doctor_name"
                placeholder="Doctor name"
                required
            >

            <input
                type="time"
                name="appointment_time"
                required
            >

            <button name="add_appointment">
                Add Appointment
            </button>

        </form>

    </div>


    <div class="panel">

        <h3>Queue Order</h3>

        <?php
        $n = 1;

        while ($row = $result->fetch_assoc()) {

            echo "<p>
                <b>$n.</b> " .
                htmlspecialchars($row["patient_name"]) .
                " → " .
                htmlspecialchars($row["doctor_name"]) .
                " <a class='small-btn' href='appointments.php?complete=" .
                $row["id"] .
                "'>Complete</a>
            </p>";

            $n++;
        }

        if ($n == 1) {
            echo "<p>No appointments waiting.</p>";
        }
        ?>

    </div>

</div>

<?php require "footer.php"; ?>