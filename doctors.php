<?php
require "config.php";

// Add doctor
if (isset($_POST["add_doctor"])) {
    $name = trim($_POST["name"]);
    $specialization = trim($_POST["specialization"]);
    $phone = trim($_POST["phone"]);
    $status = $_POST["status"];

    $stmt = $conn->prepare(
        "INSERT INTO doctors (name, specialization, phone, status) VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $name, $specialization, $phone, $status);
    $stmt->execute();
}


// Change doctor status
if (isset($_POST["change_status"])) {
    $doctorId = (int)$_POST["doctor_id"];
    $newStatus = $_POST["new_status"];

    $stmt = $conn->prepare(
        "UPDATE doctors SET status = ? WHERE id = ?"
    );

    $stmt->bind_param("si", $newStatus, $doctorId);
    $stmt->execute();
}


require "header.php";

// Get all doctors
$result = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
?>

<div class="page-title">
    <div>
        <h1>Doctors</h1>
        <p class="subtitle">Manage hospital doctors and their availability</p>
    </div>
</div>


<!-- Add Doctor Form -->
<form method="post" class="inline-form">

    <input
        name="name"
        placeholder="Doctor name"
        required
    >

    <input
        name="specialization"
        placeholder="Specialization"
        required
    >

    <input
        name="phone"
        placeholder="Phone"
        required
    >

    <select name="status">
        <option value="Available">Available</option>
        <option value="Unavailable">Unavailable</option>
    </select>

    <button type="submit" name="add_doctor">
        + Add Doctor
    </button>

</form>


<!-- Doctors Table -->
<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Specialization</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Action</th>
    </tr>


    <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>

            <td>
                D<?php echo $row["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["specialization"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["phone"]); ?>
            </td>

            <td>
                <span class="status">
                    <?php echo htmlspecialchars($row["status"]); ?>
                </span>
            </td>

            <td>

                <form method="post">

                    <input
                        type="hidden"
                        name="doctor_id"
                        value="<?php echo $row["id"]; ?>"
                    >

                    <?php if ($row["status"] == "Available") { ?>

                        <input
                            type="hidden"
                            name="new_status"
                            value="Unavailable"
                        >

                        <button
                            type="submit"
                            name="change_status"
                        >
                            Mark Unavailable
                        </button>

                    <?php } else { ?>

                        <input
                            type="hidden"
                            name="new_status"
                            value="Available"
                        >

                        <button
                            type="submit"
                            name="change_status"
                        >
                            Mark Available
                        </button>

                    <?php } ?>

                </form>

            </td>

        </tr>

    <?php } ?>

</table>

<?php require "footer.php"; ?>