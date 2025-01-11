<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/user.class.php');

session_start();



// ═════ AUTHENTICATED ═════
if( isset($_SESSION['authenticated']) ) {
	$_user = $_SESSION['authenticated'];

	header('Location: main.php');
	exit;

}



// ═════ ANONYMOUS ═════
else {
	header('Location: ../login.php');
	exit;
}

?>