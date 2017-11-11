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

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">

<HTML>
<HEAD>
</head>

<body>

<?php


        if ($_POST['alumnes'] == 0) 
            {
            emparella_grups_actualitzacio_csv();
            }
        if ($_POST['alumnes'] == 1) 
            {
            actualitzar_alumnat($exportsagaxml); 
            }
		
?>
</body>

	




