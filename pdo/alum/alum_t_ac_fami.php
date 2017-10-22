<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes   = intval($_REQUEST['idalumnes']);
$id_families = getFamiliaAlumne($db,$idalumnes);

// Treure l'acc�s per la familia per veure tot(e)s els/les german(e)s
$sql  = "SELECT a.idalumnes FROM alumnes a ";
$sql .= "INNER JOIN alumnes_families af ON af.idalumnes=a.idalumnes ";
$sql .= "WHERE af.idfamilies=".$id_families;
$rs = $db->query($sql);
foreach($rs->fetchAll() as $row) {
      $sql_al = "update alumnes set acces_familia='N' where idalumnes=".$row['idalumnes'];
      $result = $db->query($sql_al);
}	   
$rs->closeCursor();
   
if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}

//mysql_close();
?>