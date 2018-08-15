<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:franges_intro.php
 * Autor: Víctor Lino
 * Descripció: Carrega les franges horaries i les assigna als dies
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/func_espais_franges.php");
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
        <title>C&aacute;rrega autom&aacute;tica GPUntis</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>

        <?php
        $recompte = $_POST['recompte'];
        if (isset($_POST['mesfranges'])) {
            $mes_franges = $_POST['mesfranges'];
        } else {
            $mes_franges = 0;
        }

// Extreiem el periode escolar actual
        $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual='S';";
//echo $sql;echo "<br>";
        $result = $db->prepare($sql);
        $result->execute();
        $periode_escolar_arr = $result->fetch();
        $periode_escolar = $periode_escolar_arr['idperiodes_escolars'];

        $sql = "CREATE TABLE IF NOT EXISTS `franges_tmp` (`id_xml_horaris` int(11) NOT NULL,`id_taula_franges` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM franges_tmp";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "SELECT COUNT(*) AS count FROM franges_horaries;";
        $result = $db->prepare($sql);
        $result->execute();
        $franges_arr = $result->fetch();
        $franges = $franges_arr['count'];
        $j = 1;
        for ($i = 1; $i <= $recompte; $i++) {
            $codi_franja = $_POST['id_codi_' . $i];

            // Si es tracta de Peñalara
            if (extreu_fase('app_horaris', $db) == 1) {
                $codi_franja++;
            }

            // Si es tracta de kronowin
            if (extreu_fase('app_horaris', $db) == 2) {
                $inici = $_POST['inicih_' . $i] . ":" . $_POST['inicim_' . $i] . ":00";
                $fi = $_POST['fih_' . $i] . ":" . $_POST['fim_' . $i] . ":00";
            } else {
                $inici = $_POST['inici_' . $i];
                $fi = $_POST['fi_' . $i];
            }

            if (isset($_POST['esbarjo_' . $i])) {
                $esbarjo = $_POST['esbarjo_' . $i];
            } else {
                $esbarjo = 0;
            }

            $id_torn = $_POST['id_torn_' . $i];
            if (count($id_torn) != 0) {
                // Inserim en una taula temporal que, al crar les unitat classe farà la conversió a l'id real de la franja
                $codi_tmp = $franges + $j;
                if ((isset($_POST['fran_parella_' . $i])) && ($_POST['fran_parella_' . $i] != 0) && (extreu_fase('segona_carrega'))) {
                    $codi_tmp = $_POST['fran_parella_' . $i];
                }
                $j++;
                $sql = "INSERT INTO `franges_tmp`(id_xml_horaris,id_taula_franges) ";
                $sql .= "VALUES ('" . $codi_franja . "','" . $codi_tmp . "');";
                $result = $db->prepare($sql);
                $result->execute();


                if ((!extreu_fase('segona_carrega', $db)) || ((isset($_POST['fran_parella_' . $i])) && ($_POST['fran_parella_' . $i] == 0) && (isset($_POST['crea_franja_' . $i])) && ($_POST['crea_franja_' . $i] != 0))) {
                    for ($k = 0; $k < count($id_torn); $k++) {
                        //echo $id_torn[$k]."<br>";
                        if ($id_torn[$k] != 0) {
                            $sql = "INSERT INTO `franges_horaries`(idfranges_horaries,activada,esbarjo,hora_inici,hora_fi,idtorn) ";
                            $sql .= "VALUES ";
                            $sql .= "('" . $codi_tmp . "','S',";
                            if ($esbarjo == "1") {
                                $sql .= "'S',";
                            } else {
                                $sql .= "' ',";
                            }
                            $sql .= "'" . $inici . "','" . $fi . "','" . $id_torn[$k] . "');";

                            $result = $db->prepare($sql);
                            $result->execute();
                            // Assignem cada franja als dies corresponents
                            $sql = "SELECT iddies_setmana FROM dies_setmana WHERE laborable='S';";
                            $result = $db->prepare($sql);
                            $result->execute();
                            foreach ($result->fetchAll() as $fila1) {
                                $sql3 = "INSERT INTO dies_franges(iddies_setmana,idfranges_horaries,idperiode_escolar) ";
                                $sql3 .= "VALUES ('" . $fila1['iddies_setmana'] . "','" . $codi_tmp . "','" . $periode_escolar . "');";
                                $result3 = $db->prepare($sql3);
                                $result3->execute();
                            }
                            if ($k < count($id_torn) - 1) {
                                $j++;
                                $codi_tmp++;
                            } // En el cas en que tingui diferents trons simultanis
                            // Em va augmentant j per anar incrementar l'identificador de la franja
                        }
                    }
                }
            }
        }
        introduir_fase('franges', 1, $db);
        $exporthorarixml = $_SESSION['upload_horaris'];
        if ($mes_franges) {//cerca_altres_franges_gp($exporthorarixml);
            $page = "./franges_addicionals.php";
            $sec = "0";
            header("Refresh: $sec; url=$page");
        } else {
            if ((extreu_fase('espais', $db) == 1) && (extreu_fase('dies_setmana', $db) == 1) && (extreu_fase('franges', $db) == 1)) {
                introduir_fase('dies_espais_franges', 1, $db);

                $page = "./menu.php";
                $sec = "0";
                header("Refresh: $sec; url=$page");
            }
        }
        ?>


    </body>





