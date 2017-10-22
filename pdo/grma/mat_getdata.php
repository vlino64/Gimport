<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  ="SELECT idmateria AS id, nom_materia AS nom FROM materia ";
$sql .="UNION ";
$sql .="SELECT uf.idunitats_formatives AS id, CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom FROM moduls m ";
$sql .="INNER JOIN moduls_ufs mu         ON mu.id_moduls=m.idmoduls ";
$sql .="INNER JOIN unitats_formatives uf ON uf.idunitats_formatives=mu.id_ufs ";
$sql .=" ORDER BY 2";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>