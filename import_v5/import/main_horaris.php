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
include("../funcions/func_horaris.php");
include("../funcions/func_espais_franges.php");
include("../funcions/func_grups_materies.php");
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

//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php

    $exportsagaxml=$_SESSION['upload_saga'];
    $exporthorarixml=$_SESSION['upload_horaris'];
    $app= extreu_fase('app_horaris',$db);
    switch ($app)
        {
        case 0:
            crea_horaris_gp_mixt($exportsagaxml,$exporthorarixml,$db);
            break;
        case 1:
            crea_horaris_PN_mixt($exportsagaxml,$exporthorarixml,$db);
            break;
        case 2:
            crea_horaris_KW_mixt($exportsagaxml,$exporthorarixml,$db);
            break;
        case 3:
            crea_horaris_HW_mixt($exportsagaxml,$exporthorarixml,$db);
            break;
        case 4:
            crea_horaris_ASC_mixt($db);
            break;
        }
		
?>
</body>

	




