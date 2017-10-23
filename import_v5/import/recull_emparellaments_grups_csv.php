<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:relaciona_grups_materies_alumnes.php
* Autor: Víctor Lino
* Descripció: estableix la relació entre els alumnes i les matèries corresponents a cadascun dels grups
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../bbdd/connect.php');
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
	
    require_once('../../bbdd/connect.php');
    include("../funcions/funcionsCsv.php");
    include("../funcions/func_prof_alum.php");
	

    $recompte=$_POST['recompte'];
    $j = 0;
    for ($i=1;$i<=$recompte;$i++)
        {
        $id_grup=$_POST['id_grup_'.$i];
        $nom_grup=$_POST['nom_grup_'.$i];
        $id_grup_CSV=$_POST['id_grup_CSV_'.$i];
        // Cerquem el seu equivalent dl programa d'horaris

        if ($id_grup_CSV!= "0")
            {
            // Generem un array que enviarem a la funció per actualitzar
            $relacioGrups[$j][0] = $id_grup;
            $relacioGrups[$j][1] = $nom_grup;
            $relacioGrups[$j][2] = $id_grup_CSV;
            $j++;
            }
        }
    actualitzar_alumnat_csv($relacioGrups);    
//	introduir_fase('assig_alumnes',1);
//	$page = "./menu.php";
//	$sec="0";
//	header("Refresh: $sec; url=$page");


?>
</body>
