<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$idprofessors  = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0 ;

$sql  = "SELECT pa.*,ma.nom_materia,CONCAT(ma.nom_materia,' - ',gr.nom) AS matgrup,gr.nom FROM prof_agrupament pa ";
$sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE pa.idprofessors='".$idprofessors."' ";

$sql .= "UNION ";

$sql .= "SELECT pa.*,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia,CONCAT(m.nom_modul,'::',uf.nom_uf,' - ',gr.nom) AS matgrup,gr.nom FROM prof_agrupament pa ";
$sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla      = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla      = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls          = m.idmoduls ";
$sql .= "INNER JOIN grups gr ON gm.id_grups=gr.idgrups ";
$sql .= "WHERE pa.idprofessors='".$idprofessors."' ";

$sql .= "ORDER BY 4,6 ";

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>