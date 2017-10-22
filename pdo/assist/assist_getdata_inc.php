<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id                     = intval($_REQUEST['id']);
$idgrups                = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']   : 0 ;
$idmateria              = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;
$data                   = date("Y-m-d");
$idfranges_horaries     = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
$idalumnes_grup_materia = getAlumneMateriaGrup($db,$idgrups,$idmateria,$id)["idalumnes"];
//$idalumnes_grup_materia = getAlumneMateriaGrup($db,$idgrups,$idmateria,$id)["idalumnes"]_grup_materia;

$sql          = "SELECT id_tipus_incident,comentari FROM incidencia_alumne WHERE idalumnes='$id' AND data='$data' AND idfranges_horaries='$idfranges_horaries' AND id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_SEGUIMENT."'";

$rs = $db->query($sql);

$items = array('id_tipus_incident' => '','comentari' => '');
  
foreach($rs->fetchAll() as $row) {  
	$items = $row;
}  
$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
//mysql_close();
?>