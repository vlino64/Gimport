<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$data     = isset($_REQUEST['data'])     ? $_REQUEST['data']     : '' ;
$idfranges_horaries = isset($_REQUEST['idfranges_horaries'])     ? $_REQUEST['idfranges_horaries']     : '' ;
$idfranges_horaries_ant = isset($_REQUEST['idfranges_horaries_ant']) ? $_REQUEST['idfranges_horaries_ant']:'' ;
$idgrups   = isset($_REQUEST['idgrups'])      ? $_REQUEST['idgrups']    : '' ;
$idmateria = isset($_REQUEST['idmateria'])    ? $_REQUEST['idmateria']  : '' ;
$idprofessors = isset($_SESSION['professor']) ? $_SESSION['professor']  : 0;

$sql    = "DELETE FROM incidencia_alumne WHERE ";
$sql   .= "data='$data' AND ";
$sql   .= "idfranges_horaries=$idfranges_horaries AND ";
$sql   .= "idgrups=$idgrups AND ";
$sql   .= "id_mat_uf_pla=$idmateria AND idprofessors=$idprofessors ";
$result = $db->query($sql);

$rsImportar = getIncidenciasDataFHGrupMateria($db,$data,$idfranges_horaries_ant,$idgrups,$idmateria,$idprofessors);

foreach($rsImportar->fetchAll() as $row) {
	$idalumnes = $row['idalumnes'];
	$idprofessors = $row['idprofessors'];
	$tipus_incidencia = $row['id_tipus_incidencia'];
	$comentari = $row['comentari'];
	
	$sql    = "INSERT INTO incidencia_alumne (idalumnes,idgrups,id_mat_uf_pla,idprofessors,";
	$sql   .= "id_tipus_incidencia,data,idfranges_horaries,comentari) ";
	$sql   .= "VALUES ($idalumnes,$idgrups,$idmateria,$idprofessors,$tipus_incidencia,";
	$sql   .= "'$data',$idfranges_horaries,'$comentari')";
	$result = $db->query($sql);
}

	
if (isset($rsImportar)){
  //mysql_free_result($rsImportar);
}

echo json_encode(array('success'=>true));
//mysql_close();
?>