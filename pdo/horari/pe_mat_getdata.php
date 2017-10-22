<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  = "SELECT gm.*,ma.nom_materia,gr.nom,CONCAT(ma.nom_materia,' - ',gr.nom) AS matgrup FROM grups_materies gm ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "ORDER BY ma.nom_materia,gr.nom";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>