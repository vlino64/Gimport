<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups            = isset($_REQUEST['idgrups'])     	     ? $_REQUEST['idgrups']            : 0 ;
$id_mat_uf_pla      = isset($_REQUEST['id_mat_uf_pla'])      ? $_REQUEST['id_mat_uf_pla']      : 0 ;
$grup_materia       = existGrupMateria($db,$idgrups,$id_mat_uf_pla);
$idprofessors 	    = isset($_SESSION['professor'])          ? $_SESSION['professor']          : 0 ;
$idfranges_horaries = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
$data_llista        = date("Y-m-d");

if (! existLogProfessorDataFranjaGrupMateria($db,$idprofessors,TIPUS_ACCIO_PASALLISTAGUARDIA,date("Y-m-d"),$idfranges_horaries,$grup_materia)) {
    $log = insertaLogProfessorExtended($db,$idprofessors,TIPUS_ACCIO_PASALLISTAGUARDIA,$data_llista,$idfranges_horaries,$grup_materia);
}

//if (! existsGuardiaSignada($idprofessors,$idfranges_horaries,$data,$id_mat_uf_pla,$idgrups)) {
	$sql    = "DELETE FROM guardies_signades WHERE idprofessors=$idprofessors AND idgrups=$idgrups ";
	$sql   .= "AND id_mat_uf_pla=$id_mat_uf_pla AND idfranges_horaries=$idfranges_horaries AND data='$data'";
	$result = $db->query($sql);
	
	$sql     = "INSERT INTO guardies_signades (idprofessors,id_mat_uf_pla,idgrups,idfranges_horaries,data) ";
	$sql    .= "VALUES ($idprofessors,$id_mat_uf_pla,$idgrups,$idfranges_horaries,'$data')";
	$result  = $db->query($sql);
	
	if ($result){
	    echo json_encode(array('success'=>true));
	} else {
            echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}
//}

//mysql_close();
?>