<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id   =  intval($_REQUEST['id']);

$sql  = "SELECT cf.Valor as login ";
$sql .= "FROM contacte_families cf ";
$sql .= "INNER JOIN alumnes_families af ON cf.id_families = af.idfamilies ";
$sql .= "WHERE af.idalumnes=".$id." AND cf.id_tipus_contacte=".TIPUS_login;
$rs = $db->query($sql);

foreach($rec->fetchAll() as $row) {  
	$login_tutor_1 = $row["login"];
}  

$sql  = "SELECT cf.Valor as login ";
$sql .= "FROM contacte_families cf ";
$sql .= "INNER JOIN alumnes_families af ON cf.id_families = af.idfamilies ";
$sql .= "WHERE af.idalumnes=".$id." AND cf.id_tipus_contacte=".TIPUS_login2;
$rs = $db->query($sql);

foreach($rec->fetchAll() as $row) {  
	$login_tutor_2 = $row["login"];
}  

$items = array('login_tutor_1' => $login_tutor_1,'login_tutor_2' => $login_tutor_2);
  
//$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
//mysql_close();
?>