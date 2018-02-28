<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:menu.php
* Autor: Víctor Lino
* Descripció: Presenta diiferents menús de càrrega en funció de la selecció d'una opció o una altra
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../pdo/bbdd/connect.php');
include("../funcions/funcions_generals.php");
ini_set("display_errors",1);


session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
	header("location: ../login/access-denied.php");
	exit();
	}

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>



<?php

	$geisoft=$_POST['geisoft'];//echo "<br>>>> ".$geisoft;
	introduir_fase('app_horaris',$geisoft,$db);
        if (extreu_fase('app_horaris',$db)==5) {unlink('../uploads/pujat_horaris.xml');}
	
	$segona=$_POST['segona'];
        if ( $segona == "") {$segona = 0;}
	introduir_fase('segona_carrega',$segona,$db);
        if (extreu_fase('segona_carrega',$db) AND (extreu_fase('carrega',$db)==1)) 
            {
            introduir_fase('alumnat',1,$db);introduir_fase('professorat',0,$db);introduir_fase('grups',0,$db);
            introduir_fase('materies',0,$db);introduir_fase('dies_espais_franges',0,$db);introduir_fase('dies_setmana',1,$db);
            introduir_fase('franges',0,$db);introduir_fase('espais',0,$db);introduir_fase('lessons',1,$db);
            introduir_fase('assig_alumnes',0,$db);
            }
        
        // Totes les càrregues a partir de 2018/19 seran duals
        // Es carrega el valor a la base de dades    
	introduir_fase('modalitat_fitxer',2,$db);
	
	$aprofitar=$_POST['carrega2'];
        echo $aprofitar." >> ".$_POST['carrega2'];
        if ( $aprofitar == "") {$aprofitar = 0;}
	introduir_fase('aprofitar_horaris',$aprofitar,$db);
	
	// Si es tracta d'una segona càrrega, desmarca tots els passos excepte alumnat
	if ((extreu_fase('segona_carrega',$db)) && (extreu_fase('carrega',$db)!=2)) 
		{
		introduir_fase('professorat',0,$db);introduir_fase('grups',0,$db);
                introduir_fase('alumne_grups',0,$db);
		introduir_fase('materies',0,$db);introduir_fase('dies_espais_franges',0,$db);introduir_fase('dies_setmana',0,$db);
		introduir_fase('franges',0,$db);introduir_fase('espais',0,$db);introduir_fase('lessons',0,$db);introduir_fase('assig_alumnes',0,$db);
		}
	
        $tmp_name = $_FILES["archivo"]["tmp_name"];
	if ($tmp_name =="")
		{
		echo "Utilitzarem un fitxer carregat anteriorment.<br>";
		$_SESSION['upload_horaris'] = '../uploads/pujat_horaris.xml';
		}
	else
		{
		echo "<br>";
		$tmp_name = $_FILES["archivo"]["tmp_name"];
		$exporthorarixml="../uploads/pujat_horaris.xml";
		$_SESSION['upload_horaris'] = '../uploads/pujat_horaris.xml';
		move_uploaded_file($tmp_name,$exporthorarixml);
                $today = date("d-m-Y");$time = date("H-i-s");
                $newname = $exporthorarixml."_".$today."_".$time;
                if (!copy($exporthorarixml, $newname)) {
                    echo "failed to copy";
                }		
		//Netegem el fitxer d'apostrofs
		$str=implode("\n",file('../uploads/pujat_horaris.xml'));
		$fp=fopen('../uploads/pujat_horaris.xml','w');
		//$str=str_replace('\'',' ',$str);
		$find[]='&apos;';
		$replace[]=' ';
		$str=str_replace($find,$replace,$str);
		//$str=preg_replace('/\'/', '', $str);
		//now, TOTALLY rewrite the file
		fwrite($fp,$str,strlen($str));
		}
	
	die("<script>location.href = './menu.php'</script>");

?>

</body>

	




