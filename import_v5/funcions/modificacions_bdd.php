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

function modificacions()
    {   

    require_once('../../bbdd/connect.php');
    
    // ***************************************** MODIFICACIONS 17/18 *********************************************************************************

    // Neteja duplicats accessos pares
    $sql = "SELECT idfamilies FROM families";
    $result=mysql_query($sql); if (!$result) {die(_SELECT_FAMILIES.mysql_error());}
    while ($fila=  mysql_fetch_row($result)) {
        $idFamilia = $fila[0];
        $sql2 = "SELECT COUNT(*) FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
        $sql2 .= "id_tipus_contacte = '21';";
//        echo "<br>".$sql2;
        $result2=mysql_query($sql2); if (!$result2) {die(_CHECK_CONTACT_FAMILY_.mysql_error());}
        $resultat = mysql_fetch_row($result2);
        if ($resultat[0] > 1)
            {
            //Escollim el login a respectar
            $sql2 = "SELECT idcontacte_families FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '21' ORDER BY idcontacte_families DESC LIMIT 1;";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_CHECK_CONTACT_FAMILY2_.mysql_error());}
            $resultat = mysql_fetch_row($result2);
            $idFamiliaGuardar = $resultat[0];
            // Eliminem els logins repetits
            $sql2 = "DELETE FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '21' AND idcontacte_families != '".$idFamiliaGuardar."';";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_DELETE_LOGIN_.mysql_error());}
            // Eliminem els passwords md5 que hi hagin
            $sql2 = "DELETE FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '20';";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_DELETE_LOGIN_.mysql_error());}
            // Seleccionem el password a desar
            $sql2 = "SELECT MAX(idcontacte_families) FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '25';";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_CHECK_CONTACT_FAMILY_.mysql_error());}
            $resultat = mysql_fetch_row($result2);
            $idFamiliaGuardar = $resultat[0];
            
            $sql2 = "DELETE FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '25' AND idcontacte_families != '".$idFamiliaGuardar."';";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_DELETE_LOGIN_.mysql_error());}
            // Extreiem el login a mantenir
            $sql2 = "SELECT Valor FROM contacte_families WHERE id_families = '".$idFamilia."' AND ";
            $sql2 .= "id_tipus_contacte = '25'";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_SELECT_LOGIN_.mysql_error());}
            $resultat = mysql_fetch_row($result2);
            $pass = $resultat[0];
            // Restablim la password MD5
            $sql2 = "INSERT INTO contacte_families(id_families,id_tipus_contacte,Valor) VALUES ";
            $sql2 .= "('".$idFamilia."','20',MD5('".$pass."'))";
