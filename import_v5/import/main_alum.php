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

//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function marcar(source) 
    {	
		var patt1 ="alta";
        checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
        for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
        {
            if((checkboxes[i].type == "checkbox") && (checkboxes[i].name.indexOf(patt1) != -1))
            {
                checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
            }
        }
    }
</script>

</head>

<body>

<?php 
    if (!isset($_POST['alumnes']))
        {
        ?>
    
        <form enctype="multipart/form-data" action="./main_alum.php" method="post" name="selectAlumnes">
        <br><br><br>        
        <table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
            <tr><td align="center"><p><h3>Carrega o actualitza els alumnes:</h3><br></td></tr>
            <tr><td align="center"><p><b><br>Indica d'on vols extreure els alumnes<br></h3><br></b></td></tr>
                <tr><td align="center"><input type="radio" name="alumnes" value="0" id="alumnes_0" /> Carreguem des del fitxer csv</td></tr>
                <tr><td align="center"><input type="radio" name="alumnes" value="1" id="alumnes_1"  /> Carreguem des del fitxer de SAGA</td></tr>
                <tr><td align="center"><br><input name="boton" type="submit" id="boton" value="Envia la configuració"></td></tr>
        </table>
        </form>        
    
<?php
        }
    else 
        {
        if ($_POST['alumnes'] == 0) {altaAlumne($db); }
        if ($_POST['alumnes'] == 1) {select_alumnat($db); }
        }
		
?>
</body>

	




