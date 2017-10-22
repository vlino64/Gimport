<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idmateria = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;
$idgrups   = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;

foreach ($idgrups as $id_grup) {
	$idgrups_materies = existGrupMateria($db,$id_grup,$idmateria);
	
	$rsAlumnes        = getAlumnesGrup($db,$id_grup,TIPUS_nom_complet);
        foreach($rsAlumnes->fetchAll() as $row) {
		$sql    = "DELETE FROM alumnes_grup_materia WHERE idgrups_materies='$idgrups_materies' AND idalumnes='".$row['idalumnes']."'";
		$result = $db->query($sql);		
	}
	
	$sql    = "DELETE FROM grups_materies WHERE idgrups_materies='$idgrups_materies'";
	$result = $db->query($sql);

}

echo json_encode(array('success'=>true));

//mysql_free_result($rsAlumnes);
//mysql_close();
?>