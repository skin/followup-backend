<?php

include "../phpqrcode/qrlib.php";

$app->get('/events/:eventID', 'getEvent');
$app->get('/events/:eventID/qrCode', 'getQrCodeEvent');
$app->get('/events/:eventID/questions',	'getEventQuestions');


function getQrCodeEvent($eventID) {
	$sql = "select qrCodePath FROM event WHERE eventID=:eventID";
	try {
		$dbCon = getConnection();
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("eventID", $eventID);
		$stmt->execute();
		$event = $stmt->fetchObject();
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	header('Content-Type: image/png');
	imagepng(imagecreatefrompng($event->qrCodePath));
}

function getEvent($eventID) {
	$sql = "select eventID,ownerID,title,description,(UNIX_TIMESTAMP(eventDate)*1000) eventDate,eventURL FROM event WHERE eventID=:eventID";
	try {
		$dbCon = getConnection();
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("eventID", $eventID);
		$stmt->execute();
		$event = $stmt->fetchObject();
		$dbCon = null;
		header('Access-Control-Allow-Origin : *');
		echo json_encode($event, JSON_UNESCAPED_SLASHES);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getEventQuestions($eventID) {
	$sql = "SELECT questionID,ownerID,eventID,question,(UNIX_TIMESTAMP(creationDate)*1000) creationDate,answer,(UNIX_TIMESTAMP(answerDate)*1000)  answerDate FROM question WHERE eventID=:eventID";
	try {
		$dbCon = getConnection();
		$stmt = $dbCon->prepare($sql);
		$stmt->bindParam("eventID", $eventID);
		$stmt->execute();
		$questions  = $stmt->fetchAll(PDO::FETCH_OBJ);
		header('Access-Control-Allow-Origin : *');
		$dbCon = null;
		echo '{"questions": ' . json_encode($questions) . '}';
	}
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}



?>