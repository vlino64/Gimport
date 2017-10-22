<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups          = isset($_REQUEST['idgrup'])    ? $_REQUEST['idgrup'] : 0 ;
$idmateria        = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;

$sql              = "insert into grups_materies(id_grups,id_mat_uf_pla) values ('$idgrups','$idmateria')";
$result           = $db->query($sql);
$idgrups_materies = $db->lastInsertId();

$rsAlumnes        = getAlumnesGrup($db,$idgrups,TIPUS_nom_complet);
foreach($rsAlumnes->fetchAll() as $row) {
	$sql    = "INSERT INTO alumnes_grup_materia (idalumnes,idgrups_materies) VALUES ('".$row['idalumnes']."','$idgrups_materies')";
	$result = $db->query($sql);
}

echo json_encode(array('success'=>true));

//mysql_free_result($rsAlumnes);
//mysql_close();
?>