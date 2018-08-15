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
include("../funcions/func_grups_materies.php");
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
if (extreu_fase('modalitat_fitxer', $db) == 2) {
    // Comprovem siel pla ESO_BAT ja està introduit
    $id_pla = extreu_id('plans_estudis', 'Nom_plan_estudis', 'idplans_estudis', 'ESO LOE', $db);
    if ($id_pla == '') {
        // Introduim un pla fictici
        $sql = "INSERT IGNORE INTO plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) VALUES ('S','ESO_BAT','ESO_BAT');";
        //echo $sql."<br>";
        $result = $db->prepare($sql);
        $result->execute();
        $id_pla = extreu_id('plans_estudis', 'Nom_plan_estudis', 'idplans_estudis', 'ESO_BAT', $db);
    }
}

$recompte = $_POST['recompte'];
//   if (extreu_fase('app_horaris')==0)
//      // Des de GPuntis
//      {
$j = 0;
$k = 0;
$materia = "";
for ($i = 1; $i <= $recompte; $i++) {
    //echo "<br>>>>".$i." >>> ".$recompte;
    $crea = false;
    $check = false;
    if (isset($_POST['crea_' . $i]) && $_POST['crea_' . $i] == "Yes") {
        $crea = true;
    }
    if (isset($_POST['es_CCFF_LOE_' . $i]) && $_POST['es_CCFF_LOE_' . $i] == "Yes") {
        $check = true;
    }
    $codi_materia = $_POST['codi_mat_' . $i];
    $codi_materia = neteja_apostrofs($codi_materia);
    $nom_materia = $_POST['nom_mat_' . $i];
    $nom_materia = neteja_apostrofs($nom_materia);

    if ($crea) {
        if ($check) {
            $materia[$j][0] = $codi_materia;
            $materia[$j][1] = $nom_materia;
            $j++;
        } else {
            // $moduls conté les opcions que no s'han marcat i que per tan es tracta de mòduls
            $moduls[$k][0] = $codi_materia;
            $moduls[$k][1] = $nom_materia;
            $k++;
        }
    }
}
alta_materies($materia, $id_pla, $db);
sort($moduls);
alta_moduls($moduls, $db);
?>
    </body>


