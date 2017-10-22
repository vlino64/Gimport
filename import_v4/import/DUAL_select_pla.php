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
include("../funcions/func_grups_materies.php");

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
	
      
   if (extreu_fase('modalitat_fitxer')==2)   
      {
      // Comprovem siel pla ESO_BAT ja està introduit
      $id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,'ESO LOE');
      if ($id_pla=='')
         {
         // Introduim un pla fictici
         $sql="INSERT IGNORE INTO plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) VALUES ('S','ESO_BAT','ESO_BAT');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) {die(_ERR_INSERT_PLAN_MULTIPLE . mysql_error());}
         $id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,'ESO_BAT');
         }
      }
   
   $recompte=$_POST['recompte'];
//   if (extreu_fase('app_horaris')==0)
//      // Des de GPuntis
//      {
      $j=0;$k=0;
      for ($i=1;$i<=$recompte;$i++)
        {
        //echo "<br>>>>".$i." >>> ".$recompte;
        $crea=$_POST['crea_'.$i];
        $check=$_POST['es_CCFF_LOE_'.$i];
        $codi_materia=$_POST['codi_mat_'.$i];
        $codi_materia=neteja_apostrofs($codi_materia);
        $nom_materia=$_POST['nom_mat_'.$i];
        $nom_materia=neteja_apostrofs($nom_materia);
			         
        if ($crea)
            {
            if ($check)
                {
                $materia[$j][0]= $codi_materia;
                $materia[$j][1]= $nom_materia;
                $j++;

                }   
            else
                {
                // $moduls conté les opcions que no s'han marcat i que per tan es tracta de mòduls
                $moduls[$k][0]= $codi_materia;
                $moduls[$k][1]= $nom_materia;
                //echo $moduls[$k][0]." >> ".$moduls[$k][1];
                $k++;
                }   
        
            }    
        }
        alta_materies($materia,$id_pla);
        sort($moduls);
        alta_moduls($moduls);

	
?>
</body>


