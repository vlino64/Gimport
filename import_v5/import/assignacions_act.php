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
	

    $recompte=$_POST['recompte'];
    $exportsagaxml=$_SESSION['upload_saga'];
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    if ( !$resultatconsulta ) {echo "Carrega fallida";}
    else 
	{
	echo "<br>Carrega correcta";
	for ($i=1;$i<=$recompte;$i++)
            {
            $id_grup=$_POST['id_grup_'.$i];
            $nom_grup=$_POST['nom_grup_'.$i];
            $id_grup_saga=$_POST['id_grup_saga_'.$i];
            // Cerquem el seu equivalent dl programa d'horaris
				
            if ($id_grup_saga!= "0")
                {
                //echo "<br>".$id_grup." >> ".$nom_grup." >> ".$id_grup_saga ;
                //Treiem totes les materies del grup
                $sql = "SELECT id_mat_uf_pla,idgrups_materies FROM grups_materies WHERE id_grups = '".$id_grup."';";
                $result=mysql_query($sql); if (!$result) {die(_ERR_SELECT_SUBJECTS_GROUP.mysql_error());}
                while ($fila=mysql_fetch_row($result))
                    {
                    foreach($resultatconsulta -> grups -> grup as $grup)
                        {
                        if (!strcmp($grup[id],$id_grup_saga))
                            {
                            foreach ($grup ->alumnes->alumne as $alumne)
                                {
                                $sql = "SELECT id_alumne FROM contacte_alumne WHERE id_tipus_contacte = 3 AND Valor = '".$alumne[id]."';";
                                $result3=mysql_query($sql); if (!$result3) {die(_ERR_SELECT_STUDENT.mysql_error());}
                                $fila3=mysql_fetch_row($result3);
                                    
                                if ($fila3[0] != '')    
                                    {
                                    $sql4="INSERT alumnes_grup_materia(idalumnes,idgrups_materies) ";
                                    $sql4.="VALUES ('".$fila3[0]."','".$fila[1]."');";
                                    //echo ">>>>".$sql4."<br>";
                                    $result4=mysql_query($sql4);	
                                    if (!$result4) {die(_ERR_INSERT_GROUPS_SUBJECTS_PUPIL . mysql_error());}
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
	introduir_fase('assig_alumnes',1);
	$page = "./menu.php";
	$sec="0";
	header("Refresh: $sec; url=$page");
	}

?>
</body>
