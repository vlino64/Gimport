<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id              = intval($_REQUEST['id']);
$idplans_estudis = isset($_REQUEST['idplans_estudis']) ? $_REQUEST['idplans_estudis'] : 0;
$nom_materia     = str_replace("'","\'",$_REQUEST['nom_materia']);
//$curs_escolar    = 0;
$curs_escolar    = getCursActual($db)["idperiodes_escolars"];

$sql = "update materia set nom_materia='$nom_materia' where idmateria=$id";
$result = $db->query($sql);

$sql = "update moduls_materies_ufs set idplans_estudis=$idplans_estudis,Curs=$curs_escolar where id_mat_uf_pla=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>