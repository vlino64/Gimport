<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_mat_uf_pla = $_REQUEST['id_mat_uf_pla'];
$id_grups      = $_REQUEST['id_grups'];
$data_inici    = substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2);
$data_fi       = substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2);

// Insertem la moduls_materies_ufs
$sql = "insert into grups_materies (id_mat_uf_pla,id_grups,data_inici,data_fi) values ('$id_mat_uf_pla','$id_grups','$data_inici','$data_fi')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
