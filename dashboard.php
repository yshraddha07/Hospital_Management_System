<?php
require "config.php";
require "header.php";

$patients = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()["total"];
$doctors = $conn->query("SELECT COUNT(*) AS total FROM doctors")->fetch_assoc()["total"];
$appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()["total"];
$emergency = $conn->query("SELECT COUNT(*) AS total FROM emergency_queue WHERE status='Waiting'")->fetch_assoc()["total"];
?>
<h1>Dashboard</h1>
<p class="subtitle">Welcome back! Here is a quick overview of the hospital.</p>

<div class="cards">
    <div class="card"><span>👤 Patients</span><h2><?php echo $patients; ?></h2></div>
    <div class="card"><span>👨‍⚕️ Doctors</span><h2><?php echo $doctors; ?></h2></div>
    <div class="card"><span>📅 Appointments</span><h2><?php echo $appointments; ?></h2></div>
    <div class="card"><span>🚨 Emergency</span><h2><?php echo $emergency; ?></h2></div>
</div>

<h2>Quick Actions</h2>
<div class="quick-actions">
    <a href="register_patient.php">Register Patient</a>
    <a href="doctors.php">Add Doctor</a>
    <a href="appointments.php">New Appointment</a>
    <a href="emergency.php">Emergency Entry</a>
</div>

<div class="two-columns">
    <div class="panel">
        <h3>OPD Queue</h3>
        <?php
        $q = $conn->query("SELECT patient_name FROM opd_queue WHERE status='Waiting' ORDER BY id LIMIT 5");
        if ($q->num_rows == 0) echo "<p>No patients waiting.</p>";
        while ($row = $q->fetch_assoc()) echo "<p>• " . htmlspecialchars($row["patient_name"]) . "</p>";
        ?>
    </div>
    <div class="panel">
        <h3>Emergency Priority Queue</h3>
        <?php
        $q = $conn->query("SELECT patient_name, priority FROM emergency_queue WHERE status='Waiting' ORDER BY priority ASC, id ASC LIMIT 5");
        if ($q->num_rows == 0) echo "<p>No emergency cases.</p>";
        while ($row = $q->fetch_assoc()) echo "<p>• " . htmlspecialchars($row["patient_name"]) . " <b>(" . htmlspecialchars($row["priority"]) . ")</b></p>";
        ?>
    </div>
</div>
<?php require "footer.php"; ?>