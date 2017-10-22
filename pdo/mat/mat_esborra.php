<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id = intval($_REQUEST['id']);

$sql = "delete from materia where idmateria=$id";
$result = $db->query($sql);

$sql = "delete from moduls_materies_ufs where id_mat_uf_pla=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'No es posible esborrar aquesta mat&egrave;ria.'));
}

//mysql_close();
?>