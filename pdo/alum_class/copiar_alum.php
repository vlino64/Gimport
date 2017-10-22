<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups_materies       = isset($_REQUEST['idgrups_materies'])       ? $_REQUEST['idgrups_materies'] : 0 ;
$idgrups_materies_desde = isset($_REQUEST['idgrups_materies_desde']) ? $_REQUEST['idgrups_materies_desde'] : 0 ;

$sql  = "SELECT agm.idalumnes FROM alumnes_grup_materia agm ";
$sql .= "WHERE agm.idgrups_materies=".$idgrups_materies_desde;

$rsAlumnes = $db->query($sql);

foreach($rsAlumnes->fetchAll() as $row) {
	
    if (! getIDAlumneAgrupament($db,$row['idalumnes'],$idgrups_materies)) {
        $sql    = "INSERT INTO alumnes_grup_materia (idalumnes,idgrups_materies) VALUES (".$row['idalumnes'].",".$idgrups_materies.")";
	$result = $db->query($sql);
    }
    
}

echo json_encode(array('success'=>true));

//mysql_free_result($rsAlumnes);
//mysql_close();
?>