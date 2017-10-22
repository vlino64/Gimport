<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0;

$sql  = "SELECT distinct(gr.idgrups),gr.nom FROM grups gr ";
$sql .= "INNER JOIN grups_materies  gm ON gm.id_grups=gr.idgrups ";
$sql .= "INNER JOIN prof_agrupament pa ON pa.idagrups_materies=gm.idgrups_materies ";
$sql .= "WHERE pa.idprofessors='".$idprofessors."' ";
$sql .= "ORDER BY 2 ";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>