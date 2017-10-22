<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id                     = intval($_REQUEST['id']);
$idgrups                = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']   : 0 ;
$idmateria              = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;
$idprofessors 		= isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;
$data         		= date("Y-m-d");
$idfranges_horaries     = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
$idalumnes_grup_materia = getAlumneMateriaGrup($db,$idgrups,$idmateria,$id)["idalumnes"];
//$idalumnes_grup_materia = getAlumneMateriaGrup($db,$idgrups,$idmateria,$id)["idalumnes"]_grup_materia;

$sql = "DELETE FROM incidencia_alumne WHERE idalumnes=$id AND data='$data' AND idfranges_horaries='$idfranges_horaries'";
$result = $db->query($sql);

$sql = "DELETE FROM ccc_taula_principal WHERE idalumne=$id AND data='$data' AND idfranges_horaries='$idfranges_horaries'";
$result = $db->query($sql);


if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>