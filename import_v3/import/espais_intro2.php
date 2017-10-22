<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:espais_intro.php
* Autor: Víctor Lino
* Descripció: Carrega els espais
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include("../config.php");
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
<title>C&aacute;rrega autom&aacute;tica GPUntis</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
   

	$recompte=$_POST['recompte'];
   //echo $recompte;
	for ($i=1;$i<=$recompte;$i++)
		{
		$codi_espai=$_POST['espaiid_'.$i];
		$espai_nom=$_POST['espainom_'.$i];
		$espaicheck=$_POST['espaicheck_'.$i];
		echo $codi_espai." >> ".$espai_nom." >> ".$espaicheck."<br>";;
		if ($espaicheck==1)
			{
			$sql="INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
			$sql.="VALUES ('".$codi_espai."','S','".$espai_nom."');";
			//echo $sql."<br>";
			$result=mysql_query($sql);
			if (!$result) 
				{
				die(_ERR_INSERT_ROOMS . mysql_error());
				}
			}

		}
        introduir_fase('espais',1);

   
	$page = "./main_dies_franges_espais.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
?>


</body>





