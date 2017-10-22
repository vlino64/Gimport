<?php
session_start();
include('../bbdd/connect.php');
include_once('../func/constants.php');
include_once('../func/generic.php');
$db->exec("set names utf8");

$t_login      = isset($_POST['login'])  ? $_POST['login'] : 0 ;
$t_passwd     = isset($_POST['passwd']) ? $_POST['passwd'] : 0 ;
$idprofessors = validaRegistreProfessor($db,$t_login,$t_passwd,TIPUS_login,TIPUS_contrasenya);
$curs_escolar = getCursActual($db)["idperiodes_escolars"];

// IP pública del centre
$ip = "79.157.167.34";
$ip2 = "85.192.74.226";
// $ip = "80.102.74.74";
if (($ip == $_SERVER['REMOTE_ADDR']) OR ($ip2 == $_SERVER['REMOTE_ADDR']) OR 1 )
	{
	if($_POST['s3capcha'] == $_SESSION['s3capcha'] && $_POST['s3capcha'] != '') {
		//unset($_SESSION['s3capcha']);
		//session_unset();
		
		if ($idprofessors != 0) {
			if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE,date("Y-m-d"))) {
				if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE,date("Y-m-d"))) {
					$log = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE);
					echo json_encode(array(
						'login' => true,
						'msg'   => 'Registre d\'entrada fet correctament !'
					));
				}
				else {
					echo json_encode(array(
							'error' => true,
							'msg'   => 'Error: Pendent de fer registre de sortida !'
						));
				}
			}	
			else {
				$hora_entrada  = getLastLogProfessor($db,$idprofessors,date("Y-m-d"),TIPUS_ACCIO_ENTROALCENTRE);
				$hora_sortida  = getLastLogProfessor($db,$idprofessors,date("Y-m-d"),TIPUS_ACCIO_SURTODELCENTRE);
				
				if (($hora_sortida!= '00:00')) {
					if ($hora_entrada < $hora_sortida) {
						$log = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE);
						echo json_encode(array(
							'login' => true,
							'msg'   => 'Registre d\'entrada fet correctament !'
						));
					}
					else {
						echo json_encode(array(
							'error' => true,
							'msg'   => 'Error: Pendent de fer registre de sortida !'
						));
					}
				}
				else {
					echo json_encode(array(
						'error'   => true,
						'msg' => 'Error: Pendent de fer registre de sortida !'
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
	}
else 
   {
   echo json_encode(array(
		'error' => true,
		'msg'   => 'Acció habilitada només des del centre !'
		));
   }
//mysql_close();
?>
