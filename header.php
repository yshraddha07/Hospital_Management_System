<?php
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$current = basename($_SERVER["PHP_SELF"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>HMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="sidebar">
    <h2>💚 HMS</h2>
    <a class="<?php echo $current == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">🏠 Dashboard</a>
    <a class="<?php echo in_array($current, ['patients.php','register_patient.php','search_sort.php','history.php']) ? 'active' : ''; ?>" href="patients.php">👤 Patients</a>
    <a href="register_patient.php">➕ Register Patient</a>
    <a href="search_sort.php">🔍 Search & Sort</a>
    <a href="history.php">📋 Medical History</a>
    <a class="<?php echo $current == 'doctors.php' ? 'active' : ''; ?>" href="doctors.php">👨‍⚕️ Doctors</a>
    <a class="<?php echo $current == 'appointments.php' ? 'active' : ''; ?>" href="appointments.php">📅 Appointments</a>
    <a class="<?php echo $current == 'opd.php' ? 'active' : ''; ?>" href="opd.php">🏥 OPD Queue</a>
    <a class="<?php echo $current == 'emergency.php' ? 'active' : ''; ?>" href="emergency.php">🚨 Emergency</a>
    <a class="<?php echo $current == 'beds.php' ? 'active' : ''; ?>" href="beds.php">🛏 Beds & Rooms</a>
    <a class="<?php echo $current == 'billing.php' ? 'active' : ''; ?>" href="billing.php">💰 Billing</a>
    <a class="<?php echo $current == 'undo.php' ? 'active' : ''; ?>" href="undo.php">↩️ Undo Operations</a>
    <a class="<?php echo $current == 'departments.php' ? 'active' : ''; ?>" href="departments.php">🌳 Departments</a>
    <a class="<?php echo $current == 'dsa_lab.php' ? 'active' : ''; ?>" href="dsa_lab.php">🧠 DSA Lab</a>
    <a class="logout" href="logout.php">🚪 Logout</a>
</div>
<div class="main">
    <div class="topbar">
        <span>Hospital Management System</span>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION["user"]); ?></span>
    </div>
