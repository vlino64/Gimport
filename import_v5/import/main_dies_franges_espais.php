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
include("../funcions/func_espais_franges.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");

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
   
   // #############################
   // aquesta pàgina es carrega vàries vegades en funció dels continguts que s'hagin ja introduit
   // ###########################
	
    include("../config.php");

    $exportsagaxml=$_SESSION['upload_saga'];
    $exporthorarixml=$_SESSION['upload_horaris'];

    // Ja es fa la neteja de tot el que correspon a dies, franges i espais
    if (!extreu_fase('segona_carrega')) 
        {
        buidatge(desdediesfrangesespais);
    
        // INTRODUCCIÓ DELS ESPAIS, FRANGES I DIES
        $sql="INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
        $sql.="VALUES ('NOROOM','S','NOROOM');";
        //echo "<br>".$sql;
        $result=mysql_query($sql);if (!$result) {die(Inserint_noroom_espais.mysql_error());}
        if (!$result) 
            {
            die(_ERR_INSERT_ROOMS . mysql_error());
            }

        carrega_dies($exporthorarixml);
        
        }
    introduir_fase('dies_setmana',1);    
    if (!extreu_fase('segona_carrega'))
        {
        $app = extreu_fase('app_horaris');
        
        switch ($app)
            {
            case 0:
                espais_intro_GP($exporthorarixml);
                formulari_franges_GP($exporthorarixml);
                break;
            case 1:
                espais_intro_PN($exporthorarixml);
                formulari_franges_PN($exporthorarixml);
                break;
            case 2:
                espais_intro_KW($exporthorarixml);
                //formulari_franges_KW($exporthorarixml);
                introduir_fase('franges',1);
                introduir_fase('espais',1);
                introduir_fase('dies_espais_franges',1);
                carregaFrangesDiesKW();
                $page = "./menu.php";
                $sec="0";
                header("Refresh: $sec; url=$page");                
                break;
            case 3:
                espais_intro_HW($exporthorarixml);
                formulari_franges_HW($exporthorarixml);
                break;
            case 4:
                introduir_fase('franges',1);
                introduir_fase('espais',1);
                introduir_fase('dies_setmana',1);
                introduir_fase('dies_espais_franges',1);
                carregaFrangesDies();
                $page = "./menu.php";
                $sec="0";
                header("Refresh: $sec; url=$page");
                break;
            }
        
        
        
        }
    else
        {
        $app = extreu_fase('app_horaris');
        
        switch ($app)
            {
            case 0:
                if (!extreu_fase('espais')) {form_espais2_gp($exporthorarixml);}
                if ((extreu_fase('espais')) AND (!extreu_fase('franges'))) {formulari_franges_GP($exporthorarixml);}
                break;
            case 1:
                if (!extreu_fase('espais')) {form_espais2_gp($exporthorarixml);}
                if ((extreu_fase('espais')) AND (!extreu_fase('franges'))) {formulari_franges_PN($exporthorarixml);}
                break;
            case 2:
//                espais_intro_KW($exporthorarixml);
//                formulari_franges_KW($exporthorarixml);                
                break;
            case 3:
//                espais_intro_HW($exporthorarixml);
//                formulari_franges_HW($exporthorarixml);                
                break;
            }        
        }
        
  
//         else
//            {espais_intro_gp($exporthorarixml);}
//         }   
//     if (!extreu_fase('segona_carrega')) 
//         {
//         if (!extreu_fase('dies_setmana')) {carrega_dies_penalara($exporthorarixml);}
//         }
//      else
//         {introduir_fase('dies_setmana',1);}
//		if ((!extreu_fase('franges'))&&(extreu_fase('espais'))) {formulari_franges_penalara($exporthorarixml);}		
//      }
//	// Si ja està tot introduit, marxem de nou a menú
//
//        }
   
   
?>
</body>

	




