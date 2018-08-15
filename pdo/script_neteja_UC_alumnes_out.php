<?php

/*
TASQUES PENDENTS

   */

require_once('./bbdd/connect.php');

$arr_alumnes = array();
$fp = fopen("log.txt","a");


$db->exec("set names utf8");
echo "No hauries d'accedir a aquesta p&agrave;gina .....";
		
	// Extreiem el nom del grup
	$sql = "SELECT idalumnes ";
	$sql .= "FROM alumnes WHERE activat = 'N' ; ";
	echo "<br>";

	$result=$db->query($sql); if (!$result) {die(_SELECT_NOM_GRUP.mysqli_error($conn));}    
	foreach ($result->fetchAll() as $fila){
		$alumne = $fila['idalumnes'];

		$sql2 = "DELETE FROM alumnes_grup_materia WHERE idalumnes = ".$alumne.";";
		echo "<br>".$sql2;
		$result2=$db->query($sql2); if (!$result2) {die(_UPDATE_DATA_NAIXEMENT.mysqli_error($conn));}
		
		
	}
			
			
?>


