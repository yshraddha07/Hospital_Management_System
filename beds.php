<?php
require "config.php";
require "header.php";

$result = $conn->query("SELECT * FROM beds ORDER BY room_no");
$total = $conn->query("SELECT COUNT(*) total FROM beds")->fetch_assoc()["total"];
$occupied = $conn->query("SELECT COUNT(*) total FROM beds WHERE status='Occupied'")->fetch_assoc()["total"];
$available = $total - $occupied;
?>
<h1>Beds & Rooms</h1>
<div class="cards">
<div class="card"><span>Total Beds</span><h2><?php echo $total; ?></h2></div>
<div class="card"><span>Occupied</span><h2><?php echo $occupied; ?></h2></div>
<div class="card"><span>Available</span><h2><?php echo $available; ?></h2></div>
</div>
<table><tr><th>Room</th><th>Type</th><th>Status</th></tr>
<?php while ($row=$result->fetch_assoc()) { ?><tr><td><?php echo htmlspecialchars($row["room_no"]); ?></td><td><?php echo htmlspecialchars($row["room_type"]); ?></td><td><?php echo htmlspecialchars($row["status"]); ?></td></tr><?php } ?>
</table>
<?php require "footer.php"; ?>