<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
//require_once('../func/seguretat.php');
$db->exec("set names utf8");

$t_login      = isset($_POST['login'])  ? $_POST['login'] : 0 ;
$t_passwd     = isset($_POST['passwd']) ? $_POST['passwd'] : 0 ;
$idprofessors = validaRegistreProfessor($db,$t_login,$t_passwd,TIPUS_login,TIPUS_contrasenya);
$curs_escolar = getCursActual($db)["idperiodes_escolars"];

if($_POST['s3capcha'] == $_SESSION['s3capcha'] && $_POST['s3capcha'] != '') {
	//unset($_SESSION['s3capcha']);
	//session_unset();
	
	if ($idprofessors != 0) {
		if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE,date("Y-m-d"))) {
			if (existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE,date("Y-m-d"))) {
				$log = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE);
				echo json_encode(array(
					'login' => true,
					'msg'   => 'Registre de sortida fet correctament !'
				));
			}
			else {
				echo json_encode(array(
					 'error' => true,
					 'msg'   => 'Error: Pendent de fer registre d\'entrada !'
				));
			}
		}
		else {
			$hora_entrada  = getLastLogProfessor($db,$idprofessors,date("Y-m-d"),TIPUS_ACCIO_ENTROALCENTRE);
			$hora_sortida  = getLastLogProfessor($db,$idprofessors,date("Y-m-d"),TIPUS_ACCIO_SURTODELCENTRE);
			
			if (($hora_entrada!= '00:00')) {
				if ($hora_entrada > $hora_sortida) {
					$log = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE);
					echo json_encode(array(
						'login' => true,
						'msg'   => 'Registre de sortida fet correctament !'
					));
				}
				else {
					echo json_encode(array(
						 'error' => true,
						 'msg'   => 'Error: Pendent de fer registre d\'entrada !'
					));
				}
			}
			else {
				echo json_encode(array(
					 'error' => true,
					 'msg'   => 'Error: Pendent de fer registre d\'entrada !'
				));
			}
		}
	}
	
	else {
		echo json_encode(array(
				'error'   => true,
				'msg' => 'Error: Docent no existeix!'
		)); 
	}

}
//mysql_close();
?>