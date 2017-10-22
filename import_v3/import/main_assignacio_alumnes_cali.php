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
include("../config.php");
include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");


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

//function llistatAlumnes($exportsagaxml,$id_grup)
//    {
//    $cadena= '';
//    $resultatconsulta=simplexml_load_file($exportsagaxml);
//    $resultatconsulta2=simplexml_load_file($exportsagaxml);  
//    
//    foreach ($resultatconsulta -> grups -> grup as $grup)
//        {
//        //echo "<br>".$id_grup." >>> ".$grup[id];
//        if (!strcmp($id_grup,$grup[id]))
//            {
//            foreach ($grup -> alumnes -> alumne as $alumne)
//                {
//                //echo "<br>".$alumne[id];
//                foreach ($resultatconsulta2 -> alumnes -> alumne as $alumne2)
//                    {
//                    if (!strcmp($alumne[id],$alumne2[id]))
//                        {
//                        $cadena = $cadena."<br>".$alumne2[cognom1]." ".$alumne2[cognom2].", ".$alumne2[nom];
//                        break;
//                        }
//                    }
//                }
//            }
//        }    
//    return  $cadena;    
//    }
?>
    
    
    
	

<?php


    //mostra_grups($exportsagaxml);
      

    if ($_POST['automatricula'] AND extreu_fase('app_horaris')!=5)
        {
        introduir_fase('assig_alumnes',1);
	$page = "./menu.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
        }
   else
        {
        $auto=$_POST['automatricula'];
        select_grups_per_matricular_cali($exportsagaxml);
        }
   
   
	
		
?>
</body>

	




