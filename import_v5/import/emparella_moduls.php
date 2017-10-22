<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:grups_act.php
* Autor: Víctor Lino
* Descripció: Carrega els grups del fitxer de saga
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
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php

	include("../config.php");
	
	$recompte=$_POST['recompte'];
        //echo $recompte."<br>";
	// Carreguem els grups i el seu torn

	//Extreiem dada d'inici i data fi inicial de les unitats formatives
	//$sql="SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S'";
	//$result=mysql_query($sql);	if (!$result) {die(_ERR_SELECT_PERIODE . mysql_error());}
	//$data_inici=mysql_result($result,0);
	//$data_fi=mysql_result($result,1);

        // Netegem relacions del curs passat
        $sql = "DELETE FROM equivalencies WHERE materia_saga IS NOT NULL AND materia_gp IS NOT NULL";
        $result=mysql_query($sql);
        if (!$result) {die(_ERR_DELETE_EQUIV_OLD.mysql_error());}        
        
	for ($i=1;$i<=$recompte;$i++)
            {
            $id_pla=$_POST['id_pla_'.$i];
            if (strpos($id_pla,'XXX') !== false)
                {
                 $dades=explode("XXX",$id_pla);
                 $id_pla=$dades[0];
                 $id_mod_saga=$dades[1];
                 }    
            else
                 {
                 $id_pla=$_POST['id_pla_'.$i];
                //$nom_pla=$_POST['nom_pla_'.$i];
                 $id_mod_saga=$_POST['id_modul_'.$i];
                 //$nom_mod_saga=$_POST['nom_modul_'.$i];
                 }

            $nom_mod_gp =  neteja_apostrofs($_POST['nom_modul_gp_'.$i]);
            //echo "<br>".$nom_mod_gp;
            $sql="INSERT INTO equivalencies(pla_saga,materia_saga,materia_gp) VALUES ('".$id_pla."','".$id_mod_saga."','".$nom_mod_gp."')";
            //echo "<br>".$sql;
            $result=mysql_query($sql);
            if (!$result) {die(_ERR_INSERT_EQUIV3.mysql_error());}
            }
        introduir_fase('materies',1);
			
	$page = "./menu.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
	
?>
</body>
