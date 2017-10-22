<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$idprofessors = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;

if ( $idprofessors == 0 ) {
	$result = 0;
}
else {
	$result = 1;
}

if ($result != 0){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}
?>
