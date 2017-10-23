<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:prof-act.php
* Autor: Víctor Lino
* Descripció: Actualització o càrrega de professorat
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../bbdd/connect.php');
include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");


session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
	header("location: ../login/access-denied.php");
	exit();
	}

    $exportsagaxml=$_SESSION['upload_saga'];
//    $exporthorarixml=$_SESSION['upload_horaris'];

//    $resultatconsulta3=simplexml_load_file($exportsagaxml);

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">

<!-- this script got from www.javascriptfreecode.com-Coded by: Krishna Eydat -->
<HTML>
<HEAD>
<!--START OF PopUp on Button Click-->



</head>

<body>

<?php


        if ($_POST['alumnes'] == 0) 
            {
            emparella_grups_actualitzacio_csv();
            
//            $page = "./assignacions_act_csv.php";
//            $sec="0";
//            header("Refresh: $sec; url=$page");             
            
            }
        if ($_POST['alumnes'] == 1) 
            {
            actualitzar_alumnat($exportsagaxml); 
            }




    
      

//    if ($_POST['automatricula'] AND extreu_fase('app_horaris')!=5)
//        {
////        introduir_fase('assig_alumnes',1);
////	$page = "./menu.php";
////	$sec="0";
////	header("Refresh: $sec; url=$page");
//        }
//   else
//        {
//        $auto=$_POST['automatricula'];
//        
//        }
   
   
	
		
?>
</body>

	




