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
require_once('../../bbdd/connect.php');
include("../funcions/func_grups_materies.php");
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
	
	introduir_fase('alumne_grups',0);
	introduir_fase('materies_saga',0);
	introduir_fase('materies_gp',0);
	introduir_fase('lessons',0);
	introduir_fase('assig_alumnes',0);	
	
	// Modificacions a la base de dades
	$sql="ALTER TABLE  `plans_estudis` CHANGE  `Acronim_pla_estudis`  `Acronim_pla_estudis` VARCHAR( 20 ) ";
	$sql.="CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
	$result=mysql_query($sql);	
	if (!$result) 
		{die(_ERR_MODIFY_PLA_ESTUDIS_FIELD . mysql_error());}	
	
	$sql="ALTER TABLE  `plans_estudis` CHANGE  `Nom_plan_estudis`  `Nom_plan_estudis` VARCHAR( 80 ) ";
	$sql.="CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
	$result=mysql_query($sql);	
	if (!$result) 
		{die(_ERR_MODIFY_NAME_PLA_ESTUDIS_FIELD . mysql_error());}	
	
	// Comprovem si el camp codi_modul existeix. Si no existeix, el crearem i afegirem un valor per defecte
	$result = mysql_query("SHOW COLUMNS FROM `moduls` LIKE 'codi_modul'");
	$exists = (mysql_num_rows($result))?TRUE:FALSE;
	if (!$exists)
                        {
                        $sql="ALTER TABLE  `moduls` ADD  `codi_modul` VARCHAR( 30 ) NOT NULL ";
                        $result=mysql_query($sql);
                        if (!$result) {	die(_ERR_CREATING_HIST . mysql_error());}
                        }
	
	if (!extreu_fase('segona_carrega')) 
		{
		//Netegem tot el que va després
		buidatge('desdemateries');
		}

//       if ((extreu_fase('modalitat_fitxer')==0) && (!extreu_fase('segona_carrega')))   
//          {
//          // Comprovem siel pla ESO_BAT ja està introduit
//          $id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,'ESO_BAT');
//          if ($id_pla=='')
//             {
//             // Introduim un pla fictici
//             $sql="INSERT IGNORE INTO plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) VALUES ('S','ESO_BAT','ESO_BAT');";
//             //echo $sql."<br>";
//             $result=mysql_query($sql);	
//             if (!$result) {die(_ERR_INSERT_PLAN_MULTIPLE . mysql_error());}
//             }
//          }                
//                
                
                
                
        $id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,'ESO LOE');
        $exportsagaxml=$_SESSION['upload_saga'];
        $exporthorarixml=$_SESSION['upload_horaris'];

          // Càrrega de matèries des del programa d'horaris
          if(extreu_fase('app_horaris')==5)
             {
             // No hi ha eina d'horaris i carreguem matèries des de SAGA
             }
          else
             {
             $resultatconsulta=simplexml_load_file($exporthorarixml);
             if ( !$resultatconsulta ) {echo "Carrega fallida";}
             else 
                {
                echo "<br>Carrega correcta";
                // Si carregues des de gpuntis
                if (extreu_fase('app_horaris')==0)
                // Des de GPuntis
                    {    
                    intro_mat_GP($resultatconsulta,$id_pla);
                    }
                else if (extreu_fase('app_horaris')==1)
                // Des de peñalara
                   {
                   intro_mat_PN($resultatconsulta,$id_pla);
                   }
                 else if (extreu_fase('app_horaris')==2)
                // Des de peñalara
                   {
                   intro_mat_KW($resultatconsulta,$id_pla);
                   }                       
                 else if (extreu_fase('app_horaris')==3)
                // Des de peñalara
                   {
                   intro_mat_HW($resultatconsulta,$id_pla);
                   }                          

                   }
             }

	
	
	
//	
	introduir_fase('materies', 1);	
	$page = "./menu.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
	
?>
</body>
