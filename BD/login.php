<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/user.class.php');
include_once('services/authenticator.class.php');
include_once('controllers/authenticator-controller.class.php');
include_once('views/authenticator_ui.class.php');



// ═════ INITIALIZATION ═════
$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_auth = new Authenticator($_db);

$_redirect = null;
$_message = ""; $_msg_class = "";

session_start();
session_destroy();
session_unset();
session_start();



// ═════ CONTROL ═════
$_auth_ctrl = new AuthenticatorController($_auth);

if( isset($_POST['action']) ) {
	if( $_POST['action'] == 'login') { 
		if( $_auth_ctrl->Login() ) { 
			$_SESSION['authenticated'] = $_auth->GetUser();
			$_SESSION['last_time_renewed'] = time();
			$_redirect = "main.php"; 
		}
		else { $_message = "L'authentification a échoué..."; $_msg_class = "error"; }
	}
	else if( $_POST['action'] == 'create_user') { 
		$_redirect = "new-user.php";
	}
}



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');


	
// ═════ BODY ═════
echo '<body>';

# Interface wrapper
echo '<DIV id="wrapper">';



// ═════ TITLE ═════
echo '<DIV id="header">';
	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Base de données bélénoise</div>';
echo '</DIV>';

echo '<DIV id="content">';



// ═════ NAVIGATION ═════
require_once('includes/login_menu.php');



// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
$_auth_ui = new AuthenticatorUI( $_auth );

echo '<DIV id="main">';

echo '<span>Vous avez déjà un compte utilisateur pour ce site? Authentifiez-vous ici : </span>';
$_auth_ui->Display();


echo '<hr width=25%>';

echo '<div>';
echo '<span>Vous n’avez ni compte ni personnages? Inscrivez-vous ici :</span>';
	echo '<form method="post">';
	echo '<button type="submit" name="action" value="create_user" class="submit-button">Créer un compte</button>';
	echo '</form>';
echo '</div>';

echo '</DIV>';



// ═════ FOOTER ═════
echo '</div>';

echo '</div>';
echo '</body>';
echo '</html>';

?>