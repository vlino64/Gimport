<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id         = intval($_REQUEST['idperiodes_escolars']);
$nom        = $_REQUEST['Nom'];
$descripcio = $_REQUEST['Descripcio'];
$data_inici = substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2);
$data_fi    = substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2);
$actual     = $_REQUEST['actual'];

$sql = "update periodes_escolars set nom='$nom',descripcio='$descripcio',data_inici='$data_inici',data_fi='$data_fi',actual='$actual' where idperiodes_escolars=$id";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>