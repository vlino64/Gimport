<?php
	ini_set("session.cookie_lifetime","7200");
	ini_set("session.gc_maxlifetime","7200");
	session_start();
	require_once('./bbdd/connect.php');
	require_once('./func/constants.php');
	require_once('./func/generic.php');
	
	if($_POST['s3capcha'] == $_SESSION['s3capcha'] && $_POST['s3capcha'] != '') {
		//unset($_SESSION['s3capcha']);
		//session_unset();
		
		if (isset($_POST['login']) && $_POST['login'] != '') {
			   $result = validaProfessor($db,$_POST['login'],$_POST['passwd'],TIPUS_login,TIPUS_contrasenya);
			   if ( $result != 0 ) {
				  $_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
				  $_SESSION['curs_escolar_literal'] = getCursActual($db)["Nom"];
				  $_SESSION['professor']            = $result;
				  $_SESSION['usuari']               = $_POST['login'];  
				  insertaLogProfessor($db,$_SESSION['professor'],TIPUS_ACCIO_LOGIN);
				  
				 /* if (! existLogProfessorData($db,$_SESSION['professor'],TIPUS_ACCIO_ENTROALCENTRE,date("Y-m-d"))) {
					      insertaLogProfessor($_SESSION['professor'],TIPUS_ACCIO_ENTROALCENTRE);
				  } */
				  
			   }
			   else {
			      $result = validaAlumne($db,$_POST['login'],$_POST['passwd'],TIPUS_login,TIPUS_contrasenya);				 
				  if ( $result != 0 ) {
					  $_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
					  $_SESSION['curs_escolar_literal'] = getCursActual($db)["Nom"];
					  $_SESSION['alumne']               = $result;
					  $_SESSION['usuari']               = $_POST['login'];  
					  insertaLogAlumne($db,$_SESSION['alumne'],TIPUS_ACCIO_LOGIN);
				  }
				  else {
                      $result = validaFamilia($db,$_POST['login'],$_POST['passwd'],TIPUS_login,TIPUS_contrasenya);				 
                      if ( $result != 0 ) {
                            $_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
                            $_SESSION['curs_escolar_literal'] = getCursActual($db)["Nom"];
                            $_SESSION['familia_1']            = $result;
                            $_SESSION['usuari']               = $_POST['login'];
                            insertaLogFamilia($db,$_SESSION['familia_1'],TIPUS_ACCIO_LOGIN);							
                      }
					  else {
						  $result = validaFamilia($db,$_POST['login'],$_POST['passwd'],TIPUS_login2,TIPUS_contrasenya2);				 
						  if ( $result != 0 ) {
								$_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
								$_SESSION['curs_escolar_literal'] = getCursActual($db)["Nom"];
								$_SESSION['familia_2']            = $result;
								$_SESSION['usuari']               = $_POST['login'];  
								insertaLogFamilia($db,$_SESSION['familia_2'],TIPUS_ACCIO_LOGIN);
						  }
						  else {
							    $_SESSION['errno'] = 1;
						  	    header('Location:index.php');
						  }
					  }
			     }	
		    } 	
		}
            $_SESSION['errno'] = 1;
	    header('Location:index.php');                
	}
?>
