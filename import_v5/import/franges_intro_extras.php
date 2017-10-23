<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:franges_intro.php
* Autor: Víctor Lino
* Descripció: Carrega les franges horaries i les assigna als dies
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
<title>C&aacute;rrega autom&aacute;tica GPUntis</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>

<?php

    require_once('../../bbdd/connect.php');

    $recompte=$_POST['recompte'];

    // Extreiem el periode escolar actual
    $sql="SELECT idperiodes_escolars FROM periodes_escolars WHERE actual='S';";
    //echo $sql;echo "<br>";
    $result=mysql_query($sql);
    if (!$result) 
        {
        die(_ERR_SELECT_PERIODS . mysql_error());
        }
    $periode_escolar=mysql_result($result,0);

    $sql="CREATE TABLE IF NOT EXISTS `franges_tmp` (`id_xml_horaris` int(11) NOT NULL,`id_taula_franges` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $result=mysql_query($sql);
    if (!$result) 
        {
        die(_ERR_CREATE_FRANGES_TMP_TABLE . mysql_error());
        }


    $sql="SELECT MAX(idfranges_horaries) FROM franges_horaries;";
    $result=mysql_query($sql);if (!$result) {die(COMPTA_FRANGES.mysql_error());}
    $franges=mysql_result($result,0);
    //echo $franges."<br>";
    $j=1;
    for ($i=0;$i<$recompte;$i++)
	{
	//$codi_franja=$_POST['id_codi_'.$i];
        //if (extreu_fase('app_horaris')==1) {$codi_franja++;}
	$inici=$_POST['inici_'.$i];
	$fi=$_POST['fi_'.$i];
	$esbarjo=$_POST['esbarjo_'.$i];
        $id_torn=$_POST['id_torn_'.$i];
	for ($k=0;$k<count($id_torn);$k++)
            {
            if ($id_torn != 0)
                {
		// Inserim en una taula temporal que, al crar les unitat classe farà la conversió a l'id real de la franja
		$codi_tmp=$franges+$j;
                $j++;
                
                $sql="INSERT INTO `franges_horaries`(idfranges_horaries,activada,esbarjo,hora_inici,hora_fi,idtorn) ";
                $sql.="VALUES ";
                $sql.="('".$codi_tmp."','S',";
                if ($esbarjo=="1") {$sql.="'S',";}
                else {$sql.="' ',";}
                $sql.="'".$inici."','".$fi."','".$id_torn."');";
                //echo "<br>".$sql;
                $result=mysql_query($sql);
                if (!$result) 
                    {
                    die(_ERR_INSERT_PERIODS . mysql_error());
                    }
                // Assignem cada franja als dies corresponents
                $sql="SELECT iddies_setmana FROM dies_setmana WHERE laborable='S';";
                $result=mysql_query($sql);
                if (!$result) 
                    {
                    die(_ERR_SELECT_DIES . mysql_error());
                    }
                while ($fila1=mysql_fetch_row($result))
                    {
                    $sql3="INSERT INTO dies_franges(iddies_setmana,idfranges_horaries,idperiode_escolar) ";
                    $sql3.="VALUES ('".$fila1[0]."','".$codi_tmp."','".$periode_escolar."');";
                    //echo $sql3;
                    $result3=mysql_query($sql3);
                    if (!$result3) 
                       {
                       die(_ERR_INSERT_DIES_FRANGES . mysql_error());
                       }
                    }
                }
            }
         }
        introduir_fase('franges',1);
        introduir_fase('dies_espais_franges',1);
	$page = "./menu.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
?>


</body>





