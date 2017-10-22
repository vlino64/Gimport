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

    introduir_fase('grups',0);
    introduir_fase('alumne_grups',0);
    introduir_fase('materies_saga',0);
    introduir_fase('materies_gp',0);
    introduir_fase('lessons',0);
    introduir_fase('assig_alumnes',0);

    if (!extreu_fase('segona_carrega'))
        {
        buidatge('desdegrups');

        // Eliminem tots els carrecs del curs anterior excepte el de superadministrador
        $sql="DELETE FROM professor_carrec WHERE ((idcarrecs='1') OR (idcarrecs='2'));";
        $result=mysql_query($sql);	
        if (!$result) {die(_ERR_DELETE_CARRECS . mysql_error());}

        $sql="DELETE FROM equivalencies WHERE grup_gp!='';";
        $result=mysql_query($sql);	
        if (!$result) 
                {die(_ERR_NETEJANT_EQUIVALENCIES . mysql_error());}
        }

    $recompte = $_POST['recompte'];
    // Carreguem els grups i el seu torn
    for ($i=1;$i<=$recompte;$i++)
        {
        $crea = $_POST['crea_'.$i];
        $id_grup_gp = $_POST['id_grup_gp_'.$i];
        $codi_grup_gp = $_POST['codi_grup_gp_'.$i];
        if ($codi_grup_gp == '') {$codi_grup_gp = $id_grup_gp;}
        $nom_grup_gp = $_POST['name_grup_gp_'.$i];
        if ($nom_grup_gp == '') {$nom_grup_gp = $id_grup_gp;}
        $nom_grup_gp =  neteja_apostrofs($nom_grup_gp);
        $id_torn=$_POST['id_torn_'.$i];
        $id_pla = $_POST['id_pla_'.$i];
        //echo $crea.">>".$id_grup_gp.">>".$nom_grup_gp.">>".$id_torn."<br>";
        if (($id_torn != "0") AND ($id_torn != "") AND $crea)
            {
            $sql="INSERT grups(codi_grup,nom,idtorn) ";
            $sql.="VALUES ('".$id_grup_gp."','".$nom_grup_gp."','".$id_torn."');";
            //echo $sql;
            $result=mysql_query($sql);	
            if (!$result) {die(_ERR_INSERT_GROUPS_1 . mysql_error());}

            //Extreiem l'identificador
            $id_grup=extreu_id('grups','codi_grup','idgrups',$id_grup_gp);    

            //Desem l'emparellament a la taula equivalencies per quan s'hagin de carregat els alumnes i matèries
            $sql="INSERT INTO equivalencies(grup_gp,grup_ga,pla_saga) VALUES ('".$codi_grup_gp."','".$id_grup."','".$id_pla."');";
            $result=mysql_query($sql);	
            if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}				
            }
        }

//	$exportsagaxml=$_SESSION['upload_saga'];
//	$exporthorarixml=$_SESSION['upload_horaris'];

    if (extreu_fase('app_horaris') ==0 ) {crea_agrupaments_GP($exporthorarixml);}
    else if (extreu_fase('app_horaris') == 1) {crea_agrupaments_PN($exporthorarixml);}
    // Els agrupament , amb kronowin i aSc es generaran  al crear les unitats classe.
    //Fer-ho abasn és complicat
    //else if (extreu_fase('app_horaris') == 2) {crea_agrupaments_KW($exporthorarixml);}

    else if (extreu_fase('app_horaris') == 3) {crea_agrupaments_HW($exporthorarixml);}

    introduir_fase('grups',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");

		
?>
</body>
