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
include("../funcions/func_horaris_cali.php");
include("../funcions/func_horaris.php");
include("../funcions/func_espais_franges.php");
include("../funcions/func_grups_materies.php");
include("../funcions/funcions_generals.php");

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

    require_once('../../bbdd/connect.php');
    
    carrega_CCFF_de_SAGA($db);
    
    genera_horaris_cali();
		
?>
</body>

	




