<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes_grup_materia = isset($_REQUEST['idalumnes_grup_materia']) ? $_REQUEST['idalumnes_grup_materia'] : 0 ;
$idalumnes              = getAlumneGrupMateria($db,$idalumnes_grup_materia)["idalumnes"];
$idgrups_materies       = getAlumneGrupMateria($db,$idalumnes_grup_materia)["idgrups_materies"];
$idgrups                = getGrupMateria($db,$idgrups_materies)["id_grups"];
$idmateria              = getGrupMateria($db,$idgrups_materies)["id_mat_uf_pla"];
$id_tipus_incidencia    = isset($_REQUEST['id_tipus_incidencia'])    ? $_REQUEST['id_tipus_incidencia']    : 0 ;
$id_tipus_incident      = isset($_REQUEST['id_tipus_incident'])      ? $_REQUEST['id_tipus_incident']      : 0 ;
$idprofessors 		= isset($_SESSION['professor'])              ? $_SESSION['professor']              : 0 ;
$data         		= isset($_REQUEST['data'])                   ? $_REQUEST['data']                   : 0 ;
$idfranges_horaries     = isset($_REQUEST['idfranges_horaries'])     ? $_REQUEST['idfranges_horaries']     : 0 ;
$comentari    		= isset($_REQUEST['comentari'])              ? str_replace("'","\'",$_REQUEST['comentari']) : '' ;

$accio_inserir = 1;

if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_ABSENCIA) {
	if (existsIncidenciaAlumnebyTipus($db,$idalumnes,$data,$idfranges_horaries,TIPUS_FALTA_ALUMNE_RETARD)) {
		$accio_inserir = 0;
	}
}

if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_RETARD) {
	if (existsIncidenciaAlumnebyTipus($db,$idalumnes,$data,$idfranges_horaries,TIPUS_FALTA_ALUMNE_ABSENCIA)) {
		$accio_inserir = 0;
	}
}

if ($accio_inserir) {
	
	$sql     = "INSERT INTO incidencia_alumne (idalumnes,idgrups,id_mat_uf_pla,idprofessors,id_tipus_incidencia,id_tipus_incident,data,idfranges_horaries,comentari) ";
	$sql    .= "VALUES ('$idalumnes','$idgrups','$idmateria','$idprofessors','$id_tipus_incidencia','$id_tipus_incident','$data','$idfranges_horaries','$comentari')";
	$result = $db->query($sql);
	
	if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}
}
?>


