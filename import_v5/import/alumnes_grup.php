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
</head>

<body>
<?php

    $id_grup = $_GET['idgrup'];
    $cadena= '';
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    $resultatconsulta2=simplexml_load_file($exportsagaxml);  
    
    foreach ($resultatconsulta -> grups -> grup as $grup)
        {
        if (!strcmp($id_grup,$grup['id']))
            {
            foreach ($grup -> alumnes -> alumne as $alumne)
                {
                foreach ($resultatconsulta2 -> alumnes -> alumne as $alumne2)
                    {
                    if (!strcmp($alumne[id],$alumne2['id']))
                        {
                        echo "<br>".$alumne2['cognom1']." ".$alumne2['cognom2'].", ".$alumne2['nom'];
                        break;
                        }
                    }
                }
            }
        }    
    
    
?>

</body>

	




