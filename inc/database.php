<?php

function getConnection() {
	try {
		$db_username = "root";
		$db_password = "zurich";
		$conn = new PDO('mysql:host=127.0.0.1;dbname=followup', $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getMessage();
	}
	return $conn;
}

?>