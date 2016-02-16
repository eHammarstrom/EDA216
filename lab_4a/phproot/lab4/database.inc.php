<?php
/*
 * Class Database: interface to the movie database from PHP.
 *
 * You must:
 *
 * 1) Change the function userExists so the SQL query is appropriate for your tables.
 * 2) Write more functions.
 *
 */
class Database {
	private $host;
	private $userName;
	private $password;
	private $database;
	private $conn;

	/**
	 * Constructs a database object for the specified user.
	 */
	public function __construct($host, $userName, $password, $database) {
		$this->host = $host;
		$this->userName = $userName;
		$this->password = $password;
		$this->database = $database;
	}

	/** 
	 * Opens a connection to the database, using the earlier specified user
	 * name and password.
	 *
	 * @return true if the connection succeeded, false if the connection 
	 * couldn't be opened or the supplied user name and password were not 
	 * recognized.
	 */
	public function openConnection() {
		try {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", 
				$this->userName,  $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$error = "Connection error: " . $e->getMessage();
			print $error . "<p>";
			unset($this->conn);
			return false;
		}
		return true;
	}

	/**
	 * Closes the connection to the database.
	 */
	public function closeConnection() {
		$this->conn = null;
		unset($this->conn);
	}

	/**
	 * Checks if the connection to the database has been established.
	 *
	 * @return true if the connection has been established
	 */
	public function isConnected() {
		return isset($this->conn);
	}

	/**
	 * Execute a database query (select).
	 *
	 * @param $query The query string (SQL), with ? placeholders for parameters
	 * @param $param Array with parameters 
	 * @return The result set
	 */
	private function executeQuery($query, $param = null) {
		try {
			$stmt = $this->conn->prepare($query);
			$stmt->execute($param);
			$result = $stmt->fetchAll();
		} catch (PDOException $e) {
			$error = "*** Internal error: " . $e->getMessage() . "<p>" . $query;
			die($error);
		}
		return $result;
	}

	/**
	 * Execute a database update (insert/delete/update).
	 *
	 * @param $query The query string (SQL), with ? placeholders for parameters
	 * @param $param Array with parameters 
	 * @return The number of affected rows
	 */
	private function reservation($query, $date, $movieName, $userId) {
		try {
			$tActionStart = $this->conn->prepare("START TRANSACTION");
			$tActionStart->execute();

			$stmt = $this->conn->prepare($query);
			$stmt->execute(array($date, $movieName, $userId));

			$lockQuery = "SELECT seatsLeft"
				. " FROM performances"
				. " WHERE movieName = ? AND date = ? FOR UPDATE";
			$lock = $this->conn->prepare($lockQuery);
			$lock->execute(array($movieName, $date));
			$seats = $lock->fetchAll();

			$lockUpdateQuery = "UPDATE performances"
				. " SET seatsLeft = seatsLeft - 1"
				. " WHERE movieName = ? AND date = ?";
			$updateSeats = $this->conn->prepare($lockUpdateQuery);
			$updateSeats->execute(array($movieName, $date));

			if ($seats[0]['seatsLeft'] > 0) {
				$tActionCommit = $this->conn->prepare("COMMIT");
				$tActionCommit->execute();
				$result = true;
			} else {
				$tActionRollback = $this->conn->prepare("ROLLBACK");
				$tActionRollback->execute();
				$result = false;
			}

			return $result;

		} catch (PDOException $e) {
			$error = "*** Internal error: " . $e->getMessage() . "<p>" . $query . "</p>";
			die($error);
		}
	}

	/**
	 * Check if a user with the specified user id exists in the database.
	 * Queries the Users database table.
	 *
	 * @param userId The user id 
	 * @return true if the user exists, false otherwise.
	 */
	public function userExists($userId) {
		$sql = "SELECT username FROM users WHERE username = ?";
		$result = $this->executeQuery($sql, array($userId));
		return count($result) == 1; 
	}

	/*
	 * *** Add functions ***
	 */

	public function getMovieNames() {
		$sql = "SELECT name FROM movies";
		$result = $this->executeQuery($sql);
		return $result;
	}

	public function getMovieDates($movieName) {
		$sql = "SELECT date FROM performances WHERE moviename = ?";
		$result = $this->executeQuery($sql, array($movieName));
		return $result;
	}

	public function getPerformance($movieName, $date) {
		$sql = "SELECT * FROM performances WHERE moviename = ? AND date = ?";
		$result = $this->executeQuery($sql, array($movieName, $date));
		return $result;
	}

	public function makeReservation($movieName, $date, $userId) {
		$sql = "INSERT INTO reservations(date, movieName, userName)"
			. " VALUES (?, ?, ?)";
		$result = $this->reservation($sql, $date, $movieName, $userId);
		return $result;
	}
	
	public function getLastReservation($movieName, $date, $userId) {
		$sql = "SELECT resnbr FROM reservations"
			. " WHERE movieName = ? AND date = ? AND username = ?"
			. " ORDER BY resnbr DESC";
		$result = $this->executeQuery($sql, array($movieName, $date, $userId));
		return $result[0]['resnbr'];
	}
}
?>
