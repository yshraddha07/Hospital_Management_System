 <?php
require "config.php";
require "header.php";

function buildDepartmentPayload($conn, $selectedDepartments) {
    $data = [];
    foreach ($selectedDepartments as $deptName) {
        $dept = $conn->query("SELECT * FROM departments WHERE name = '" . $conn->real_escape_string($deptName) . "' LIMIT 1")->fetch_assoc();
        if (!$dept) continue;

        $patients = $conn->query("SELECT id, name, department FROM patients WHERE department = '" . $conn->real_escape_string($deptName) . "'")->fetch_all(MYSQLI_ASSOC);

        $data[] = [
            "name" => $dept["name"],
            "hod" => $dept["hod"],
            "patients" => $patients
        ];
    }

    return $data;
}

$allDepartments = $conn->query("SELECT * FROM departments ORDER BY id");
$departmentNames = [];
while ($row = $allDepartments->fetch_assoc()) {
    $departmentNames[] = $row["name"];
}

$selected = [];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["departments"])) {
    $selected = array_values(array_filter(array_map("trim", (array)$_POST["departments"])));
    if (count($selected) > 2) {
        $error = "Please select only two departments for BST visualization.";
    }
}

if (isset($_GET["tree_json"])) {
    header("Content-Type: application/json");
    $selectedJson = isset($_GET["departments"]) ? explode(",", $_GET["departments"]) : [];
    echo json_encode(buildDepartmentPayload($conn, $selectedJson));
    exit;
}

$treePayload = buildDepartmentPayload($conn, $selected);
if (count($selected) === 2 && empty($error)) {
    $treePayloadJson = json_encode($treePayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
} else {
    $treePayloadJson = "[]";
}
?>
<h1>Departments <span class="tag">BST</span></h1>

<?php if ($error != "") { ?>
    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
<?php } ?>

<form method="post" class="department-form">
    <h3>Department Selection</h3>
    <div class="department-checkboxes">
        <?php foreach ($departmentNames as $name) { ?>
            <label class="department-option">
                <input type="checkbox" name="departments[]" value="<?php echo htmlspecialchars($name); ?>" <?php echo in_array($name, $selected) ? "checked" : ""; ?>>
                <span><?php echo htmlspecialchars($name); ?></span>
            </label>
        <?php } ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="generate-button">Generate Tree</button>
    </div>
</form>

<?php if (count($selected) === 2 && empty($error)) { ?>
    <div class="tree-panel panel">
        <h3>Tree Structure</h3>
        <div id="bst-tree" class="bst-tree" data-selected="<?php echo htmlspecialchars(implode(",", $selected)); ?>"></div>

        <div class="traversal-buttons">
            <button type="button" class="traversal-btn" data-order="preorder">Preorder</button>
            <button type="button" class="traversal-btn" data-order="inorder">Inorder</button>
            <button type="button" class="traversal-btn" data-order="postorder">Postorder</button>
        </div>

        <div class="traversal-output">
            <h4>Traversal Result</h4>
            <div id="traversal-result">Select a traversal to view the order.</div>
            <p id="traversal-count"></p>
        </div>
    </div>
<?php } ?>

<script>
    const treeData = <?php echo $treePayloadJson; ?>;
</script>
<?php require "footer.php"; ?>