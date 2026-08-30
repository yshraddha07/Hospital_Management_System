<?php
require "config.php";

$patientId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($patientId == 0) {
    $p = $conn->query("SELECT id FROM patients ORDER BY id LIMIT 1")->fetch_assoc();
    if ($p) {
        $patientId = $p["id"];
    }
}

// Add medical record
if (isset($_POST["add_record"])) {
    $patientId = (int)$_POST["patient_id"];
    $diagnosis = trim($_POST["diagnosis"]);
    $treatment = trim($_POST["treatment"]);
    $doctor = trim($_POST["doctor"]);

    $stmt = $conn->prepare(
        "INSERT INTO medical_history (patient_id, diagnosis, treatment, doctor) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isss",
        $patientId,
        $diagnosis,
        $treatment,
        $doctor
    );
    $stmt->execute();
}

// Delete medical record
if (isset($_POST["delete_record"])) {
    $recordId = (int)$_POST["record_id"];

    $stmt = $conn->prepare(
        "DELETE FROM medical_history WHERE id = ?"
    );
    $stmt->bind_param("i", $recordId);
    $stmt->execute();
}

require "header.php";

$patient = $conn->query(
    "SELECT * FROM patients WHERE id=$patientId"
)->fetch_assoc();
?>

<h1>Medical History</h1>

<?php if ($patient) { ?>

<div class="two-columns">

    <div class="panel patient-info">
        <h3><?php echo htmlspecialchars($patient["name"]); ?></h3>

        <p>ID: P<?php echo $patient["id"]; ?></p>

        <p>Age: <?php echo $patient["age"]; ?></p>

        <p>Phone: <?php echo htmlspecialchars($patient["phone"]); ?></p>

        <p>
            Department:
            <?php echo htmlspecialchars($patient["department"]); ?>
        </p>
    </div>

    <div class="panel">

        <h3>Add Medical Record</h3>

        <form method="post">

            <input
                type="hidden"
                name="patient_id"
                value="<?php echo $patientId; ?>"
            >

            <input
                type="text"
                name="diagnosis"
                placeholder="Diagnosis"
                required
            >

            <input
                type="text"
                name="treatment"
                placeholder="Treatment"
                required
            >

            <input
                type="text"
                name="doctor"
                placeholder="Doctor"
                required
            >

            <button name="add_record">
                Add Record
            </button>

        </form>

    </div>

</div>


<div class="panel">

    <h3>Linked List View of Medical Records</h3>

    <?php

    $result = $conn->query(
        "SELECT * FROM medical_history 
         WHERE patient_id=$patientId 
         ORDER BY id"
    );

    // Node for Singly Linked List
    class HistoryNode {
        public $data;
        public $next = null;

        public function __construct($data) {
            $this->data = $data;
        }
    }


    // Singly Linked List
    class MedicalLinkedList {
        public $head = null;

        public function insert($data) {

            $newNode = new HistoryNode($data);

            if ($this->head == null) {
                $this->head = $newNode;
                return;
            }

            $temp = $this->head;

            while ($temp->next != null) {
                $temp = $temp->next;
            }

            $temp->next = $newNode;
        }
    }


    // Create linked list from database records
    $list = new MedicalLinkedList();

    while ($row = $result->fetch_assoc()) {
        $list->insert($row);
    }


    // Display linked list
    if ($list->head == null) {

        echo "<p>No medical records yet.</p>";

    } else {

        echo "<div class='linked-list'>";

        $temp = $list->head;

        while ($temp != null) {

            $row = $temp->data;

            echo "<div class='node'>";

            echo "<b>" .
                htmlspecialchars($row["diagnosis"]) .
                "</b><br>";

            echo "<small>" .
                htmlspecialchars($row["treatment"]) .
                "<br>" .
                htmlspecialchars($row["doctor"]) .
                "</small>";

            ?>

            <form method="post" style="margin-top:10px;">

                <input
                    type="hidden"
                    name="record_id"
                    value="<?php echo $row["id"]; ?>"
                >

                <button
                    type="submit"
                    name="delete_record"
                >
                    Delete
                </button>

            </form>

            <?php

            echo "</div>";

            if ($temp->next != null) {
                echo "<span class='arrow'>→</span>";
            }

            $temp = $temp->next;
        }

        echo "</div>";
    }

    ?>

</div>

<?php } else { ?>

    <p>No patient found. Please add a patient first.</p>

<?php } ?>

<?php require "footer.php"; ?>