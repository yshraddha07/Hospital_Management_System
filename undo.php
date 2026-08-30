<?php
require "config.php";

$message = "";

if (isset($_GET["undo"]) && !empty($_SESSION["undo_stack"])) {

    $last = array_pop($_SESSION["undo_stack"]);

    if ($last["type"] == "add_patient") {

        $id = (int)$last["id"];

        $conn->query(
            "DELETE FROM patients WHERE id=$id"
        );

        $message = "Last patient addition was undone.";

    } else {

        $message = "Last operation was removed from the stack.";
    }
}

require "header.php";

$stack = isset($_SESSION["undo_stack"])
    ? $_SESSION["undo_stack"]
    : [];
?>

<h1>Undo Operations</h1>

<?php
if ($message != "") {
    echo "<p>$message</p>";
}
?>

<a class="button" href="undo.php?undo=1">
    Undo Last Operation
</a>


<div class="stack-box">

<?php

if (empty($stack)) {

    echo "<p>Stack is empty.</p>";

} else {

    for ($i = count($stack) - 1; $i >= 0; $i--) {

        echo "<div class='stack-item'>
                TOP → " .
                htmlspecialchars($stack[$i]["type"]) .
                " : " .
                htmlspecialchars($stack[$i]["name"]) .
             "</div>";
    }
}

?>

</div>

<?php require "footer.php"; ?>