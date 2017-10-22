<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_grups         = isset($_REQUEST['id_grups'])         ? $_REQUEST['id_grups']         : 0 ;
$idgrups_materies = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0 ;
$id_dies_franges  = isset($_REQUEST['id_dies_franges'])  ? $_REQUEST['id_dies_franges']  : 0 ;
$idespais_centre  = isset($_REQUEST['idespais_centre'])  ? $_REQUEST['idespais_centre']  : 0 ;
$afegir           = isset($_REQUEST['afegir'])           ? $_REQUEST['afegir']           : 0 ;

foreach ($idgrups_materies as $id_gr) {
    $pos = 0;
	foreach ($id_dies_franges as $id_fh) {
		$id_ec = $idespais_centre[$pos];
		
		if ($id_gr != 0){
			$sql = "DELETE FROM unitats_classe WHERE id_dies_franges='$id_fh' AND idespais_centre='$id_ec' AND idgrups_materies='$id_gr'";
			$result = $db->query($sql);
			
			if ($afegir == 1) {
				$sql  = "INSERT INTO unitats_classe (id_dies_franges,idespais_centre,idgrups_materies) ";
				$sql .= "VALUES ('$id_fh','$id_ec','$id_gr')";
				$result = $db->query($sql);	
				
			}	
		}
		$pos++;	
	}
}

echo json_encode(array('success'=>true));
//mysql_close();
?>