//            echo "<br>".$sql2;
            $result2=mysql_query($sql2); if (!$result2) {die(_INSERT_PASS_MD5_.mysql_error());}
            }        
    }
    
    
    // Esborra taules d'històrics. No s'estan utilitzant
    $sql = "DROP TABLE IF EXISTS HIST_CCC;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());}
    $sql = "DROP TABLE IF EXISTS HIST_dates;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());} 
    $sql = "DROP TABLE IF EXISTS HIST_faltes_alumnes;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());} 
    $sql = "DROP TABLE IF EXISTS HIST_incidencia_professor;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());}     
    $sql = "DROP TABLE IF EXISTS HIST_sortides;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());} 
    $sql = "DROP TABLE IF EXISTS HIST_sortides_alumne;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());}     
    $sql = "DROP TABLE IF EXISTS HIST_sortides_professor;";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_HIST_TABLE.mysql_error());} 
    
    // Neteja logs de professors/alumnes/ incidències d'alumnes/.... des de fa més de 500 dies
    // Calculem la data de fa 500 dies
    $date = date("Y-m-d", strtotime("-500 day"));
    $sql = "DELETE FROM incidencia_alumne WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_INCIDENCIA_ALUMNE.mysql_error());} 
    $sql = "DELETE FROM incidencia_professor WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_INCIDENCIA_PROF.mysql_error());}
    $sql = "DELETE FROM log_alumnes WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_LOG_ALUMNES.mysql_error());}    
    $sql = "DELETE FROM log_professors WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_LOG_PROF.mysql_error());}  
    $sql = "DELETE FROM log_families WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_LOG_FAMILIES.mysql_error());} 
    $sql = "DELETE FROM qp_seguiment WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_SEGUIMENTS.mysql_error());}    
    $sql = "DELETE FROM ccc_alumne_principal WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_CCC_ALUMNES.mysql_error());}
    $sql = "DELETE FROM ccc_taula_principal WHERE data < '".$date."';";
    $result=mysql_query($sql); if (!$result) {die(_DELETE_CCC_TAULA_PRINCIPAL.mysql_error());}    
        
    // Crear la infraestructura per desar empremtes
    // Crear la taula d'empremtes
    $sql = "CREATE TABLE IF NOT EXISTS `UAREUfmds` (`id` int(8) NOT NULL AUTO_INCREMENT,`fmd` longtext NOT NULL,";
    $sql .="`nom` varchar(256) NOT NULL DEFAULT 'sense nom',";
    $sql.= "PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;";
    $result=mysql_query($sql); if (!$result) {die(_CREATE_TABLE_FMDS.mysql_error());}    
    // Crear la vista
    $sql = "CREATE OR REPLACE VIEW `UAREUVistaProf` AS select `A`.`id_professor` AS `id_professor`,`A`.`Valor` AS `Valor` ";
    $sql.= "from (`contacte_professor` `A` join `professors` `B`) where ((`A`.`id_professor` = `B`.`idprofessors`) and ";
    $sql.= "(`A`.`id_tipus_contacte` = 1) and (`B`.`activat` = 'S'));";
    
    $result=mysql_query($sql); if (!$result) {die(_CREATE_VIEW_TEACHERS_FMDS.mysql_error());}        
    
    // Canviar conserge per responsable de faltes
    $sql = "SELECT COUNT(nom_carrec) FROM carrecs WHERE nom_carrec = 'CONSERGE';";
    $result=mysql_query($sql); if (!$result) {die(_CHECK_ROL_CONSERGE_.mysql_error());}
    $resultat = mysql_fetch_row($result);
    if ($resultat[0] != 0)
        {
        $sql2 = "UPDATE carrecs SET nom_carrec = 'RESPONSABLE DE FALTES' , descripcio = 'RESPONSABLE DE FALTES' ";
        $sql2.= "WHERE nom_carrec = 'CONSERGE';";
        $result2=mysql_query($sql2); if (!$result2) {die(_UPDATE_ROL_CONSERGE_.mysql_error());}
        }    
    
    // taula alumnes. Convertir codi alumnes saga de bigint a varchar
    $sql = " ALTER TABLE `alumnes` CHANGE `codi_alumnes_saga` `codi_alumnes_saga` VARCHAR(30) NULL DEFAULT NULL;";
    $result=mysql_query($sql); if (!$result) {die(_UPDATE_TABLE_ALUMNES_.mysql_error());}
    
    // valor a contacte families a 255
    $sql = "ALTER TABLE `contacte_families` CHANGE `Valor` `Valor` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql); if (!$result) {die(_UPDATE_TABLE_FAMILIES_.mysql_error());}
    
    $sql = "ALTER TABLE `equivalencies` CHANGE `materia_gp` `materia_gp` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
        $result=mysql_query($sql); if (!$result) {die(_UPDATE_TABLE_EQUIVALENCIES_.mysql_error());}

    $sql ="ALTER TABLE `contacte_families` CHANGE `Valor` `Valor` VARCHAR(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
        $result=mysql_query($sql);if (!$result) {die(_ERR_UPDATE_TABLE_CONT_FAM . mysql_error());} 
    
    }
    ?>

</body>

	




