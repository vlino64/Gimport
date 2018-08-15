<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$nom_falta                    = isset($_REQUEST['nom_falta']) ? $_REQUEST['nom_falta'] : 0 ;
$valor                        = isset($_REQUEST['valor']) ? $_REQUEST['valor'] : 0 ;
if ($valor=='') {
    $valor = 1;
}

$limit_acumulacio_comunicacio = isset($_REQUEST['limit_acumulacio_comunicacio']) ? $_REQUEST['limit_acumulacio_comunicacio'] : 0 ;
if ($limit_acumulacio_comunicacio=='') {
    $limit_acumulacio_comunicacio = 1;
}

$comentari 	      	      = isset($_REQUEST['comentari']) ? str_replace("'","\'",$_REQUEST['comentari']) : 0 ;

$sql  = "insert into ccc_tipus (nom_falta,valor,limit_acumulacio_comunicacio,comentari) values ";
$sql .= "('$nom_falta',$valor,$limit_acumulacio_comunicacio,'$comentari')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}
?>
