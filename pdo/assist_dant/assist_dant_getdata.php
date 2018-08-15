<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$grup_materia       = isset($_REQUEST['grup_materia']) ? $_REQUEST['grup_materia'] : 0 ;
$data               = isset($_REQUEST['data']) ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : '1989-1-1';
$idfranges_horaries = isset($_REQUEST['idfranges_horaries'])     ? $_REQUEST['idfranges_horaries']     : 0 ;

$sort   = isset($_POST['sort']) ? strval($_POST['sort']) : '2';  
$order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$result = array();

$sql  = "(SELECT DISTINCT(agm.idalumnes),ca.Valor AS alumne,ia.id_tipus_incidencia,ia.id_tipus_incident,tf.tipus_falta,ia.data,ia.comentari, agm.idalumnes_grup_materia,CONCAT('".$data."') AS data,ia.idfranges_horaries,ti.tipus_incident ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes            a ON agm.idalumnes          = a.idalumnes ";
$sql .= "INNER JOIN incidencia_alumne  ia ON agm.idalumnes=ia.idalumnes "; 
$sql .= "INNER JOIN tipus_incidents  ti ON ia.id_tipus_incident=ti.idtipus_incident "; 
$sql .= "INNER JOIN tipus_falta_alumne tf ON ia.id_tipus_incidencia=tf.idtipus_falta_alumne ";
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes=ca.id_alumne ";
$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";
$sql .= "WHERE a.activat='S' AND agm.idgrups_materies=".$grup_materia." AND ca.id_tipus_contacte=".TIPUS_nom_complet." AND ia.data='".$data."'  AND idfranges_horaries='".$idfranges_horaries."' GROUP BY 1) ";
$sql .= "UNION (SELECT DISTINCT(agm.idalumnes),ca.Valor AS alumne,CONCAT(' ') AS id_tipus_incidencia,";
$sql .= "CONCAT(' ') AS id_tipus_incident,CONCAT(' ') AS tipus_falta,CONCAT(' ') AS data,CONCAT(' ') AS comentari, agm.idalumnes_grup_materia,CONCAT('".$data."') AS data,".$idfranges_horaries." AS idfranges_horaries,CONCAT(' ') AS tipus_incident "; 
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN alumnes            a ON agm.idalumnes          = a.idalumnes ";
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes=ca.id_alumne ";
$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";
$sql .= "WHERE a.activat='S' AND agm.idgrups_materies=".$grup_materia." AND ca.id_tipus_contacte=".TIPUS_nom_complet." AND agm.idalumnes NOT IN ";
$sql .= "(SELECT DISTINCT(agm.idalumnes) ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN incidencia_alumne ia ON agm.idalumnes=ia.idalumnes "; 
$sql .= "INNER JOIN tipus_incidents   ti ON ia.id_tipus_incident=ti.idtipus_incident "; 
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes=ca.id_alumne ";
$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";
$sql .= "INNER JOIN grups              g ON gm.id_grups            = g.idgrups "; 
$sql .= "WHERE agm.idgrups_materies=".$grup_materia." AND ca.id_tipus_contacte=".TIPUS_nom_complet." AND ia.data='".$data."' AND idfranges_horaries='".$idfranges_horaries."')) ";
$sql .= "ORDER BY $sort $order ";

$rs = $db->query($sql);
  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  

echo json_encode($result);

$rs->closeCursor();
?>
