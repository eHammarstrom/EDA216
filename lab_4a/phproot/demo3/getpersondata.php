<?php
$host = "puccini.cs.lth.se";
$username = "db82";
$password = "youtwo";
$database = "db82";

$conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("SELECT * FROM PersonPhones ORDER BY name");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
	<head>
		<title>PHP PDO Test</title>
	</head>
	<body>
		<h2>Data from the PersonPhones table</h2>

		<table border=1>
			<tr>
				<th>Name</th>
				<th>Phone</th>
			</tr>
<?php
$rowcount = 0;
foreach ($result as $row) {
	$rowcount++;
	print "<tr>";
	foreach ($row as $attr) {
		print "<td>";
		print htmlentities($attr);
		print "</td>";
	}
	print "</tr>";
}

?>
		</table>

		<p>A total of <?php print "$rowcount"; ?> rows.</p>

	</body>
</html>
