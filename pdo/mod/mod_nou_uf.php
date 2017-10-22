<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_moduls       = $_REQUEST['id_moduls'];
$idplans_estudis = $_REQUEST['idplans_estudis'];
$nom_uf          = str_replace("'","\'",$_REQUEST['nom_uf']);
$hores           = $_REQUEST['hores'];
$data_inici      = substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2);
$data_fi         = substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2);
$activat         = isset($_REQUEST['activat']) ? $_REQUEST['activat'] : 'N';
$curs_escolar    = getCursActual($db)["idperiodes_escolars"];
//$automatricula   = $_REQUEST['automatricula'];

// Insertem a moduls_materies_ufs
//$sql = "insert into moduls_materies_ufs (idplans_estudis,hores_finals,Curs,automatricula) values ('$idplans_estudis','$hores','$curs_escolar','$automatricula')";
$sql = "insert into moduls_materies_ufs (idplans_estudis,hores_finals,Curs,codi_materia,activat) values ('$idplans_estudis','$hores','$curs_escolar','$nom_uf','$activat')";
$result = $db->query($sql);

$sql_nova_uf    = "select moduls_materies_ufs.id_mat_uf_pla from moduls_materies_ufs order by 1 desc limit 0,1";
$result_nova_uf = $db->query($sql_nova_uf);

foreach($result_nova_uf->fetchAll() as $row) {
        $idunitats_formatives = $row["id_mat_uf_pla"];
}

// Insertem a unitats_formatives
$sql = "insert into unitats_formatives (idunitats_formatives,nom_uf,hores,data_inici,data_fi) values ('$idunitats_formatives','$nom_uf','$hores','$data_inici','$data_fi')";
$result = $db->query($sql);

// Insertem a moduls_ufs
$sql = "insert into moduls_ufs (id_moduls,id_ufs) values ('$id_moduls','$idunitats_formatives')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
