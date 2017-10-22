<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups_materies = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0;
$idmateria        = getGrupMateria($db,$idgrups_materies)["id_mat_uf_pla"];
$escicleloe       = isMateria($db,$idmateria) ? 0 : 1 ;
$curs_escolar     = getCursActual($db)["idperiodes_escolars"];

$data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
  if ($data_inici=='--') {
  	  $data_inici = getCursActual($db)["data_inici"];
  }
 
$data_fi    = isset($_REQUEST['data_fi']) ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2) : date("Y-m-d");
  if ($data_fi=='--') {
  	  $data_fi = date("Y-m-d");
  }

if ($escicleloe) {
	$data_inici = getGrupMateria($db,$idgrups_materies)["data_inici"];
   	$data_fi    = getGrupMateria($db,$idgrups_materies)["data_fi"];	
}

if ($idgrups_materies!=0){
	$dies = sessions_grup_materia($db,$data_inici,$data_fi,$idgrups_materies,$curs_escolar);
}

echo json_encode($dies);

//mysql_close();
?>