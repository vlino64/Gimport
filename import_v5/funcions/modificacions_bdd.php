<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:menu.php
 * Autor: Víctor Lino
 * Descripció: Presenta diiferents menús de càrrega en funció de la selecció d'una opció o una altra
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */

function modificacions($db) {
    // ***************************************** MODIFICACIONS 18/19 *********************************************************************************
    //Soluciona el tema del user de la vista
    // Neteja logs de professors/alumnes/ incidències d'alumnes/.... des de fa més de 500 dies
    // Calculem la data de fa 500 dies
    $date = date("Y-m-d", strtotime("-500 day"));
    $sql = "DELETE FROM incidencia_alumne WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM incidencia_professor WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM log_alumnes WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM log_professors WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM log_families WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM qp_seguiment WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM ccc_alumne_principal WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM ccc_taula_principal WHERE data < '" . $date . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM qp_seguiment;";
    $result = $db->prepare($sql);
    $result->execute();
    
    $sql = "ALTER TABLE `equivalencies` CHANGE `materia_gp` `materia_gp` VARCHAR(200) "
            . "CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result = $db->prepare($sql);
    $result->execute();
}
?>

</body>






