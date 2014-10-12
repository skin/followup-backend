<?php

$app->get('/questions/test', 'test');
$app->get('/questions/:questionID', 'getQuestion');

$app->post('/events/:eventid/questions', 'addQuestion');
$app->post('/questions/:questionID/answer', 'addAnswer');

function test() {
    echo 'TEST';
}

/*
 * Add question.
 */

function addQuestion($eventID) {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $jsonMessage = (array)  json_decode($req->getBody(),true);

    $question = $jsonMessage['question'];
    $paramQuestion = $question['question'];
    $paramUserID = $question['ownerID'];
    $paramEventID = $eventID;

    $sql = "INSERT INTO question (eventID, ownerID, question,creationDate) VALUES (:eventID,:userID,:question,FROM_UNIXTIME(:creationDate))";
    try {
    	$creationDate = time();
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $stmt->bindParam("eventID", $eventID);
        $stmt->bindParam("userID", $paramUserID);
        $stmt->bindParam("question", $paramQuestion);
        $stmt->bindParam("creationDate", $creationDate);
        $stmt->execute();
        $question['questionID'] = $dbCon->lastInsertId();
        $question['creationDate'] = $creationDate;
        $dbCon = null;
        header('Access-Control-Allow-Origin : *');
        echo '{"question": ' . json_encode($question) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

/*
 * Add answer to a question
 */
function addAnswer($questionID) {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $jsonMessage = (array)  json_decode($req->getBody(),true);

    $answer = $jsonMessage['answer'];
    $paramAnswer = $answer['answer'];
    //$paramUserID = $answer['ownerID'];

    
        $sql = "UPDATE question SET answer=:answer, answerDate=FROM_UNIXTIME(:answerDate) WHERE questionID = :questionID";
    try {
    	$answerDate = time();
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        //$stmt->bindParam("eventID", $paramEventID);
        //$stmt->bindParam("userID", $paramUserID);
        $stmt->bindParam("answer", $paramAnswer);
        $stmt->bindParam("answerDate", $answerDate);
        $stmt->bindParam("questionID", $questionID);
        $stmt->execute();
        //$answer['answerID'] = $dbCon->lastInsertId();
        $dbCon = null;
        echo '{"answer": ' . json_encode($answer) . '}';
        header('Access-Control-Allow-Origin : *');
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
/*
 ** Query for existing question
 */
function getQuestion($questionID) {
    $sql_query = "select question,ownerID,(UNIX_TIMESTAMP(creationDate)*1000) creationDate,answer,(UNIX_TIMESTAMP(answerDate)*1000) answerDate FROM question WHERE questionID=:qID";

    try {
        // Initialize DB connection
        $dbCon = getConnection();
        // Prepare query
        $stmt = $dbCon->prepare($sql_query);
        $stmt->bindParam("qID", $questionID);
        $stmt->execute();
        // Get result
        $question = $stmt->fetchObject();
		$dbCon = null;
		header('Access-Control-Allow-Origin : *');
		echo json_encode($question, JSON_UNESCAPED_SLASHES);
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

?>