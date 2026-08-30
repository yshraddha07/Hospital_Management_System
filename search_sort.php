<?php
require "config.php";
require "header.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "name";
$order = isset($_GET["order"]) ? $_GET["order"] : "ASC";

$allowedSort = ["name", "age", "id"];

if (!in_array($sort, $allowedSort)) {
    $sort = "name";
}

if ($order != "ASC" && $order != "DESC") {
    $order = "ASC";
}

$raw = $conn->query("SELECT * FROM patients");

$patients = [];

while ($row = $raw->fetch_assoc()) {
    $patients[] = $row;
}


// Linear Search
if ($search != "") {

    $filtered = [];

    foreach ($patients as $patient) {

        if (
            stripos($patient["name"], $search) !== false ||
            stripos($patient["phone"], $search) !== false
        ) {
            $filtered[] = $patient;
        }
    }

    $patients = $filtered;
}


// Bubble Sort
for ($i = 0; $i < count($patients) - 1; $i++) {

    for ($j = 0; $j < count($patients) - $i - 1; $j++) {

        $left = $patients[$j][$sort];
        $right = $patients[$j + 1][$sort];

        $swap = ($order == "ASC")
            ? ($left > $right)
            : ($left < $right);

        if ($swap) {

            $temp = $patients[$j];
            $patients[$j] = $patients[$j + 1];
            $patients[$j + 1] = $temp;
        }
    }
}
?>

<h1>Search & Sort Patients</h1>

<form method="get" class="search-bar">

    <input
        type="text"
        name="search"
        placeholder="Search by name or phone..."
        value="<?php echo htmlspecialchars($search); ?>"
    >

    <select name="sort">

        <option value="name">
            Sort by Name
        </option>

        <option value="age">
            Sort by Age
        </option>

        <option value="id">
            Sort by ID
        </option>

    </select>

    <select name="order">

        <option value="ASC">
            Ascending
        </option>

        <option value="DESC">
            Descending
        </option>

    </select>

    <button>Search</button>

</form>


<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Phone</th>
    </tr>


    <?php foreach ($patients as $row) { ?>

        <tr>

            <td>
                P<?php echo $row["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["name"]); ?>
            </td>

            <td>
                <?php echo $row["age"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["gender"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["phone"]); ?>
            </td>

        </tr>

    <?php } ?>

</table>

<?php require "footer.php"; ?>