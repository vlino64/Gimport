<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

function uploadFile($id_professor,$idfranges_horaries,$any,$mes,$dia) {
	$allowedExts = array("gif", "jpeg", "jpg", "png","doc","xls","ppt","pdf","docx","xlsx","pptx","rar","zip","odt","odp","ods","odg");
	$temp        = explode(".", $_FILES["file"]["name"]);
	$extension   = end($temp);
        
	$resultat = 1;
	                  
	if (($_FILES["file"]["size"] < 2000000)
	&& in_array($extension, $allowedExts)) {
            
	  if ($_FILES["file"]["error"] > 0) {
		echo "Retorna codi: " . $_FILES["file"]["error"] . "<br>";
		$resultat = 0;
	  } else {
		/*echo "Pujat: " . $_FILES["file"]["name"] . "<br>";
		echo "Tipus: " . $_FILES["file"]["type"] . "<br>";
		echo "Mesura: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";*/
		
		$sTempFileName = '../feina_guardies/'.$any.$mes.$dia."_".$id_professor."_".$idfranges_horaries.".".$extension;
		$sTempFileName_noextension = '../feina_guardies/'.$any.$mes.$dia."_".$id_professor."_".$idfranges_horaries;
		//@unlink($sTempFileName_noextension."_*.*");
		
		foreach (glob($sTempFileName_noextension."*.*") as $filename) {
    		@unlink($filename);
		}

		if (file_exists($sTempFileName)) {
		  move_uploaded_file($_FILES["file"]["tmp_name"],$sTempFileName);
		  //echo $_FILES["file"]["name"] . " ja existeix. ";
		} else {
		  move_uploaded_file($_FILES["file"]["tmp_name"],$sTempFileName);
		  //echo "Enregistrat en: " . "upload/" . $sTempFileName;
		}
                  
	  }
	} else {
	  //echo "Fitxer inv&agrave;lid";
	  $resultat = 0;
	}
	return $resultat;
}


$id_professor       = $_REQUEST['id_professor'];
$idfranges_horaries = $_REQUEST['idfranges_horaries'];
$data               = isset($_REQUEST['data']) ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d");
$any                = substr($data,0,4);
$mes                = substr($data,5,2);
$dia                = substr($data,8,2);

$resultat           = uploadFile($id_professor,$idfranges_horaries,$any,$mes,$dia);

if ($resultat) {
	echo '<img src="./images/task_complete.png" width=30 height=30 />';
}
else {
	echo '<img src="./images/task_incomplete.png" width=30 height=30 />';
}
?>
        