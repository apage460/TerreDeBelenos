<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/user.class.php');
include_once('services/user-services.class.php');
include_once('controllers/user-controller.class.php');
include_once('views/user_ui.class.php');



// ═════ INITIALIZATION ═════
session_start();

$_redirect = null;
$_show_form = True;
$_message = ""; $_msg_class = "";
$_user = new User();

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_user_svc = new UserServices($_db, $_user);

$_user_ctrl = new UserController($_user_svc);



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {
	if( $_POST['action'] == 'register') { 
		if( $_user_ctrl->CheckInput() ) {
			if( $_user_ctrl->CheckAge(AGE_NO_TUTOR) && !$_user_ctrl->CheckMail($_POST) ) {
				$_message = $_user_ctrl->Error; $_msg_class = "error";
			}
			elseif( !$_user_ctrl->CheckAge(AGE_NO_TUTOR) && !$_user_ctrl->CheckTutor($_POST) ) {
				$_message = $_user_ctrl->Error; $_msg_class = "error";
			}
			elseif( $_user_ctrl->RegisterUser() ) {
				$_message = "L'enregistrement du compte s'est fait avec succès!"; $_msg_class = "success";
				$_show_form = False;
			}
			else { $_message = "Une erreur est survenue lors de l'enregistrement!"; $_msg_class = "error"; }
		}
		else { $_message = $_user_ctrl->Error; $_msg_class = "error"; }
	}
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
$_user = $_user_svc->GetUser();
$_user_ui = new UserUI( $_user );

echo '<DIV id="main">';

if($_show_form) {
	echo   '<div style="margin:auto; margin-bottom:15px; padding: 5px; width:620px; border:1px solid red; font-size:0.8em;">
		<span><b><u>IMPORTANT</b></u><br />
			Si vous avez créé un compte depuis 2016, mais que vous avez oublié votre identifiant ou votre mot de passe, contactez TI@Terres-de-Belenos.com. <br />
			<b>En aucun cas vous ne devez vous créer un second compte.</b> Ceux-ci sont régulièrement détruit pour conserver l\'intégrité des données et vous prenez le risque de perdre vos personnages et vos acquis.<br />
		</span>
		</div>';

	echo '<span class="ghost" style="padding-left:100px;">Saisissez les informations suivantes : </span>';
	$_user_ui->DisplayRegForm();
}
else{
	echo '<a href="index.php">S\'authentifier</a><br />';
}

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = ""; if($_show_form) { $footnote = "<br><br>Si vous avez moins de ".AGE_NO_TUTOR." ans, vous pouvez vous créer un compte et jouer à condition d'avoir un tuteur de 25 ans et plus dont le compte utilisateur existe déjà dans la base de données. Assurez-vous alors d'avoir noter l'identifiant du compte de votre tuteur.<br><br>Certaines activités ne requièrent pas que les très jeunes enfants créent un compte pour jouer. Informez-vous à l'Accueil."; }
require_once('includes/footer.php');

?>