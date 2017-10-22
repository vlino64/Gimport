<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups     = isset($_REQUEST['id_grups']) ? $_REQUEST['id_grups'] : 0;
$idtorn      = getGrup($db,$idgrups)["idtorn"];
$g_pe        = isset($_REQUEST['g_pe']) ? $_REQUEST['g_pe'] : '';
$m_pe        = isset($_REQUEST['m_pe']) ? $_REQUEST['m_pe'] : '';
$curs_actual = getCursActual($db)["idperiodes_escolars"]; 

$sql  = "SELECT df.id_dies_franges,CONCAT(t.nom_torn,'-',ds.dies_setmana,'(',LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5),')') AS dia_hora,' ' AS idespais_centre ";
$sql .= "FROM dies_franges df ";
$sql .= "INNER JOIN dies_setmana     ds ON df.iddies_setmana     = ds.iddies_setmana ";
$sql .= "INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries ";
$sql .= "INNER JOIN torn 			  t ON t.idtorn              = fh.idtorn ";
$sql .= "WHERE fh.idtorn=".$idtorn." AND df.idperiode_escolar=".$curs_actual." AND fh.esbarjo<>'S'";
$sql .= "ORDER BY ds.iddies_setmana,fh.idfranges_horaries ";

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>