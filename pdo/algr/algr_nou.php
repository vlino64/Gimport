<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes    = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;
$idgrups      = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']   : 0 ;
$curs_escolar = $_SESSION['curs_escolar'];

$result = 0;
$rsMateries = getMateriesGrup($db,$curs_escolar,$idgrups);
foreach($rsMateries->fetchAll() as $row) {
        $idgrups_materies = $row['idgrups_materies'];
	$sql = "INSERT INTO alumnes_grup_materia (idalumnes,idgrups_materies) VALUES ('$idalumnes','$idgrups_materies')";

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
