<?php

require_once('./bbdd/connect.php');
ini_set("display_errors", 1);

$db->exec("set names utf8");
echo "No hauries d'accedir a aquesta p&agrave;gina .....";

// Extreiem el nom del grup
$sql = "SELECT id_seguiment,id_dia_franja, id_grup_materia, seguiment, data ";
$sql .= "FROM qp_seguiment WHERE data >= '2017-09-12' AND data <= '2018-06-02' ; ";
//echo "<br>".$sql;

$result = $db->query($sql);
$comptador = 0;
foreach ($result->fetchAll() as $fila) {
    $seguiment = $fila['seguiment'];
    // Si té contingut no l'hem de mirar
    if ( $seguiment == ""){
		$id_seguiment = $fila['id_seguiment'];
		$dia_franja = $fila['id_dia_franja'];
		$data = $fila['data'];
		$grup_materia = $fila['id_grup_materia'];
    
		// Mirem si el dia de la setmana de la data coincideix amb el dia de la setmana del dia franja
		// Extreiem el dia de la setmana del dia franja
		$sql2 = "SELECT iddies_setmana FROM dies_franges WHERE ";
		$sql2 .= "id_dies_franges = ".$dia_franja.";";
		$result2 = $db->query($sql2);
		$diesSetmanaArr = $result2 ->fetch();
		$diesSetmana = $diesSetmanaArr['iddies_setmana'];
		
		// Estreiem el dia de la setmana de la data
		$timestamp = strtotime($data);
		$diesSetmana2 = date("w",$timestamp);	
		
		// Els comparem i si no coindideixen esborrem la línia del seguiment
		if ( $diesSetmana != $diesSetmana2) {
			$sql3 = "DELETE FROM qp_seguiment WHERE id_seguiment = ".$id_seguiment.";";
			$result3 = $db->query($sql3);
			$comptador++;
		}
		// Comparem ara si el dia franja i grupmateria coincideixen amb alguna unitat classe
		else {
			$sql2 = "SELECT COUNT(idunitats_classe) AS iduc FROM unitats_classe WHERE ";
			$sql2 .= "id_dies_franges = ".$dia_franja." AND idgrups_materies = ".$grup_materia.";";
			$result2 = $db->query($sql2);
			$UCArr = $result2 ->fetch();
			$UC = $UCArr['iduc'];
			if ($UC == 0) {
				$sql3 = "DELETE FROM qp_seguiment WHERE id_seguiment = ".$id_seguiment.";";
				$result3 = $db->query($sql3);
				$comptador++;
			}
			
		}
		
		
	}
}	
echo "S'han esborrat ".$comptador." registres."    
 ?>   
    



