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
include("../config.php");
include("../funcions/funcions_generals.php");


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
	introduir_fase('app_horaris',$geisoft);
        if (extreu_fase('app_horaris')==5) {unlink('../uploads/pujat_horaris.xml');}
	
	$segona=$_POST['segona'];
        if ( $segona == "") {$segona = 0;}
	introduir_fase('segona_carrega',$segona);
        if (extreu_fase('segona_carrega') AND (extreu_fase('carrega')==1)) 
            {
            introduir_fase('alumnat',1);introduir_fase('professorat',0);introduir_fase('grups',0);
            introduir_fase('materies',0);introduir_fase('dies_espais_franges',0);introduir_fase('dies_setmana',1);
            introduir_fase('franges',0);introduir_fase('espais',0);introduir_fase('lessons',1);
            introduir_fase('assig_alumnes',0);
            }
   
	$fitxers=$_POST['fitxerorg'];
        if ( $fitxers == "") {$fitxers = 0;}
	introduir_fase('modalitat_fitxer',$fitxers);
	
	$aprofitar=$_POST['carrega2'];
        if ( $aprofitar == "") {$aprofitar = 0;}
	introduir_fase('aprofitar_horaris',$aprofitar);
	
	// Si es tracta d'una segona càrrega, desmarca tots els passos excepte alumnat
	if ((extreu_fase('segona_carrega')) && (extreu_fase('carrega')!=2)) 
		{
		introduir_fase('professorat',0);introduir_fase('grups',0);
                introduir_fase('alumne_grups',0);
		introduir_fase('materies',0);introduir_fase('dies_espais_franges',0);introduir_fase('dies_setmana',0);
		introduir_fase('franges',0);introduir_fase('espais',0);introduir_fase('lessons',0);introduir_fase('assig_alumnes',0);
		}
	
        $tmp_name = $_FILES["archivo"]["tmp_name"];
	if ($tmp_name =="")
		{
		echo "Utilitzarem un fitxer ESO carregat anteriorment.<br>";
		$_SESSION['upload_horaris'] = '../uploads/pujat_horaris.xml';
		}
	else
		{
		echo "<br>";
		$tmp_name = $_FILES["archivo"]["tmp_name"];
		$exporthorarixml="../uploads/pujat_horaris.xml";
		$_SESSION['upload_horaris'] = '../uploads/pujat_horaris.xml';
		move_uploaded_file($tmp_name,$exporthorarixml);
		
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

	




