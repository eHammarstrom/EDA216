<?php
require_once('database.inc.php');

session_start();
$db = $_SESSION['db'];
$userId = $_SESSION['userId'];
$theater = $_SESSION['theater'];

$db->openConnection();
$booking = $db->makeReservation($theater[0]['moviename'], 
	$theater[0]['date'], $userId);
$bookingNumber = $db->getLastReservation($theater[0]['moviename'], 
	$theater[0]['date'], $userId);
$db->closeConnection();
?>

<html>
<head>
	<title>Booking 4</title>
</head>
<body>
	<h1>Booking 4</h1>

<!-- <?php print var_dump($theater); ?> -->
	
<?php
if ($booking) {
	print "<p> One ticket booked. Booking number: " . $bookingNumber . "</p>";
} else {
	print "<p> Error, ticket was not booked. Please try again. </p>";
}
?>
	<form method="post" action="booking1.php">
		<input type="submit" value="Book ticket">
	</form>
</body>
</html>
