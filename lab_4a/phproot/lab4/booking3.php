<?php
require_once('database.inc.php');

session_start();
$db = $_SESSION['db'];
$userId = $_SESSION['userId'];
$movieName = $_SESSION['movieName'];
$date = $_REQUEST['date'];
$_SESSION['date'] = $date;
$db->openConnection();
$theater = $db->getPerformance($movieName, $date);
$db->closeConnection();
$_SESSION['theater'] = $theater;
?>

<html>
<head>
	<title>Booking 3</title>
</head>
<body>
	<h1>Booking 3</h1>
	
	<!-- <?php print var_dump($theater) ?> -->

	<p> Current user: <?php print $userId ?> </p>
	<br>
	<p> Data for selected performance: </p>
	<br>
	<p> Movie: <?php print $theater[0]['moviename'] ?> </p>
	<p> Date: <?php print $theater[0]['date'] ?> </p>
	<p> Theater: <?php print $theater[0]['theatername'] ?> </p>
	<p> Free seats: <?php print $theater[0]['seatsleft'] ?> </p>

	<form method="post" action="booking4.php">
		<input type="submit" value="Book ticket">
	</form>
</body>
</html>
