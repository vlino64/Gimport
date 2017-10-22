<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_mat_uf_pla = intval($_REQUEST['id']);
$idgrups       = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0;
$automatricula = isset($_REQUEST['automatricula']) ? $_REQUEST['automatricula'] : 'N';
/*if ($automatricula == '' || $automatricula == 'N') {
	$automatricula = '';
}
else {
	$automatricula = 'S';
}*/
$contrasenya   = isset($_REQUEST['contrasenya']) ? $_REQUEST['contrasenya'] : '';

/*$sql = "UPDATE moduls_materies_ufs SET activat='$activat' WHERE id_mat_uf_pla=$id_mat_uf_pla";
$result = $db->query($sql);*/

$sql = "UPDATE grups_materies SET automatricula='$automatricula',contrasenya='$contrasenya' WHERE id_mat_uf_pla=$id_mat_uf_pla AND id_grups=$idgrups";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>