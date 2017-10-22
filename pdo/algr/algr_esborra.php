<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$idalumnes    = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;
$idgrups      = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']   : 0 ;
$curs_escolar = getCursActual($db)["idperiodes_escolars"];

$rsMateries = getMateriesGrup($db,$curs_escolar,$idgrups);
foreach($rsMateries->fetchAll() as $row) {
    $idgrups_materies = $row['idgrups_materies'];
    $sql = "DELETE FROM alumnes_grup_materia WHERE idalumnes=$idalumnes AND idgrups_materies=$idgrups_materies";
    $result = $db->query($sql);
}

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_free_result($rsMateries);
//mysql_close();
?>