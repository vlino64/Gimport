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

   
        <form enctype="multipart/form-data" action="./main_assignacio_alumnes.php" method="post" name="selectAlumnes">
        <br><br><br>        
        <table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
            <tr><td align="center"><p><h3>Assigna els alumnes als seus grups i matèries</h3><br></td></tr>
            <tr><td align="center"><p><b><br>Indica d'on vols extreure aquesta informació<br></h3><br></b></td></tr>
                <tr><td align="center"><input type="radio" name="alumnes" value="1" id="alumnes_1" /> Carreguem des del fitxer de SAGA (Exportació per PDA del curs actual)</td></tr>
                <tr><td align="center"><input type="radio" name="alumnes" value="0" id="alumnes_0" /> Carreguem des del fitxer csv exportat de SAGA que conté alumnes i grups</td></tr>
                <tr><td align="center"><input type="radio" name="alumnes" value="2" id="alumnes_2" /> Carreguem des del fitxer csv exportat de SAGA que conté matèries (Experimental 2018/19)</td></tr>
                <tr><td align="center"><br><input name="boton" type="submit" id="boton" value="Envia la configuració"></td></tr>
        </table>
        </form>        
    

</body>

	




