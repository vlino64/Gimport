<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  = "SELECT df.*,ds.dies_setmana,CONCAT(t.nom_torn,'-',ds.dies_setmana,'(',LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5),')') AS dia_hora,CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora from dies_franges df ";
$sql .= "INNER JOIN dies_setmana     ds ON df.iddies_setmana     = ds.iddies_setmana ";
$sql .= "INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries ";
$sql .= "INNER JOIN torn 			  t ON t.idtorn              = fh.idtorn ";
$sql .= "WHERE fh.esbarjo<>'S' ORDER BY 2,6";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>