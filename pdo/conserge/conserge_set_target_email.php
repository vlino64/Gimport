<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  unset($_SESSION['target_email']);
  $_SESSION['target_email'] = array();
  $idalumnes = $_REQUEST['idalumnes'];
  
/*$fp = fopen("log.txt","a");
fwrite($fp, $_REQUEST['idalumnes'] . PHP_EOL);
fclose($fp);*/
  
  $alumnes_array     = explode(",", $idalumnes);
  $email_array       = array();
  
  foreach ($alumnes_array as $id_alumne) {
	array_push($email_array,$id_alumne);
  }
  $_SESSION['target_email'] = $email_array;
  
  //mysql_close();
?>