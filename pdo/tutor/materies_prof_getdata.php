<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$q               = isset($_POST['q']) ? strval($_POST['q']) : '';
$idgrups         = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0;
$idprofessors    = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0;

$sql  = "SELECT mm.id_mat_uf_pla,ma.nom_materia AS materia,gr.nom,mm.idplans_estudis FROM prof_agrupament pa ";
$sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE gm.id_grups=".$idgrups." AND pa.idprofessors='".$idprofessors."' ";

$sql .= "UNION ";

$sql .= "SELECT mm.id_mat_uf_pla,CONCAT(m.nom_modul,'-',uf.nom_uf) AS materia,gr.nom, ";
$sql .= "mm.idplans_estudis FROM prof_agrupament pa ";
$sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla      = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla      = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls          = m.idmoduls ";
$sql .= "INNER JOIN grups              gr ON gm.id_grups           = gr.idgrups ";
$sql .= "WHERE gm.id_grups=".$idgrups." AND pa.idprofessors='".$idprofessors."' ";

$sql .= " ORDER BY 1,2";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>