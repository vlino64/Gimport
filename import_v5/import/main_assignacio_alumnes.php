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
require_once('../../pdo/bbdd/connect.php');
include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");
ini_set("display_errors", 1);



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
            select_grups_per_matricular_csv($db);
            }
        if ($_POST['alumnes'] == 1) 
            {
            mostra_grups($exportsagaxml);
            select_grups_per_matricular($exportsagaxml,$db); 
            }
        if ($_POST['alumnes'] == 2) 
            {
            select_grups_per_matricular_csv_materies($exportsagaxml,$db); 
            }            
	
		
?>
</body>

	




