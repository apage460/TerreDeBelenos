<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/user.class.php');

session_start();



// ═════ AUTHENTICATED ═════
if( isset($_SESSION['authenticated']) ) {
	$_user = $_SESSION['authenticated'];

	if( $_user->IsScriptor() ){
		header('Location: main.php');
		exit;
	}
	else {
		header('Location: ../login.php');
		exit;
	}

}



// ═════ ANONYMOUS ═════
else {
	header('Location: ../login.php');
	exit;
}

?>