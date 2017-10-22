<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0  ;
$op = isset($_REQUEST['op'])  ? $_REQUEST['op']         : '' ;

$sql = "update professors set activat='$op' where idprofessors=$id";
$result = $db->query($sql);

if ($op=='N') {
	// Esborrem totes les assignacions prèvies del professor actiu, per tal que 
	// no interfereixi amb els horaris
	$sql    = "delete from prof_agrupament where idprofessors=$id";
	$result = $db->query($sql);
	
	$sql    = "delete from professor_carrec where idprofessors=$id";
	$result = $db->query($sql);
	
	$sql    = "delete from guardies where idprofessors=$id";
	$result = $db->query($sql);
        
        $sql    = "delete from prof_atencions where idprofessors=$id";
	$result = $db->query($sql);
        
        $sql    = "delete from prof_coordinacions where idprofessors=$id";
	$result = $db->query($sql);
        
        $sql    = "delete from prof_direccio where idprofessors=$id";
	$result = $db->query($sql);
        
        $sql    = "delete from prof_permanencies where idprofessors=$id";
	$result = $db->query($sql);
        
        $sql    = "delete from prof_reunions where idprofessors=$id";
	$result = $db->query($sql);
}

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

?>