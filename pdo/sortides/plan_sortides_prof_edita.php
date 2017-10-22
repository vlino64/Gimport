<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessorat_sortides = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ; 
$id_sortida     		= isset($_SESSION['sortida']) ? $_SESSION['sortida'] : 0 ;
if ($id_sortida == 0) {
	$id_sortida = isset($_REQUEST['sortida']) ? $_REQUEST['sortida'] : 0 ;
}
$id_professorat 		= isset($_REQUEST['id_professorat']) ? $_REQUEST['id_professorat'] : 0 ;
$responsable   			= isset($_REQUEST['responsable']) ? $_REQUEST['responsable'] : '' ;

$sql = "UPDATE sortides_professor SET id_professorat='$id_professorat',responsable='$responsable' WHERE idprofessorat_sortides='$idprofessorat_sortides'";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>