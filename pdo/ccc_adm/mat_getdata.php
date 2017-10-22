<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrup = isset($_REQUEST['idgrup']) ? $_REQUEST['idgrup'] : 0 ;

$sql  = "SELECT gm.id_mat_uf_pla as id_mat_uf_pla,ma.nom_materia AS nom ";
$sql .= "FROM grups_materies gm ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE gm.id_grups='".$idgrup."' ";
$sql .= "UNION ";
$sql .= "SELECT gm.id_mat_uf_pla as id_mat_uf_pla,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom ";
$sql .= "FROM grups_materies gm ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla=uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs mu         ON gm.id_mat_uf_pla=mu.id_ufs ";
$sql .= "INNER JOIN moduls m              ON mu.id_moduls=m.idmoduls ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE gm.id_grups='".$idgrup."' ";
$sql .= "ORDER BY 2";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>