<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$curs_escolar     = getCursActual($db)["idperiodes_escolars"];
$tipus_registre   = isset($_REQUEST['tipus_registre']) ? $_REQUEST['tipus_registre'] : 'PRIMER';
$data   = isset($_REQUEST['data']) ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d"); 
$hora             = date("H:i");
$dia_setmana      = date('w', strtotime($data));

$rsProfessors     = getProfessorsActius($db,TIPUS_nom_complet);
$professors_array = array();
$professors_count = 0;

foreach($rsProfessors->fetchAll() as $row) {
	$hora_entrada  = getHoraEntradaProfessorDia($db,$row["idprofessors"],$data,$curs_escolar);
	$hora_sortida  = getHoraSortidaProfessorDia($db,$row["idprofessors"],$data,$curs_escolar);
	
	include_once('../ctrl_prof/comprova_altres_hores.php');
	
	$professors_count++;
	$registre_sortida      = 0;
	$txt_registre_sortida  = 'N';
	$hora_registre_sortida = '00:00';
	
		// Ja es hora d'entrar al centre
		// Falta esbrinar si s'ha fet el registre d'entrada
		
		if (existLogProfessorData($db,$row["idprofessors"],TIPUS_ACCIO_SURTODELCENTRE,$data)) {
			$registre_sortida      = 1;
			$txt_registre_sortida  = 'S';
			
			if ($tipus_registre == 'PRIMER') {
				$hora_registre_sortida = substr(getFirstLogProfessor($db,$row["idprofessors"],$data,TIPUS_ACCIO_SURTODELCENTRE),0,5);
			}
			else if ($tipus_registre == 'DARRER') {
				$hora_registre_sortida = substr(getLastLogProfessor($db,$row["idprofessors"],$data,TIPUS_ACCIO_SURTODELCENTRE),0,5);
			}
		}
		
		if (($registre_sortida==0) || ($hora_sortida > $hora_registre_sortida)) {
			if ($hora_registre_sortida == '00:00') {
				$hora_registre_sortida = '';
			}
			
			array_push($professors_array,array(
				"professor"        => $row["Valor"],
				"hora_entrada"	   => $hora_entrada,
				"hora_sortida"	   => $hora_sortida,
				"registre_sortida" => $txt_registre_sortida,
				"hora_registre_sortida" => $hora_registre_sortida));
		}
		
	

}
  
echo json_encode($professors_array);

//mysql_free_result($rsProfessors);
//mysql_close();
?>
