<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id                    = isset($_REQUEST['id'])                   ? $_REQUEST['id'] : 0 ;
$descripcio_detallada  = isset($_REQUEST['descripcio_detallada']) ? str_replace("'","\'",$_REQUEST['descripcio_detallada']) : '';
$data_incident         = isset($_REQUEST['data_incident'])        ? $_REQUEST['data_incident'] : 0 ;

if (isset($_REQUEST['idalumne'])) {
	$idalumne 	       = isset($_REQUEST['idalumne'])         ? $_REQUEST['idalumne'] : 0 ;
	$idprofessor           = isset($_REQUEST['idprofessor'])      ? $_REQUEST['idprofessor'] : 0 ;
	$idunitats_classe      = isset($_REQUEST['idunitats_classe']) ? $_REQUEST['idunitats_classe'] : 0 ;
	
	if ($idunitats_classe != 0) {
		$idgrups_materies      = getUnitatsClasse($db,$idunitats_classe)["idgrups_materies"];
		$idmateria 	       = getGrupMateria($db,$idgrups_materies)["id_mat_uf_pla"] ;
		$idgrup 	       = getGrupMateria($db,$idgrups_materies)["id_grups"];
		$id_dies_franges       = getUnitatsClasse($db,$idunitats_classe)["id_dies_franges"];
		$idfranges_horaries    = getDiesFranges($db,$id_dies_franges)["idfranges_horaries"];
		$idespais 	       = getUnitatsClasse($db,$idunitats_classe)["idespais_centre"];
	}
	else if ($idunitats_classe == 0){
		$idgrups_materies      = 0;
		$idmateria 	       = 0;
		$idgrup 	       = getGrupAlumne($db,$idalumne)["idgrups"];
		$id_dies_franges       = 0;
		$idfranges_horaries    = 0;
		$idespais 	       = 0;
	}
}

$data_i = substr($data_incident,6,4)."-".substr($data_incident,3,2)."-".substr($data_incident,0,2);

/*$sql  = "SELECT descripcio_detallada ";
$sql .= "FROM ccc_alumne_principal ";
$sql .= "WHERE idccc_taula_principal='$id'";*/

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

/*$rec          = $db->query($sql);
$count        = 0;
while($row = mysql_fetch_object($rec)) {
	$count++;
}*/

if ($id == 0) {
		$sql    = "DELETE FROM ccc_alumne_principal WHERE idccc_alumne_principal=$id";
		$result = $db->query($sql);
		
		$sql    = "INSERT INTO ccc_alumne_principal (idalumne,idgrup,idprofessor,idmateria,idfranges_horaries,idespais,data,descripcio_detallada) ";
		$sql   .= "VALUES ('$idalumne','$idgrup','$idprofessor','$idmateria','$idfranges_horaries','$idespais','$data_i','$descripcio_detallada')";
		$result = $db->query($sql);
		
		include('../alum_automat/alum_automat_send_ccc.php');
		
}
else { 
		if (isset($_REQUEST['action']) && $_REQUEST['action']=='UPDATE' ) {
			$idgrup    = $_REQUEST['idgrup'];
			$idmateria = $_REQUEST['idmateria'];
			$idespais  = $_REQUEST['idespais'];
		}
		$sql    = "UPDATE ccc_alumne_principal SET idgrup='$idgrup',idmateria='$idmateria',idespais='$idespais',descripcio_detallada='$descripcio_detallada' WHERE idccc_taula_principal='$id'";
		$result = $db->query($sql);
}

echo json_encode(array('success'=>true));

/*if ($result != 0){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}*/

if (isset($rec)){
  //mysql_free_result($rec);
}
//mysql_close();
?>
