<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0 ;

$sql  = "SELECT DISTINCT p.codi_professor,p.activat,cp.* FROM professors p ";
$sql .= "INNER JOIN contacte_professor cp ON cp.id_professor=p.idprofessors ";
$sql .= "WHERE cp.id_tipus_contacte=".TIPUS_nom_complet." AND p.idprofessors<>".$idprofessors." AND p.activat = 'S'" ;
$sql .= " ORDER BY cp.Valor";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>