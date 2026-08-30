<?php
require "config.php";

$message = "";

if (isset($_POST["save"])) {
    $name = trim($_POST["name"]);
    $age = (int)$_POST["age"];
    $gender = $_POST["gender"];
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $blood_group = $_POST["blood_group"];
    $department = $_POST["department"];

    $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, phone, address, blood_group, department) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssss", $name, $age, $gender, $phone, $address, $blood_group, $department);

    if ($stmt->execute()) {
        if (!isset($_SESSION["undo_stack"])) $_SESSION["undo_stack"] = [];
        $_SESSION["undo_stack"][] = ["type" => "add_patient", "id" => $conn->insert_id, "name" => $name];
        $message = "Patient registered successfully!";
    } else {
        $message = "Something went wrong.";
    }
}

require "header.php";
?>
<div class="page-title">
    <div><h1>Register Patient</h1><p class="subtitle">Enter patient details</p></div>
    <a class="text-link" href="patients.php">← Back to Patients</a>
</div>

<?php if ($message != "") echo "<p class='success'>$message</p>"; ?>

<form method="post" class="form-card">
    <div class="form-grid">
        <div><label>Full Name</label><input type="text" name="name" required></div>
        <div><label>Age</label><input type="number" name="age" required></div>
        <div><label>Gender</label>
            <select name="gender"><option>Male</option><option>Female</option><option>Other</option></select>
        </div>
        <div><label>Phone Number</label><input type="text" name="phone" required></div>
        <div><label>Blood Group</label>
            <select name="blood_group"><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>O+</option><option>O-</option><option>AB+</option><option>AB-</option></select>
        </div>
        <div><label>Department</label>
            <select name="department"><option>Cardiology</option><option>Orthopedics</option><option>Neurology</option><option>Pediatrics</option><option>General Medicine</option></select>
        </div>
        <div class="full"><label>Address</label><textarea name="address"></textarea></div>
    </div>
    <button type="submit" name="save">Save Patient</button>
    <button type="reset" class="secondary">Reset</button>
</form>
<?php require "footer.php"; ?>