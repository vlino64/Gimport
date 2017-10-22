<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id = intval($_REQUEST['id']);

$sql    = "delete from alumnes_grup_materia where idalumnes=$id";
$result = $db->query($sql);

$sql    = "delete from incidencia_alumne where idalumnes=$id ";
$result = $db->query($sql);

$sql    = "delete from alumnes_families where idalumnes=$id";
$result = $db->query($sql);

$sql    = "delete from contacte_alumne where id_alumne=$id";
$result = $db->query($sql);

$sql    = "delete from contacte_families where id_families = (select idfamilies from alumnes_families where idalumnes=$id");
$result = $db->query($sql);

$sql    = "delete from alumnes where idalumnes=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>