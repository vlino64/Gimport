<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_professor     = isset($_REQUEST['id_professor']) ? $_REQUEST['id_professor'] : 0 ;
$idgrups_materies = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0 ;
$afegir           = isset($_REQUEST['afegir']) ? $_REQUEST['afegir'] : 0 ;

foreach ($idgrups_materies as $id_gr) {
		
		if ($id_gr != 0){
			//esborrar grup_materia
			$sql = "DELETE FROM prof_agrupament WHERE idprofessors=$id_professor AND idagrups_materies='$id_gr'";
			$result = $db->query($sql);
			
			if ($afegir == 1) {
				//insertar grup_materia
				$sql  = "INSERT INTO prof_agrupament (idprofessors,idagrups_materies) ";
				$sql .= "VALUES ('$id_professor','$id_gr')";
				$result = $db->query($sql);
			}		
		}
}

echo json_encode(array('success'=>true));

//mysql_close();
?>