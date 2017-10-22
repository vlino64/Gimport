<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  unset($_SESSION['target_sms']);
  $_SESSION['target_sms'] = array();
  $idalumnes = $_REQUEST['idalumnes'];
  
  $alumnes_array     = explode(",", $idalumnes);
  $sms_array         = array();
  $nom_alumnes_array = array();
  
  foreach ($alumnes_array as $id_alumne) {
	  
    $nom_sms   = str_replace(",","",getValorTipusContacteAlumne($db,$id_alumne,TIPUS_nom_complet));
    // Afegim l'id per poder consultar a posteriori des dels centres. 
    // Al server 2 s'extreurà l'id de l'alumne
    $nom_sms = $id_alumne."#".$nom_sms;    

    //$fp = fopen("log.txt","a");
	//fwrite($fp,$nom_sms.PHP_EOL);
	//fclose($fp);
    
    $sms_tutor = getValorTipusContacteFamilies($db,$id_alumne,TIPUS_mobil_sms);
    if ($sms_tutor != '') {
	  $mobil_sms = "+34.".getValorTipusContacteFamilies($db,$id_alumne,TIPUS_mobil_sms);
	  array_push($sms_array,$mobil_sms);
	  array_push($nom_alumnes_array,$nom_sms);
	}
      	
	$sms_tutor2 = getValorTipusContacteFamilies($db,$id_alumne,TIPUS_mobil_sms2);
	if ($sms_tutor2 != '') {
	  $mobil_sms2 = "+34.".getValorTipusContacteFamilies($db,$id_alumne,TIPUS_mobil_sms2);
	  array_push($sms_array,$mobil_sms2);
	  array_push($nom_alumnes_array,$nom_sms);
	}
	
  }
  $_SESSION['target_sms']         = $sms_array;
  $_SESSION['target_nom_alumnes'] = $nom_alumnes_array;
  
  //mysql_close();
?>
