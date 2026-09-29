<?php
require "config.php";
require "header.php";

$patientId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($patientId == 0) {
    $p = $conn->query("SELECT id FROM patients ORDER BY id LIMIT 1")->fetch_assoc();
    if ($p) $patientId = $p["id"];
}

$checkDateColumn = $conn->query("SHOW COLUMNS FROM medical_history LIKE 'record_date'");
if ($checkDateColumn->num_rows == 0) {
    $conn->query("ALTER TABLE medical_history ADD COLUMN record_date DATE NULL AFTER patient_id");
}

if (isset($_GET["delete_record"])) {
    $recordId = (int)$_GET["delete_record"];
    $conn->query("DELETE FROM medical_history WHERE id=$recordId AND patient_id=$patientId");
    header("Location: history.php?id=$patientId");
    exit();
}

if (isset($_POST["add_record"])) {
    $patientId = (int)$_POST["patient_id"];
    $diagnosis = trim($_POST["diagnosis"]);
    $treatment = trim($_POST["treatment"]);
    $doctor = trim($_POST["doctor"]);
    $recordDate = $_POST["record_date"] ?? date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO medical_history (patient_id, record_date, diagnosis, treatment, doctor) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $patientId, $recordDate, $diagnosis, $treatment, $doctor);
    $stmt->execute();
}

$patient = $conn->query("SELECT * FROM patients WHERE id=$patientId")->fetch_assoc();
?>
<h1>Medical History</h1>
<?php if ($patient) { ?>
<div class="two-columns">
    <div class="panel patient-info">
        <h3><?php echo htmlspecialchars($patient["name"]); ?></h3>
        <p>ID: P<?php echo $patient["id"]; ?></p>
        <p>Age: <?php echo $patient["age"]; ?></p>
        <p>Phone: <?php echo htmlspecialchars($patient["phone"]); ?></p>
        <p>Department: <?php echo htmlspecialchars($patient["department"]); ?></p>
    </div>

    <div class="panel">
        <h3>Add Medical Record</h3>
        <form method="post">
            <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">
            <input type="date" name="record_date" value="<?php echo date("Y-m-d"); ?>" required>
            <input type="text" name="diagnosis" placeholder="Diagnosis" required>
            <input type="text" name="treatment" placeholder="Treatment" required>
            <input type="text" name="doctor" placeholder="Doctor" required>
            <button name="add_record">Add Record</button>
        </form>
    </div>
</div>

<div class="panel">
<h3>Linked List Timeline of Medical Records</h3>
<?php
$result = $conn->query("SELECT * FROM medical_history WHERE patient_id=$patientId ORDER BY record_date ASC, id ASC");
class HistoryNode {
    public $data;
    public $next = null;
    public function __construct($data) { $this->data = $data; }
}

class MedicalLinkedList {
    public $head = null;
    public function insert($data) {
        $newNode = new HistoryNode($data);
        if ($this->head == null) {
            $this->head = $newNode;
            return;
        }
        $temp = $this->head;
        while ($temp->next != null) $temp = $temp->next;
        $temp->next = $newNode;
    }
}

$list = new MedicalLinkedList();
while ($row = $result->fetch_assoc()) $list->insert($row);

if ($list->head == null) {
    echo "<p>No medical records yet.</p>";
} else {
    echo "<div class='linked-list'>";
    $temp = $list->head;
    while ($temp != null) {
        $row = $temp->data;
        $recordDate = isset($row["record_date"]) && $row["record_date"] ? date("d M Y", strtotime($row["record_date"])) : "No date";
        echo "<div class='node'>"
            . "<div class='record-date'>" . htmlspecialchars($recordDate) . "</div>"
            . "<b>" . htmlspecialchars($row["diagnosis"]) . "</b>"
            . "<br><small>" . htmlspecialchars($row["treatment"]) . "<br>" . htmlspecialchars($row["doctor"]) . "</small>"
            . "<div class='record-actions'><a class='delete-record-btn' href='history.php?id=" . $patientId . "&delete_record=" . $row["id"] . "' onclick='return confirm(\"Delete this record?\")'>Delete</a></div>"
            . "</div>";
        if ($temp->next != null) echo "<span class='arrow'>→</span>";
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