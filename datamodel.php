<?php

class LoginService {
	public static function login($userid, $pw) {
		// TODO: implement
		return false;
	}
}


/*
**	User
*/

class User {
	var $userID;						// nick and login
	var $password;
	var $email;
	var $myEvents = array();			// events that this user has created
	var $myQuestions = array();		// questions this user has asked

    function __construct() {
        
    }

	public static function load($userID) {
        // TODO: implement
        //
        $user = User::createExampleUser();
        return $user;

	}

    public static function createExampleUser() {
        $user = new User();
        $user->userID = 'BruceWayne';
        $user->email = 'foo.bar@gmail.com';
        array_push($user->myEvents, Event::createExampleEvent($user) );
        return $user;
    }
}

/*
** Event that users can follow up
*/

class Event {
	var $eventID;				// uid to identify and share the event (needed fo the barcode)

	var $owner;					// owner of the event
	var $title;
	var $description;
	var $questions = array();	// questions posted to this event

	public function load($eventID) {
		// TODO: implement
	}

	public function save() {
		// TODO: implement
		// return $uid;
	}

    public static function createExampleEvent($user) {
        $retval = new Event();
        $retval->title = 'Example Event';
        $retval->description = 'Example description';
        $retval->owner = $user;
    }
}

/*
** Question posted to the event
*/

class Question {
	var $questionID;
	
	var $ownerUser;				// user who posted the question
	var $question;				// question
	var $answer;				// answer posted by the creator of the event
	var $parentEvent;			// event that this question belongs to

	public function load($questionID) {
		// TODO: implement
	}

	public function save() {
		// TODO: implement
		// return $uid;
	}
}

/*
class Example {
	function hello() {
		echo 'Hello world';
	}
}
*/
?>