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
        // Eliminem totes les assitències del curs anterior que no siguin docències
        $sql = "DELETE FROM prof_altres ";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM prof_atencions ";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM prof_coordinacions ";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM prof_direccio ";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM prof_permanencies ";
        $result = $db->prepare($sql);
        $result->execute();

        $sql = "DELETE FROM prof_reunions ";
        $result = $db->prepare($sql);
        $result->execute();

        // Si no esta creat, generem l'espai 'Sense deerminar'
        $sql = "SELECT idespais_centre FROM espais_centre "
                . "WHERE descripcio = 'Sense determinar';";
        $result = $db->prepare($sql);
        $result->execute();
        $files = $result->rowCount();
        if ($files == 0) {
            $sql = "INSERT INTO espais_centre(descripcio,activat,codi_espai) "
                    . "VALUES ('Sense determinar','S','Sense determinar');";
            $result = $db->prepare($sql);
            $result->execute();
            $sql = "SELECT idespais_centre FROM espais_centre "
                    . "WHERE descripcio = 'Sense determinar';";
            $result = $db->prepare($sql);
            $result->execute();
        }
        $fila = $result->fetch();
        $idEspai = $fila['idespais_centre'];


        $recompte = $_POST['recompte'];
//        echo "<br>" . $recompte;
        // Carreguem els grups i el seu torn
        for ($i = 1; $i <= $recompte; $i++) {

            $idProf = $_POST['idProfessor' . $i];
            $id_torn = $_POST['id_torn_' . $i];

            $sql = "SELECT B.id_dies_franges AS diafranja "
                    . "FROM franges_horaries A, dies_franges B "
                    . "WHERE A.idfranges_horaries = B.idfranges_horaries AND "
                    . "A.activada = 'S' AND "
                    . "A.idtorn = " . $id_torn . ";";

//            echo "<br>" . $sql;
            $result = $db->prepare($sql);
            $result->execute();
            foreach ($result->fetchAll() as $fila) {
                $sql2 = "INSERT INTO prof_permanencies(idprofessors,id_dies_franges,idespais_centre) "
                        . "VALUES (" . $idProf . "," . $fila['diafranja'] . ", " . $idEspai . ");";
//                echo "<br>" . $sql2;
                $result = $db->prepare($sql2);
                $result->execute();
            }
        }
        $page = "../import/index.php";
        $sec = "0";
        header("Refresh: $sec; url=$page");
        ?>
    </body>
