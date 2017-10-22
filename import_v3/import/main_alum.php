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

	include("../config.php");

	$exportsagaxml=$_SESSION['upload_saga'];
	$exporthorarixml=$_SESSION['upload_horaris'];
	$fase=extreu_fase('carrega');
   
   // 16/17 Afegim data naixement
   // Comporvem que no existeix
   $sql = "SELECT COUNT(dada_contacte) FROM tipus_contacte WHERE dada_contacte = 'Data de naixement';";
   $result=mysql_query($sql);
	if (!$result) {die(_ERR_INSERT_ALUM . mysql_error());}
   $fila=mysql_fetch_row($result);$present=$fila[0];
   
   if (!$present)
      {
      $sql="INSERT INTO tipus_contacte (`idtipus_contacte` ,`dada_contacte` ,`Nom_info_contacte`) VALUES (NULL ,  'Data de naixement',  'data_naixement');";   
      $result=mysql_query($sql);
      if (!$result) {die(_ERR_INSERT_ALUM . mysql_error());}
      }
      
      
   // Final bloc data de naixement 
   
	select_alumnat($exportsagaxml,$fase);
	
		
?>
</body>

	




