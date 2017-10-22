<?php
	session_start();	 
	require_once('../bbdd/connect.php');
        require_once('../func/constants.php');
        require_once('../func/generic.php');
        //require_once('../func/seguretat.php');
		
	
	if (isset($_POST['login']) && $_POST['login'] != '') {
			   $result = validaProfessor($db,$_POST['login'],$_POST['passwd'],TIPUS_login,TIPUS_contrasenya);
			   if ( $result != 0 ) {
				  $_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
				  $_SESSION['curs_escolar_literal'] = getCursActual($db)["Nom"];
				  $_SESSION['professor']            = $result;
				  $_SESSION['usuari']               = $result;  
				  insertaLogProfessor($db,$_SESSION['professor'],TIPUS_ACCIO_LOGIN);
			   }
			   /*else {
			      $result = validaAlumne($_POST['login'],$_POST['passwd'],TIPUS_login,TIPUS_contrasenya);				 
				  if ( $result != 0 ) {
					  $_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
					  $_SESSION['curs_escolar_literal'] = getCursActual($db)->Nom;
					  $_SESSION['alumne']               = $result;
					  $_SESSION['usuari']               = $result;  
				  }
			   }	*/
	}
?>
