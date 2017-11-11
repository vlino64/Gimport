<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:menu.php
* Autor: Víctor Lino
* Descripció: Presenta diiferents menús de càrrega en funció de la selecció d'una opció o una altra
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/

function modificacions($db)
    {   

    
    
    // ***************************************** MODIFICACIONS 17/18 *********************************************************************************

    // Arreglar dates de naixement


    // Reordenar noms i cognoms


    // Neteja logs de professors/alumnes/ incidències d'alumnes/.... des de fa més de 500 dies
    // Calculem la data de fa 500 dies
    $date = date("Y-m-d", strtotime("-500 day"));
    $sql = "DELETE FROM incidencia_alumne WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_INCIDENCIA_ALUMNE.mysql_error());} 
    $sql = "DELETE FROM incidencia_professor WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_INCIDENCIA_PROF.mysql_error());}
    $sql = "DELETE FROM log_alumnes WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_LOG_ALUMNES.mysql_error());}    
    $sql = "DELETE FROM log_professors WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_LOG_PROF.mysql_error());}  
    $sql = "DELETE FROM log_families WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_LOG_FAMILIES.mysql_error());} 
    $sql = "DELETE FROM qp_seguiment WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_SEGUIMENTS.mysql_error());}    
    $sql = "DELETE FROM ccc_alumne_principal WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_CCC_ALUMNES.mysql_error());}
    $sql = "DELETE FROM ccc_taula_principal WHERE data < '".$date."';";
    $result = $db->query($sql); if (!$result) {die(_DELETE_CCC_TAULA_PRINCIPAL.mysql_error());}    
        
    
    
    }
    ?>

</body>

	




