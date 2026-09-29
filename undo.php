<?php
require "config.php";

$message = "";

if (!isset($_SESSION["undo_stack"])) $_SESSION["undo_stack"] = [];
if (!isset($_SESSION["redo_stack"])) $_SESSION["redo_stack"] = [];

if (isset($_GET["undo"]) && !empty($_SESSION["undo_stack"])) {
    $last = array_pop($_SESSION["undo_stack"]);

    if ($last["type"] == "add_patient") {
        $id = (int)($last["id"] ?? 0);
        if ($id > 0) {
            $conn->query("DELETE FROM patients WHERE id=$id");
        }
        $_SESSION["redo_stack"][] = $last;
        $message = "Last patient addition was undone.";
    } elseif ($last["type"] == "delete_patient" && isset($last["patient"])) {
        $patient = $last["patient"];
        $id = (int)($patient["id"] ?? $last["id"]);
        $name = $patient["name"] ?? $last["name"];
        $age = (int)($patient["age"] ?? 0);
        $gender = $patient["gender"] ?? "";
        $phone = $patient["phone"] ?? "";
        $address = isset($patient["address"]) ? $patient["address"] : "";
        $bloodGroup = $patient["blood_group"] ?? "";
        $department = $patient["department"] ?? "";

        $stmt = $conn->prepare("INSERT INTO patients (id, name, age, gender, phone, address, blood_group, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisssss", $id, $name, $age, $gender, $phone, $address, $bloodGroup, $department);
        $stmt->execute();
        $_SESSION["redo_stack"][] = $last;
        $message = "Last patient deletion was undone.";
    } else {
        $message = "Undo record could not be restored.";
    }
} elseif (isset($_GET["redo"]) && !empty($_SESSION["redo_stack"])) {
    $last = array_pop($_SESSION["redo_stack"]);

    if ($last["type"] == "add_patient") {
        $patient = isset($last["patient"]) ? $last["patient"] : [
            "id" => $last["id"],
            "name" => $last["name"],
            "age" => 0,
            "gender" => "",
            "phone" => "",
            "address" => "",
            "blood_group" => "",
            "department" => ""
        ];

        $id = (int)($patient["id"] ?? $last["id"]);
        $name = $patient["name"] ?? $last["name"];
        $age = (int)($patient["age"] ?? 0);
        $gender = $patient["gender"] ?? "";
        $phone = $patient["phone"] ?? "";
        $address = isset($patient["address"]) ? $patient["address"] : "";
        $bloodGroup = $patient["blood_group"] ?? "";
        $department = $patient["department"] ?? "";

        $stmt = $conn->prepare("INSERT INTO patients (id, name, age, gender, phone, address, blood_group, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisssss", $id, $name, $age, $gender, $phone, $address, $bloodGroup, $department);
        $stmt->execute();
        $_SESSION["undo_stack"][] = $last;
        $message = "Last patient addition was redone.";
    } elseif ($last["type"] == "delete_patient") {
        $id = (int)($last["id"] ?? 0);
        if ($id > 0) {
            $conn->query("DELETE FROM patients WHERE id=$id");
        }
        $_SESSION["undo_stack"][] = $last;
        $message = "Last patient deletion was redone.";
    } else {
        $message = "Redo record could not be restored.";
    }
}

require "header.php";
$undoStack = isset($_SESSION["undo_stack"]) ? $_SESSION["undo_stack"] : [];
$redoStack = isset($_SESSION["redo_stack"]) ? $_SESSION["redo_stack"] : [];
?>
<h1>Undo / Redo Operations <span class="tag">Stack</span></h1>
<p class="subtitle">Last In, First Out concept</p>
<?php if ($message != "") echo "<p class='success'>$message</p>"; ?>
<div class="two-columns">
    <div class="panel">
        <h3>Undo Stack</h3>
        <a class="button" href="undo.php?undo=1">Undo Last Operation</a>
        <div class="stack-box">
        <?php
        if (empty($undoStack)) {
            echo "<p>Stack is empty.</p>";
        } else {
            for ($i=count($undoStack)-1; $i>=0; $i--) {
                $itemName = isset($undoStack[$i]["patient"]["name"]) ? $undoStack[$i]["patient"]["name"] : ($undoStack[$i]["name"] ?? "Unknown");
                echo "<div class='stack-item'>TOP → ".htmlspecialchars($undoStack[$i]["type"])." : ".htmlspecialchars($itemName)."</div>";
            }
        }
        ?>
        </div>
    </div>

    <div class="panel">
        <h3>Redo Stack</h3>
        <a class="button secondary-link" href="undo.php?redo=1">Redo Last Operation</a>
        <div class="stack-box">
        <?php
        if (empty($redoStack)) {
            echo "<p>Redo stack is empty.</p>";
        } else {
            for ($i=count($redoStack)-1; $i>=0; $i--) {
                $itemName = isset($redoStack[$i]["patient"]["name"]) ? $redoStack[$i]["patient"]["name"] : ($redoStack[$i]["name"] ?? "Unknown");
                echo "<div class='stack-item'>TOP → ".htmlspecialchars($redoStack[$i]["type"])." : ".htmlspecialchars($itemName)."</div>";
            }
        }
        ?>
        </div>
    </div>
</div>
<?php require "footer.php"; ?>