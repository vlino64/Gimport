<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$idincidencia_professor = intval($_REQUEST['id']);

$sql  = "select * from incidencia_professor where idincidencia_professor='$idincidencia_professor'";
$rs   = $db->query($sql);
foreach($rs->fetchAll() as $item) {
    $idprofessors       = $item['idprofessors'];
    $idfranges_horaries = $item['idfranges_horaries'];
    $data               = $item['data'];
    $any                = substr($data,0,4);
    $mes                = substr($data,5,2);
    $dia                = substr($data,8,2);
}

$sTempFileName_noextension = '../feina_guardies/'.$any.$mes.$dia."_".$idprofessors."_".$idfranges_horaries;
foreach (glob($sTempFileName_noextension."*.*") as $filename) {
    unlink($filename);
}

$sql = "DELETE FROM incidencia_professor WHERE idincidencia_professor=$idincidencia_professor";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>