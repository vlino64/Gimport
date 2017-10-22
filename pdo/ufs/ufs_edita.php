<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->query("SET NAMES 'utf8'");

$id                    = intval($_REQUEST['id']);
$idplans_estudis       = $_REQUEST['idplans_estudis'];
$nom_uf                = str_replace("'","\'",$_REQUEST['nom_uf']);
$hores_finals          = $_REQUEST['hores_finals'];
$hores                 = $_REQUEST['hores'];
$curs_escolar          = $_SESSION['curs_escolar'];

$sql = "update idunitats_formatives set nom_uf='$nom_uf',hores='$hores' where idunitats_formatives=$id";
$result = $db->query($sql);

$sql = "update moduls_materies_ufs set idplans_estudis='$idplans_estudis',hores_finals='$hores_finals',Curs='$curs_escolar' where id_mat_uf_pla=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>