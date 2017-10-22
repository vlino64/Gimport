<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id           		   = isset($_REQUEST['id'])                   ? $_REQUEST['id'] : 0 ;
$id_falta     		   = isset($_REQUEST['id_falta'])             ? $_REQUEST['id_falta'] : 0 ;
$expulsio     		   = isset($_REQUEST['expulsio'])   		  ? $_REQUEST['expulsio'] : 'N' ;
//$descripcio_breu       = isset($_REQUEST['descripcio_breu'])      ? str_replace("'","\'",$_REQUEST['descripcio_breu']) : '';
$id_motius     		   = isset($_REQUEST['id_motius_nova'])       ? $_REQUEST['id_motius_nova'] : 0 ;
if ($id_motius == 0) {
	$id_motius     	   = isset($_REQUEST['id_motius'])            ? $_REQUEST['id_motius'] : 0 ;
}
$descripcio_detallada  = isset($_REQUEST['descripcio_detallada']) ? str_replace("'","\'",$_REQUEST['descripcio_detallada']) : '';
$id_tipus_sancio       = isset($_REQUEST['id_tipus_sancio'])      ? $_REQUEST['id_tipus_sancio'] : 0 ;
$data_incident         = isset($_REQUEST['data_incident'])        ? $_REQUEST['data_incident'] : 0 ;
$data_inici_sancio     = isset($_REQUEST['data_inici_sancio'])    ? $_REQUEST['data_inici_sancio'] : 0 ;
$data_fi_sancio        = isset($_REQUEST['data_fi_sancio'])       ? $_REQUEST['data_fi_sancio'] : 0 ;

if (isset($_REQUEST['idalumne'])) {
	$idalumne 	       = isset($_REQUEST['idalumne'])         ? $_REQUEST['idalumne']     : 0 ;
	$idprofessor           = isset($_SESSION['professor'])        ? $_SESSION['professor']    : 0 ;
	
	$idunitats_classe      = isset($_REQUEST['idunitats_classe']) ? $_REQUEST['idunitats_classe'] : 0 ;
	if ($idunitats_classe!=0) {
		$idgrups_materies      = getUnitatsClasse($db,$idunitats_classe)["idgrups_materies"];
		$idmateria 	       = getGrupMateria($db,$idgrups_materies)["id_mat_uf_pla"];
		$idgrup 	       = getGrupMateria($db,$idgrups_materies)["id_grups"];
		$id_dies_franges       = getUnitatsClasse($db,$idunitats_classe)["id_dies_franges"];
		$idfranges_horaries    = getDiesFranges($db,$id_dies_franges)["idfranges_horaries"];
		$idespais 	       = getUnitatsClasse($db,$idunitats_classe)["idespais_centre"];
	}
	else {	
		$idmateria          = 0;
		$idgrup             = getGrupAlumne($db,$idalumne)["idgrups"];
		$idfranges_horaries = 0;
		$idespais           = 0;
	}
}

$data_i = substr($data_incident,6,4)."-".substr($data_incident,3,2)."-".substr($data_incident,0,2);
$d_i_s  = substr($data_inici_sancio,6,4)."-".substr($data_inici_sancio,3,2)."-".substr($data_inici_sancio,0,2);
$d_f_s  = substr($data_fi_sancio,6,4)."-".substr($data_fi_sancio,3,2)."-".substr($data_fi_sancio,0,2);

if ($d_i_s == '0000-00-00') {
	//$d_i_s = date("Y-m-d");
}

if ($d_f_s == '0000-00-00') {
	//d_f_s = date("Y-m-d");
}

$sql  = "SELECT id_falta,expulsio,id_motius,descripcio_detallada,id_tipus_sancio,data_inici_sancio,data_fi_sancio ";
$sql .= "FROM ccc_taula_principal ";
$sql .= "WHERE idccc_taula_principal='$id'";


$rec          = $db->query($sql);
$count        = 0;
foreach($rec->fetchAll() as $row) {
	$count++;
}

if ($count == 0) {
		$sql    = "DELETE FROM ccc_taula_principal WHERE idccc_taula_principal=$id";
		$result = $db->query($sql);
		
		$sql    = "INSERT INTO ccc_taula_principal (idalumne,idgrup,idprofessor,idmateria,idfranges_horaries,idespais,data,hora,id_falta,expulsio,id_motius,descripcio_detallada,id_tipus_sancio,data_inici_sancio,data_fi_sancio) ";
		$sql   .= "VALUES ('$idalumne','$idgrup','$idprofessor','$idmateria','$idfranges_horaries','$idespais','$data_i','".date("H:i")."','$id_falta','$expulsio','$id_motius','$descripcio_detallada','$id_tipus_sancio','$d_i_s','$d_f_s')";
		$result = $db->query($sql);
				
		include('../ccc/ccc_send.php');
		
		if (isset($rsCarrecs)) {
			//mysql_free_result($rsCarrecs);
		}
		if (isset($rsProfessorsCarrec)) {
			//mysql_free_result($rsProfessorsCarrec);
		}
		
}
else {  
		$sql    = "UPDATE ccc_taula_principal SET id_falta='$id_falta',expulsio='$expulsio',id_motius='$id_motius',descripcio_detallada='$descripcio_detallada',data='$data_i',id_tipus_sancio='$id_tipus_sancio' WHERE idccc_taula_principal='$id'";
		$result = $db->query($sql);
}

echo json_encode(array('success'=>true));

/*if ($result != 0){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}*/

//mysql_free_result($rec);
//mysql_close();
?>