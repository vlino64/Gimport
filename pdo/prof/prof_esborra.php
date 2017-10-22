<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id = intval($_REQUEST['id']);

$sql = "delete from incidencia_alumne where idprofessors=$id";
$result = $db->query($sql);

$sql = "delete from prof_agrupament where idprofessors=$id";
$result = $db->query($sql);

$sql = "delete from guardies where idprofessors=$id";
$result = $db->query($sql);

$sql = "delete from professor_carrec where idprofessors=$id";
$result = $db->query($sql);

$sql = "delete from contacte_professor where id_professor=$id";
$result = $db->query($sql);

$sql = "delete from professors where idprofessors=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

?>