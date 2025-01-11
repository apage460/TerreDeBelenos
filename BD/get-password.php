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

$_auth_ctrl = new AuthenticatorController($_auth);

$_redirect = null;
$_message = ""; $_msg_class = "";
$_inputmail = "";
	if( isset($_POST['mail']) ) { $_inputmail = $_POST['mail']; }

session_start();



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {
	if( $_POST['action'] == 'get_password') 	{ if( $_auth_ctrl->SendRecoveryMail() ) { $_message = "Le courriel de récupération a été envoyé avec succès!"; $_msg_class = "success"; }
						  	  else { $_message = $_auth_ctrl->Error; $_msg_class = "error"; } }
}



// ═════ DATA COLLECTION ═════



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ NAVIGATION ═════
require_once('includes/signin_menu.php');



// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
echo '<DIV id="main">';

echo   '<div style="margin:auto; margin-bottom:15px; padding: 5px; width:620px; border:1px solid red; font-size:0.8em;">
	<span><b><u>INSTRUCTIONS</b></u><br />
		Si vous avez déjà un compte, mais que vous ne vous souvenez plus de vos informations de connexion, saississez ci-dessous l\'adresse courriel associée à votre compte. Si votre compte peut être retrouvé grâce à cette adresse, la BD vous enverra un courriel contenant le nom de votre compte, ainsi que votre nouveau mot de passe.<br />
	</span>
	</div>';

echo '<span class="section-title">Récupération des informations de connexion</span>';
echo '<hr width=35% />';

echo '<div>';
echo '<form method="post">';

echo '<table>';
echo '<tr><td class="inputname">Courriel</td>			<td class="inputbox"><input name="mail" type="email" value="' .$_inputmail. '" maxlength="254" style="width:250px"/></td></tr>';
echo '<tr class="filler"></tr>';

echo '<tr><td colspan="2">';
	echo '<button type="submit" name="action" value="get_password" class="submit-button" />';
	echo 'Envoyer';
	echo '</button>';
echo '</td></tr>';
echo '</table>';

echo '</form>';
echo '</div>';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "";
require_once('includes/footer.php');

?>