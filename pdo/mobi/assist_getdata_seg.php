<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../mobi/seguretat.php');
$db->exec("set names utf8");

$idgrups                = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']   : 0 ;
$idmateria              = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;
$grup_materia           = existGrupMateria($db,$idgrups,$idmateria);

$data                   = date("Y-m-d");
$franja                 = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
$dia                    = date("w");
$dia_franja             = existDiesFranges($db,$dia,$franja);

$sql = "SELECT lectiva,seguiment FROM qp_seguiment WHERE id_grup_materia='$grup_materia' AND data='$data' AND id_dia_franja='$dia_franja'";

$rs = $db->query($sql);

$items = array('lectiva' => '','seguiment' => '');
  
foreach($rs->fetchAll() as $row) {  
	$items = $row;
}  
$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
//mysql_close();
?>