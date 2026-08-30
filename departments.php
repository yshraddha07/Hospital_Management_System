<?php
require "config.php";
require "header.php";

$result = $conn->query(
    "SELECT * FROM departments ORDER BY id"
);
?>

<h1>Departments</h1>

<div class="tree">

    <div class="tree-root">
        🏥 Hospital
    </div>

    <div class="tree-children">

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div class="tree-node">

                <b>
                    <?php echo htmlspecialchars($row["name"]); ?>
                </b>

                <small>
                    HOD:
                    <?php echo htmlspecialchars($row["hod"]); ?>
                </small>

            </div>

        <?php } ?>

    </div>

</div>

<?php require "footer.php"; ?>