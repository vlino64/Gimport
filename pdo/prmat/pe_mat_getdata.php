<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$q    = isset($_POST['q']) ? strval($_POST['q']) : '';

$sql  = "SELECT gm.*,ma.nom_materia,gr.nom,CONCAT(ma.nom_materia,' - ',gr.nom) AS matgrup FROM grups_materies gm ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE ma.nom_materia like '%$q%' OR gr.nom like '%$q%' ";

$sql .= "UNION ";

$sql .= "SELECT gm.*,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia,gr.nom,CONCAT(m.nom_modul,'::',uf.nom_uf,' - ',gr.nom) AS matgrup FROM grups_materies gm ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla      = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla      = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls          = m.idmoduls ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE m.nom_modul like '%$q%' OR gr.nom like '%$q%' ";

$sql .= "ORDER BY 2,3";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>