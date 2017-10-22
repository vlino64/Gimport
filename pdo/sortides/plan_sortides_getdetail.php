<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$id_sortida = isset($_SESSION['sortida']) ? $_SESSION['sortida'] : 0 ;

if ($id_sortida==0) {
	$id_sortida = isset($_REQUEST['idsortides']) ? $_REQUEST['idsortides'] : 0 ;
}

$sql  = "SELECT ps.idprofessorat_sortides,ps.id_professorat,ps.responsable,cp.Valor as professor FROM sortides_professor ps ";
$sql .= "INNER JOIN contacte_professor cp ON cp.id_professor=ps.id_professorat ";
$sql .= "WHERE ps.id_sortida=".$id_sortida." AND cp.id_tipus_contacte=".TIPUS_nom_complet;

$rs = $db->query($sql);

$items = array();
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>