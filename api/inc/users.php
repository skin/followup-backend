<?php

$app->get('/users', 'getUsers'); // Using Get HTTP Method and process getUsers function
$app->post('/users', 'addUser'); // Using Post HTTP Method and process addUser function
$app->get('/users/:userID',    'getUser');
$app->post('/users/:userID/events', 'addUserEvent');
$app->get('/users/:userID/events',    'getUserEvents');


function getUsers() {
	$sql_query = "select userID,email,firstName,lastName FROM user";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$users  = $stmt->fetchAll(PDO::FETCH_OBJ);
		header('Access-Control-Allow-Origin : *');
		$dbCon = null;
		echo '{"users": ' . json_encode($users) . '}';
	}
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function addUser() {
	global $app;
	$req = $app->request(); // Getting parameter with names
	$jsonUser = (array)  json_decode($req->getBody(),true);
	$user = $jsonUser['user'];
	$paramEmail = $user['email']; // Getting parameter with names
	$paramPassword =$user['password']; // Getting parameter with names
	$firstName = $user['firstName']; // Getting parameter with names
	$lastName = $user['lastName']; // Getting parameter with names
	$sql = "INSERT INTO user (email,password,firstName,lastName) VALUES (:email, :password,:firstName,:lastName)";
	try {
		$dbCon = getConnection();
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("email", $paramEmail);
		$stmt->bindParam("password", $paramPassword);
		$stmt->bindParam("firstName", $firstName);
		$stmt->bindParam("lastName", $lastName);
		$stmt->execute();
		$user['id'] = $dbCon->lastInsertId();
		$dbCon = null;
		echo json_encode($user);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

}

function getUser($userID) {
	$sql = "select userID,email,firstName,lastName FROM user WHERE userID=:userID";
	try {
		// Initialize DB connection
		$dbCon = getConnection();
		// Prepare query
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("userID", $userID);
		$stmt->execute();
		$user = $stmt->fetchObject();
		header('Access-Control-Allow-Origin : *');
		$dbCon = null;
		echo json_encode($user);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getUserEvents($userID) {
	$sql = "SELECT eventID,ownerID,title,description,(UNIX_TIMESTAMP(eventDate)*1000) eventDate,eventURL FROM event WHERE ownerID=:userID";
	try {
        // Initialize DB connection
		$dbCon = getConnection();
        // Prepare query
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("userID", $userID);
		$stmt->execute();
        // Get result
		$events = $stmt->fetchAll(PDO::FETCH_OBJ);
        //  Needed for client side framework
		header('Access-Control-Allow-Origin : *');
		$dbCon = null;
        // Create json response
		echo '{"events": ' . json_encode($events) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function addUserEvent($userID) {
	global $app;
	$req = $app->request(); // Getting parameter with names
	$jsonEvent = (array)  json_decode($req->getBody(),true);
	$event = $jsonEvent['event'];
	$paramTitle = $event['title']; // Getting parameter with names
	$paramDescription =$event['description']; // Getting parameter with names
	$paramEventDate =$event['eventDate']/1000;
	$sql = "INSERT INTO event (ownerID,title,description,eventDate) VALUES (:ownerID, :title, :description,:eventDate)";
	try {
		$dbCon = getConnection();
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("ownerID", $userID);
		$stmt->bindParam("title", $paramTitle);
		$stmt->bindParam("description", $paramDescription);
		$stmt->bindParam("eventDate", getDateFromTimestamp($paramEventDate));
		$stmt->execute();
		$eventID = $dbCon->lastInsertId();
		$event['eventID'] = $eventID;
		$eventURL = getEventURL($eventID);
		$qrCodePath = generateEventQrCode($eventID,$eventURL);
		$sql = "UPDATE event set qrCodePath=:qrCodePath,eventURL=:eventURL where eventID=:eventID";
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("eventID", $eventID);
		$stmt->bindParam("qrCodePath", replaceBackslashes($qrCodePath));
		$stmt->bindParam("eventURL", replaceBackslashes($eventURL));
		$event['eventURL'] = $eventURL;
		header('Access-Control-Allow-Origin : *');
		$stmt->execute();
		$dbCon = null;
		echo json_encode($event);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

}

?>