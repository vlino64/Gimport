<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id            = intval($_REQUEST['id']);
$nom_uf        = str_replace("'","\'",$_REQUEST['nom_uf']);
$hores         = isset($_REQUEST['hores'])           ? $_REQUEST['hores']           : 0;
$data_inici    = substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2);
$data_fi       = substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2);
$activat       = isset($_REQUEST['activat']) ? $_REQUEST['activat'] : 'N';

$sql    = "update moduls_materies_ufs set activat='$activat',hores_finals=$hores where id_mat_uf_pla=$id";
$result = $db->query($sql);

$sql    = "update unitats_formatives set nom_uf='$nom_uf',hores=$hores,data_inici='$data_inici',data_fi='$data_fi' where idunitats_formatives=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>