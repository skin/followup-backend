<?php
	include 'inc/includes.php';
	include 'datamodel.php';


	//phpinfo();
?>

<html>
	<body>
        <?php
            $user = User::createExampleUser();
            echo 'User: ' . $user->userID . '<br>';

            echo 'Events';
            foreach ($user->myEvents as $event) {
                echo $event->title . '<br>';
            }
        ?>
        Follow up
	</body>
</html>


