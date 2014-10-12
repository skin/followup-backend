<?php
	include 'inc/includes.php';

	getConnection();
	
	$app = new \Slim\Slim();
	
	include 'inc/users.php';
	include 'inc/events.php';
	include 'inc/questions.php';
	
	$app->run();
	
	
?>
