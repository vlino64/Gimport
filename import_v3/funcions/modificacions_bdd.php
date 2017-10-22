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

    include("../config.php");

    // Modificacions taula_equivalencies
    $sql="ALTER TABLE  `equivalencies` CHANGE  `materia_gp`  `materia_gp` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL ;";
    $result=mysql_query($sql);	
    if (!$result) {die(_ERR_ALTER_LENGTH_MAT_GP6 . mysql_error());}

    // Correccions taula equivalenciess
    // Elimina les parelles profgp i profga per aconseguir el trinomi: profga, nomgp i codi_profgp
    $sql = "SELECT prof_gp,prof_ga FROM equivalencies WHERE prof_gp!='' AND prof_ga!='';";
    $result=mysql_query($sql); if (!$result) {die(_SELECT_PROF_EQUIVALENCIES_.mysql_error());}
    while ($fila=  mysql_fetch_row($result))
      {
      $sql2 = "UPDATE equivalencies SET prof_ga='".$fila[1]."' WHERE codi_prof_gp='".$fila[0]."';";
      $result2=mysql_query($sql2); if (!$result2) {die(_UPDATE_PROF_EQUIVALENCIES_.mysql_error());}
      
      $sql2 = "DELETE FROM equivalencies WHERE prof_ga='".$fila[1]."' AND prof_gp='".$fila[0]."';";
      $result2=mysql_query($sql2); if (!$result2) {die(_DELETE_PROF_EQUIVALENCIES_.mysql_error());}      
      }

    $sql ="UPDATE  `fases` SET  `comentaris` =  '0: gpuntis,1:ghc peñalara;2:Kronowin;3:HorW (Sevilla); ";
    $sql.="4:aSc; 5: cap aplicacio' WHERE `fases`.`id` =8;";
    //echo $sql;
    $result=mysql_query($sql);	
    if (!$result) {die(_ERR_ALTER_INSERT_ASC1 . mysql_error());}    
    
    
    //Retocs a la taula equivalències
    $sql="ALTER TABLE `equivalencies` CHANGE `grup_gp` `grup_gp` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC2 . mysql_error());}

    $sql="ALTER TABLE `equivalencies` CHANGE `grup_ga` `grup_ga` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC3 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `grup_saga` `grup_saga` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";  
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC4 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `prof_gp` `prof_gp` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC5 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `prof_ga` `prof_ga` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC6 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `nom_prof_gp` `nom_prof_gp` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC7 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `codi_prof_gp` `codi_prof_gp` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC8 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `pla_saga` `pla_saga` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC9 . mysql_error());}    
    
    $sql="ALTER TABLE `equivalencies` CHANGE `materia_saga` `materia_saga` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC10 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `materia_gp` `materia_gp` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC11 . mysql_error());}
    
    $sql="ALTER TABLE `equivalencies` CHANGE `altres` `altres` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC12 . mysql_error());}

    $sql = "SELECT idtipus_contacte FROM tipus_contacte WHERE Nom_info_contacte = 'cognoms_profe';";
    $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC13 . mysql_error());}
    if (mysql_num_rows($result) == 0)    
        {
        $sql = "INSERT INTO `tipus_contacte` (`dada_contacte`, `Nom_info_contacte`, `ordre`) VALUES ('Nom ', 'nom_profe', '38'), ('Cognoms', 'cognoms_profe', '39');";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_INSERT_ASC14 . mysql_error());}
        }

    // Retocs taula històrics
    
    $sql = "SHOW TABLES LIKE 'HIST_CCC';";
    $result=mysql_query($sql);
    if (mysql_num_rows($result) > 0)
        {
        
        $sql = "UPDATE `HIST_CCC` SET `data_inici_sancio` = NULL WHERE `data_inici_sancio` = 0000-00-00;";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC4 . mysql_error());}
        
        $sql = "UPDATE `HIST_CCC` SET `data_fi_sancio` = NULL WHERE `data_fi_sancio` = 0000-00-00;";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC5 . mysql_error());}
        
        $sql = "ALTER TABLE `HIST_CCC` CHANGE `grup` `grup` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0';";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC0 . mysql_error());}

        $sql = "ALTER TABLE `HIST_CCC` CHANGE `grup` `grup` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0';";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC1 . mysql_error());}

        $sql = "ALTER TABLE `HIST_CCC` CHANGE `motiu` `motiu` VARCHAR(180) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC2 . mysql_error());} 

        $sql = "ALTER TABLE `HIST_CCC` CHANGE `descripcio_breu` `descripcio_breu` VARCHAR(180) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;";
        $result=mysql_query($sql);if (!$result) {die(_ERR_ALTER_HIST_CCC3 . mysql_error());}
        }
    
    }
    ?>

</body>

	




