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
include("../funcions/funcions_generals.php");
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
        $recompte = $_POST['recompte'];
        // Netegem relacions del curs passat
        $sql = "DELETE FROM equivalencies WHERE materia_saga IS NOT NULL AND materia_gp IS NOT NULL";
        $result = $db->prepare($sql);
        $result->execute();

        for ($i = 1; $i <= $recompte; $i++) {
            $id_pla = $_POST['id_pla_' . $i];
            if (strpos($id_pla, 'XXX') !== false) {
                $dades = explode("XXX", $id_pla);
                $id_pla = $dades[0];
                $id_mod_saga = $dades[1];
            } else {
                $id_pla = $_POST['id_pla_' . $i];
                //$nom_pla=$_POST['nom_pla_'.$i];
                $id_mod_saga = $_POST['id_modul_' . $i];
                //$nom_mod_saga=$_POST['nom_modul_'.$i];
            }

            $nom_mod_gp = neteja_apostrofs($_POST['nom_modul_gp_' . $i]);
            //echo "<br>".$nom_mod_gp;
            if ($nom_mod_gp != "0") {
                $sql = "INSERT INTO equivalencies(pla_saga,materia_saga,materia_gp) VALUES ('" . $id_pla . "','" . $id_mod_saga . "','" . $nom_mod_gp . "')";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
        introduir_fase('materies', 1, $db);

        $page = "./menu.php";
        $sec = "0";
        header("Refresh: $sec; url=$page");
        ?>
    </body>
