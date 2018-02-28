<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:grups_act.php
 * Autor: Víctor Lino
 * Descripció: Carrega els grups del fitxer de saga
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/func_grups_materies.php");
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
        introduir_fase('alumne_grups', 0, $db);
        introduir_fase('materies_saga', 0, $db);
        introduir_fase('materies_gp', 0, $db);
        introduir_fase('lessons', 0, $db);
        introduir_fase('assig_alumnes', 0, $db);

// Modificacions a la base de dades
        $sql = "ALTER TABLE  `plans_estudis` CHANGE  `Acronim_pla_estudis`  `Acronim_pla_estudis` VARCHAR( 20 ) ";
        $sql .= "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "ALTER TABLE  `plans_estudis` CHANGE  `Nom_plan_estudis`  `Nom_plan_estudis` VARCHAR( 80 ) ";
        $sql .= "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
        $result = $db->prepare($sql);
        $result->execute();

// Comprovem si el camp codi_modul existeix. Si no existeix, el crearem i afegirem un valor per defecte
        $sql = "SHOW COLUMNS FROM `moduls` LIKE 'codi_modul'";
        $result = $db->prepare($sql);
        $result->execute();
        $exists = $result->rowCount() == 1 ? TRUE : FALSE;
        if (!$exists) {
            $sql = "ALTER TABLE  `moduls` ADD  `codi_modul` VARCHAR( 30 ) NOT NULL ";
            $result = $db->prepare($sql);
            $result->execute();
        }

        if (!extreu_fase('segona_carrega', $db)) {
            //Netegem tot el que va després
            buidatge('desdemateries', $db);
        }

        $id_pla = extreu_id('plans_estudis', 'Nom_plan_estudis', 'idplans_estudis', 'ESO LOE', $db);
        $exportsagaxml = $_SESSION['upload_saga'];
        $exporthorarixml = $_SESSION['upload_horaris'];

        if ((extreu_fase('app_horaris', $db) >= 0) AND ( extreu_fase('app_horaris', $db) <= 3)) {
            $resultatconsulta = simplexml_load_file($exporthorarixml);
            if (!$resultatconsulta) {
                echo "Carrega fallida";
            } else {
                echo "<br>Carrega correcta";
                // Si carregues des de gpuntis
                if (extreu_fase('app_horaris', $db) == 0) {
                    // Des de GPuntis
                    intro_mat_GP($resultatconsulta, $id_pla, $db);
                } else if (extreu_fase('app_horaris', $db) == 1) {
                    // Des de peñalara
                    intro_mat_PN($resultatconsulta, $id_pla, $db);
                } else if (extreu_fase('app_horaris', $db) == 2) {
                    // Des de kronowin
                    intro_mat_KW($resultatconsulta, $id_pla, $db);
                } else if (extreu_fase('app_horaris', $db) == 3) {
                    // Des de Horwin
                    intro_mat_HW($resultatconsulta, $id_pla, $db);
                }
            }
        } else {
            // Des de ASC
            $materies = array();
            $materies = extreuMateriesCsv();
            alta_materies($materies, $id_pla, $db);
        }


        introduir_fase('materies', 1, $db);
        $page = "./menu.php";
        $sec = "0";
        header("Refresh: $sec; url=$page");
        ?>
    </body>
