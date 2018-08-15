<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:relaciona_grups_materies_alumnes.php
 * Autor: Víctor Lino
 * Descripció: estableix la relació entre els alumnes i les matèries corresponents a cadascun dels grups
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
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
        $relacio_grups = array();

        $recompte = $_POST['recompte'];
// Generem un array amb la relació entre els grups
        for ($i = 1; $i <= $recompte; $i++) {
            $id_grup = $_POST['id_grup_' . $i];
            $nom_grup = $_POST['nom_grup_' . $i];
            $grup_csv = $_POST['id_grup_saga_' . $i];

            $relacio_grups[$i - 1][0] = $id_grup;
            $relacio_grups[$i - 1][1] = $grup_csv;
        }

        $alumnat = extreuAlumnatCsv();
        for ($l = 1; $l < count($alumnat); $l++) {
            $grup1 = $alumnat[$l][0];
            $grup2 = $alumnat[$l][1];
            $grup3 = $alumnat[$l][2];
            $idAlumne = $alumnat[$l][3];

            if ($idAlumne != "") {
                if ($grup1 != "") {
                    foreach ($relacio_grups as $grup) {
                        if (!strcmp($grup[1], $grup1)) {
                            matricula($idAlumne, $grup[0], $db);
                        }
                    }
                }
                if ($grup2 != "") {
                    foreach ($relacio_grups as $grup) {
                        if (!strcmp($grup[1], $grup2)) {
                            matricula($idAlumne, $grup[0], $db);
                        }
                    }
                }
                if ($grup3 != "") {
                    foreach ($relacio_grups as $grup) {
                        if (!strcmp($grup[1], $grup3)) {
                            matricula($idAlumne, $grup[0], $db);
                        }
                    }
                }
            }
        }
        introduir_fase('assig_alumnes', 1, $db);
        $page = "./menu.php";
        $sec = "0";
        header("Refresh: $sec; url=$page");

        function matricula($idAlumne, $id_grup, $db) {

            // Extreiem l'id de l'alumne a l'aplicació
            $sql = "SELECT idalumnes FROM alumnes WHERE codi_alumnes_saga = '" . $idAlumne . "';";
            $result = $db->prepare($sql);
            $result->execute();
            $fila = $result->fetch();
            $idAlumne = $fila['idalumnes'];

            //Treiem totes les materies del grup
            if ($idAlumne != "") {
                $sql = "SELECT id_mat_uf_pla,idgrups_materies FROM grups_materies WHERE id_grups = '" . $id_grup . "';";
                //echo ">".$sql."<br>";
                $result = $db->prepare($sql);
                $result->execute();
                foreach ($result->fetchAll() as $fila) {
                    $sql4 = "INSERT alumnes_grup_materia(idalumnes,idgrups_materies) ";
                    $sql4 .= "VALUES ('" . $idAlumne . "','" . $fila['idgrups_materies'] . "');";
                    //echo ">>>>".$sql4."<br>";
                    $result4 = $db->prepare($sql4);
                    $result4->execute();
                }
            }
        }
        ?>
    </body>
