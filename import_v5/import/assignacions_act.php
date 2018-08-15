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
$exportsagaxml = $_SESSION['upload_saga'];
$resultatconsulta = simplexml_load_file($exportsagaxml);
if (!$resultatconsulta) {
    echo "Carrega fallida";
} else {
    echo "<br>Carrega correcta";
    for ($i = 1; $i <= $recompte; $i++) {
        $id_grup = $_POST['id_grup_' . $i];
        $nom_grup = $_POST['nom_grup_' . $i];
        $id_grup_saga = $_POST['id_grup_saga_' . $i];
        // Cerquem el seu equivalent dl programa d'horaris

        if ($id_grup_saga != "0") {
            //echo "<br>".$id_grup." >> ".$nom_grup." >> ".$id_grup_saga ;
            //Treiem totes les materies del grup
            $sql = "SELECT id_mat_uf_pla,idgrups_materies FROM grups_materies WHERE id_grups = '" . $id_grup . "';";
            $result = $db->prepare($sql);
            $result->execute();
            foreach ($result->fetchAll() as $fila) {
                foreach ($resultatconsulta->grups->grup as $grup) {
                    if (!strcmp($grup['id'], $id_grup_saga)) {
                        foreach ($grup->alumnes->alumne as $alumne) {
                            $sql = "SELECT id_alumne FROM contacte_alumne WHERE id_tipus_contacte = 3 AND Valor = '" . $alumne['id'] . "';";
                            $result3 = $db->prepare($sql);
                            $result3->execute();
                            $fila3 = $result3->fetch();

                            if ($fila3['id_alumne'] != '') {
                                $sql4 = "INSERT alumnes_grup_materia(idalumnes,idgrups_materies) ";
                                $sql4 .= "VALUES ('" . $fila3[0] . "','" . $fila[1] . "');";
                                //echo ">>>>".$sql4."<br>";
                                $result4 = $db->prepare($sql4);
                                $result4->execute();
                            }
                        }
                    }
                }
            }
        }
    }

    introduir_fase('assig_alumnes', 1, $db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}
?>
    </body>
