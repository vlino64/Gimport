<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes   = intval($_REQUEST['idalumnes']);
$id_families = getFamiliaAlumne($db,$idalumnes);
$data_n      = getAlumne($db,$idalumnes,TIPUS_data_naixement);
if ($data_n != '') {
    
    $data_naixement = explode("/",$data_n);
    $dia_n = str_pad($data_naixement[0], 2, "0", STR_PAD_LEFT);
    $mes_n = str_pad($data_naixement[1], 2, "0", STR_PAD_LEFT);
    $any_n = $data_naixement[2];
    
    //$data_naixement = substr($data_n,6,4)."-".substr($data_n,3,2)."-".substr($data_n,0,2);
    $data_naixement = $any_n."-".$mes_n."-".$dia_n;
    $edat           = calculaEdat($data_naixement);
    
    $dona_acces = ($edat >= 18) ? 'F' : 'S';
}
else {
    $dona_acces = 'S';
}

$sql  = "SELECT a.idalumnes FROM alumnes a ";
$sql .= "INNER JOIN alumnes_families af ON af.idalumnes=a.idalumnes ";
$sql .= "WHERE af.idfamilies=".$id_families;
$rs = $db->query($sql);
foreach($rs->fetchAll() as $row) {
      $sql_al = "update alumnes set acces_familia='".$dona_acces."' where idalumnes=".$row['idalumnes'];
      $result = $db->query($sql_al);
}	   
$rs->closeCursor();
   
if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}

//mysql_close();
?>