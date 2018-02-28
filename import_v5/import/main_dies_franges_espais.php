<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:prof-act.php
 * Autor: Víctor Lino
 * Descripció: Actualització o càrrega de professorat
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/func_espais_franges.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");
ini_set("display_errors", 1);



session_start();
//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
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

        $exportsagaxml = $_SESSION['upload_saga'];
        $exporthorarixml = $_SESSION['upload_horaris'];

// Ja es fa la neteja de tot el que correspon a dies, franges i espais
        if (!extreu_fase('segona_carrega', $db)) {
            buidatge('desdediesfrangesespais', $db);

            // INTRODUCCIÓ DELS ESPAIS, FRANGES I DIES
            $sql = "INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
            $sql .= "VALUES ('Sense determinar','S','Sense determinar');";
            //echo "<br>".$sql;
            $result = $db->prepare($sql);
            $result->execute();

            carrega_dies($exporthorarixml, $db);
        }
        introduir_fase('dies_setmana', 1, $db);
        if (!extreu_fase('segona_carrega', $db)) {
            $app = extreu_fase('app_horaris', $db);
            switch ($app) {
                case 0:
                    espais_intro_GP($exporthorarixml, $db);
                    formulari_franges_GP($exporthorarixml, $db);
                    break;
                case 1:
                    espais_intro_PN($exporthorarixml, $db);
                    formulari_franges_PN($exporthorarixml, $db);
                    break;
                case 2:
                    espais_intro_KW($exporthorarixml, $db);
                    //formulari_franges_KW($exporthorarixml);
                    introduir_fase('franges', 1, $db);
                    introduir_fase('espais', 1, $db);
                    introduir_fase('dies_espais_franges', 1, $db);
                    carregaFrangesDiesKW($db);

                    $page = "./menu.php";
                    $sec = "0";
                    header("Refresh: $sec; url=$page");
                    break;
                case 3:
                    espais_intro_HW($exporthorarixml, $db);
                    formulari_franges_HW($exporthorarixml, $db);
                    break;
                case 4:
                    introduir_fase('franges', 1, $db);
                    introduir_fase('espais', 1, $db);
                    introduir_fase('dies_setmana', 1, $db);
                    introduir_fase('dies_espais_franges', 1, $db);
                    carregaFrangesDies($db);

                    $page = "./menu.php";
                    $sec = "0";
                    header("Refresh: $sec; url=$page");
                    break;
            }
        } else {
            $app = extreu_fase('app_horaris', $db);

            switch ($app) {
                case 0:
                    if (!extreu_fase('espais', $db)) {
                        form_espais2_gp($exporthorarixml, $db);
                    }
                    if ((extreu_fase('espais', $db)) AND ( !extreu_fase('franges'))) {
                        formulari_franges_GP($exporthorarixml,$db);
                    }
                    break;
                case 1:
                    if (!extreu_fase('espais', $db)) {
                        form_espais2_gp($exporthorarixml, $db);
                    }
                    if ((extreu_fase('espais', $db)) AND ( !extreu_fase('franges', $db))) {
                        formulari_franges_PN($exporthorarixml,$db);
                    }
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
        ?>
    </body>






