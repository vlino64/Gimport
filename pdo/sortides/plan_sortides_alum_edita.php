<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_sortida = isset($_SESSION['sortida'])   ? $_SESSION['sortida']   : 0 ;		
$idalumnes  = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;
$afegir     = isset($_REQUEST['afegir'])    ? $_REQUEST['afegir']    : 0 ;

foreach ($idalumnes as $id_alumne) {
	if ($id_alumne != 0){
		//esborrar incidencia per aquell professor i dia
		$sql = "DELETE FROM sortides_alumne WHERE id_sortida='$id_sortida' AND id_alumne='$id_alumne'";
		$result = $db->query($sql);

		if ($afegir == 1) {
				//insertar falta asistencia per aquell professor i dia
				$sql  = "INSERT INTO sortides_alumne (id_sortida,id_alumne) ";
				$sql .= "VALUES ('$id_sortida','$id_alumne')";
				$result = $db->query($sql);
		}
	}
}

echo json_encode(array('success'=>true));

//mysql_close();
?>