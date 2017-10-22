<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
		
$idfranges_horaries   = isset($_REQUEST['idfranges_horaries'])  ? $_REQUEST['idfranges_horaries'] : 0 ;
$idgrups              = isset($_REQUEST['idgrups'])             ? $_REQUEST['idgrups'] : 0 ;
$idmateries           = isset($_REQUEST['idmateries'])          ? $_REQUEST['idmateries'] : 0 ;
$id_tipus_incidencia  = isset($_REQUEST['id_tipus_incidencia']) ? $_REQUEST['id_tipus_incidencia'] : 0 ;
$id_professor         = isset($_SESSION['professor'])           ? $_SESSION['professor'] : 0 ;
$data                 = isset($_REQUEST['data'])                ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d");
$comentari            = isset($_REQUEST['comentari'])           ? str_replace("'","\'",$_REQUEST['comentari']) : '' ;
$afegir               = isset($_REQUEST['afegir'])              ? $_REQUEST['afegir']                          : 0 ;

$pos = 0;
foreach ($idfranges_horaries as $id_fh) {
	
	$id_grup    = $idgrups[$pos];
	$id_materia = $idmateries[$pos];
	
	if ($id_fh != 0){
		//esborrar incidencia per aquell professor i dia
		$sql = "DELETE FROM incidencia_professor WHERE idprofessors='$idprofessors' AND data='$data' AND idfranges_horaries='$id_fh'";
		$result = $db->query($sql);
		
		if ($afegir == 1) {
				//insertar falta asistencia per aquell professor i dia
				$sql  = "INSERT INTO incidencia_professor (idprofessors,idgrups,id_mat_uf_pla,id_tipus_incidencia,data,comentari,idfranges_horaries) ";
				$sql .= "VALUES ('$idprofessors','$id_grup','$id_materia','$id_tipus_incidencia','$data','$comentari','$id_fh')";
				$result = $db->query($sql);
		}
	}
	$pos++;
}

echo json_encode(array('success'=>true));

//mysql_close();
?>