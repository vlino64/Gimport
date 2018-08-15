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
	$sql = "SELECT Valor,id_alumne ";
	$sql .= "FROM contacte_alumne WHERE id_tipus_contacte = 28 ; ";
	

	$result=$db->query($sql); if (!$result) {die(_SELECT_NOM_GRUP.mysqli_error($conn));}    
	foreach ($result->fetchAll() as $fila){
		$data = $fila['Valor'];
		$idAlumne = $fila['id_alumne'];
		$dataArr = explode('/',$data);
		
		if ((strlen($dataArr[2]) == 2) && ($dataArr[2] > 70)) {$dataArr[2]="19".$dataArr[2];}
		if ((strlen($dataArr[2]) == 2) && ($dataArr[2] < 70)) {$dataArr[2]="20".$dataArr[2];}
		$data = $dataArr[0]."/".$dataArr[1]."/".$dataArr[2];
		
		$sql2 = "UPDATE contacte_alumne SET Valor = '".$data."' ";
		$sql2 .= "WHERE id_tipus_contacte = 28 AND id_alumne = ".$idAlumne." ; ";
		//echo "<br>".$sql2;
		$result2=$db->query($sql2); if (!$result2) {die(_UPDATE_DATA_NAIXEMENT.mysqli_error($conn));}
		
		
	}
			
			
?>


