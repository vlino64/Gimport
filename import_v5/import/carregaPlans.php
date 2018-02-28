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
include("../funcions/funcions_generals.php");

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
$exportsagaxml = $_SESSION['upload_saga'];
$exporthorarixml = $_SESSION['upload_horaris'];

$resultatconsulta = simplexml_load_file($exportsagaxml);
if (!$resultatconsulta) {
    echo "Carrega fallida";
} else {
    foreach ($resultatconsulta->{'plans-estudi'}->{'pla-estudis'} as $pla) {
//            echo $exportsagaxml." >> ".$exporthorarixml;
        $pla[nom] = neteja_apostrofs($pla[nom]);
        $acronim = $pla[etapa] . "(" . $pla[subetapa] . ")";
//            echo "<br>".$pla[nom]." >> ".$pla[etapa];
        $id_pla = extreu_id(plans_estudis, Acronim_pla_estudis, idplans_estudis, $acronim,$db);
        if ($id_pla == '') {
            $sql = "INSERT plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) ";
            $sql .= "VALUES ('S','" . $pla[nom] . "','" . $pla[etapa] . "(" . $pla[subetapa] . ")');";
            $result = $db->prepare($sql);
            $result->execute();

            //$id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,$pla[nom]);
        }
    }
}
die("<script>location.href = './main_grups.php'</script>");
?>
    </body>






