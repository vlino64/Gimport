<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;
$descripcio_detallada_prof = isset($_REQUEST['descripcio_detallada_prof']) ? $_REQUEST['descripcio_detallada_prof'] : 0 ;

$idalumne             = getCCCEnteredbyAlumne($id)->idalumne;
$idprofessor          = getCCCEnteredbyAlumne($id)->idprofessor;
$idmateria            = getCCCEnteredbyAlumne($id)->idmateria;
$idgrup 		      = getCCCEnteredbyAlumne($id)->idgrup;
$idfranges_horaries   = getCCCEnteredbyAlumne($id)["idfranges_horaries"];
$idespais 		      = getCCCEnteredbyAlumne($id)->idespais;
$data                 = getCCCEnteredbyAlumne($id)["data"];
$descripcio_detallada_alum = getCCCEnteredbyAlumne($id)->descripcio_detallada;

$descripcio_detallada  = "Fets produïts segons l\'alumne".PHP_EOL;
$descripcio_detallada .= $descripcio_detallada_alum.PHP_EOL."\n\n";
$descripcio_detallada .= "Fets produïts segons el professor".PHP_EOL;
$descripcio_detallada .= $descripcio_detallada_prof.PHP_EOL;

$sql  = "INSERT INTO ccc_taula_principal(idalumne,idgrup,idprofessor,idmateria,";
$sql .= "idfranges_horaries,idespais,data,descripcio_detallada) ";
$sql .= "VALUES ($idalumne,$idgrup,$idprofessor,$idmateria,";
$sql .= "$idfranges_horaries,$idespais,'$data','$descripcio_detallada')";
$result = $db->query($sql);
		
$sql    = "DELETE FROM ccc_alumne_principal WHERE idccc_alumne_principal=$id";
$result = $db->query($sql);

include('../ccc_alum/ccc_alum_send.php');

echo json_encode(array('success'=>true));

//mysql_free_result($rec);
//mysql_close();
?>