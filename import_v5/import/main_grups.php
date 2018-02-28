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

//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");
?>
<html>
    <head>
        <title>Càrrega automàtica SAGA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>

    <body>

        <?php
        $exportalumnescsv = $_SESSION['upload_alumnes'];
        $exporthorarixml = $_SESSION['upload_horaris'];
        $exportsagaxml = $_SESSION['upload_saga'];
        if (!isset($_POST['grups'])) {
            if (extreu_fase('app_horaris', $db) != 5) {
                relaciona_grups_torns($exportalumnescsv, $exporthorarixml, $db);
            } else {
                ?>

                <form enctype="multipart/form-data" action="./main_grups.php" method="post" name="selectAlumnes">
                    <br><br><br>        
                    <table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
                        <tr><td align="center"><p><h3>Carrega els grups:</h3><br></td></tr>
                        <tr><td align="center"><input type="radio" name="grups" value="0" id="alumnes_0" /> Carreguem des del fitxer csv d'alumnes</td></tr>
                        <tr><td align="center"><input type="radio" name="grups" value="1" id="alumnes_1"  /> Carreguem des del fitxer de SAGA</td></tr>
                        <tr><td align="center"><input name="boton" type="submit" id="boton" value="Envia la configuració"></td></tr>
                    </table>
                </form>        

                <?php
            }
        } else {


            if ($_POST['grups'] == 0) {
                relaciona_grups_torns_csv($db);
            }
            if ($_POST['grups'] == 1) {
                relaciona_grups_torns_sol_saga($exportsagaxml, $db);
            }
        }
        ?>
    </body>    







