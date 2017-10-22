<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
      
   $strNoCache    = "";
   $idalumne      = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;
   $idgrup        = getGrupAlumne($db,$idalumne)["idgrups"];
   $idprofessor   = getCarrecPrincipalGrup($db,TIPUS_TUTOR,$idgrup);
   
   if (isset($_SESSION['familia_1'])) {
	   $num_tutor  = 1;
	   $nom_tutor  = getFamilia($db,$_SESSION['familia_1'],TIPUS_nom_pare)." ";
	   $nom_tutor .= getFamilia($db,$_SESSION['familia_1'],TIPUS_cognom1_pare)." ";
	   $nom_tutor .= getFamilia($db,$_SESSION['familia_1'],TIPUS_cognom2_pare)." ";
   }
   else if (isset($_SESSION['familia_2'])) {
	   $num_tutor  = 2;
	   $nom_tutor  = getFamilia($db,$_SESSION['familia_2'],TIPUS_nom_mare)." ";
	   $nom_tutor .= getFamilia($db,$_SESSION['familia_2'],TIPUS_cognom1_mare)." ";
	   $nom_tutor .= getFamilia($db,$_SESSION['familia_2'],TIPUS_cognom2_mare)." ";
   }
   
   $login_tutor   = isset($_SESSION['usuari'])   ? $_SESSION['usuari'] : 0 ;    
   $missatge      = isset($_REQUEST['missatge']) ? str_replace("'","\'",$_REQUEST['missatge']) : '';
   
   if ($idalumne != 0) {
   		$sql    = "INSERT INTO missatges_tutor (idprofessor,idalumne,idgrup,login_tutor,num_tutor,data,hora,missatge) ";
		$sql   .= "VALUES ('$idprofessor','$idalumne','$idgrup','$login_tutor',$num_tutor,'".date("Y-m-d")."','".date("H:i")."','$missatge')";
		$result = $db->query($sql);
   }
   
   include('../families/families_missatge_tutor_send.php');

   echo json_encode(array('success'=>true));

   //mysql_close();  
?>