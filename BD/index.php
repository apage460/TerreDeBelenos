<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/user.class.php');



// ═════ SESSION HANDLING ═════
session_start();

if(SITE_CLOSED) { session_destroy(); 
		  header('Location: closed.php');
		}



// ═════ AUTHENTICATED ═════
if( isset($_SESSION['authenticated']) ) {
	header('Location: main.php');
	exit;
}



// ═════ ANONYMOUS ═════
else {
	header('Location: login.php');
	exit;
}

?>