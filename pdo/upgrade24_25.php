<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->query("SET NAMES 'utf8'");

$versio=getVersio();
$fila=  mysql_fetch_row($versio);                
if ($fila[0] == '2.4')
    {

    // Extreiem dates de periode escolar
    $sql2="SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S'";
    $result2=$db->query($sql2);if (!$result2) {die(_ERR_UPDATE_21 . mysql_error());}
    $fila2=mysql_fetch_row($result2);


    // *********************************************************************************************************
    // Modificacions taula config

    $sql = "ALTER TABLE `config` ADD `alumne_posa_ccc` TINYINT NOT NULL DEFAULT '0' AFTER `mod_reg_prof`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_1 . mysql_error());}

    $sql = "ALTER TABLE `config` ADD `mod_login_google` TINYINT NOT NULL DEFAULT '0' AFTER `alumne_posa_ccc`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_2 . mysql_error());}

    print('<br>Modificacions taula config');
    
    // *********************************************************************************************************
    // Modificacions Gestió de les dades d'alumnes i professors

    $sql = "ALTER TABLE `tipus_contacte` CHANGE `Nom_info_contacte` `Nom_info_contacte` VARCHAR(30) ;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_3 . mysql_error());}

    $sql = "DELETE FROM contacte_alumne WHERE id_tipus_contacte IN (2,7,8,22,23,26,27);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_4 . mysql_error());}
    $sql = "DELETE FROM contacte_professor WHERE id_tipus_contacte IN (2,7,8,22,23,26,27);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_5 . mysql_error());}
    $sql = "DELETE FROM contacte_families WHERE id_tipus_contacte IN (2,7,8,22,23,26,27);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_6 . mysql_error());}
    $sql = "DELETE FROM tipus_contacte WHERE idtipus_contacte IN (2,7,8,22,23,26,27);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_7 . mysql_error());}

    $sql = "INSERT INTO  `tipus_contacte` (`idtipus_contacte` ,`dada_contacte` ,`Nom_info_contacte`)";
    $sql .= "VALUES ";
    // ('25',  'Contrasenya notificar',  'contrasenya_notifica'), ";
    $sql .= "('28',  'Data de naixement',  'data_naixement'),";
    $sql .= "('29',  'Email tutor 2',  'email2'),";
    $sql .= "('30',  'Mòbil sms tutor 2',  'mobil_sms2'),";
    $sql .= "('31',  'Login tutor 2',  'login2'), ";
    $sql .= "('32',  'Contrasenya tutor 2',  'contrasenya2'),";
    $sql .= "('33',  'Contrasenya notificar 2',  'contrasenya_notifica2' ),";
    $sql .= "('34', 'Email alumne', 'email'),";
    $sql .= "('35', 'Login tutor 1', 'login1');";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_8 . mysql_error());}

    // Taula tipus_contacte ampliar el nombre de caracters del VARCHAR del camp Nom_info_contacte de 20 que té ara a 21.
    $sql = "ALTER TABLE `tipus_contacte` CHANGE `Nom_info_contacte` `Nom_info_contacte` VARCHAR(21) ";
    $sql .= "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Identificador del SAGA';";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_12 . mysql_error());}
    
    $sql = "ALTER TABLE `tipus_contacte` ADD `ordre` TINYINT(4) NOT NULL DEFAULT '0' AFTER `Nom_info_contacte`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_12 . mysql_error());}
    
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = 'Email tutor 1' WHERE `tipus_contacte`.`idtipus_contacte` = 19; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_121 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `Nom_info_contacte` = 'email1' WHERE `tipus_contacte`.`idtipus_contacte` = 19; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_121 . mysql_error());}
    
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '1' WHERE `tipus_contacte`.`idtipus_contacte` = 1; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_122 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '2' WHERE `tipus_contacte`.`idtipus_contacte` = 3; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_123 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '3' WHERE `tipus_contacte`.`idtipus_contacte` = 6; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_124 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '4' WHERE `tipus_contacte`.`idtipus_contacte` = 4; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_125 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '5' WHERE `tipus_contacte`.`idtipus_contacte` = 5; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_126 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '6' WHERE `tipus_contacte`.`idtipus_contacte` = 34; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_127 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '7' WHERE `tipus_contacte`.`idtipus_contacte` = 28; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_128 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '8' WHERE `tipus_contacte`.`idtipus_contacte` = 21; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_129 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '9' WHERE `tipus_contacte`.`idtipus_contacte` = 15; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_130 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '10' WHERE `tipus_contacte`.`idtipus_contacte` = 13; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_131 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '11' WHERE `tipus_contacte`.`idtipus_contacte` = 14; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_132 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '12' WHERE `tipus_contacte`.`idtipus_contacte` = 19; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_133 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '13' WHERE `tipus_contacte`.`idtipus_contacte` = 24; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_134 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '14' WHERE `tipus_contacte`.`idtipus_contacte` = 35; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_135 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '15' WHERE `tipus_contacte`.`idtipus_contacte` = 18; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_136 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '16' WHERE `tipus_contacte`.`idtipus_contacte` = 16; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_137 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '17' WHERE `tipus_contacte`.`idtipus_contacte` = 17; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_138 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '18' WHERE `tipus_contacte`.`idtipus_contacte` = 29; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_139 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '19' WHERE `tipus_contacte`.`idtipus_contacte` = 30; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_140 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '20' WHERE `tipus_contacte`.`idtipus_contacte` = 31; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_141 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '21' WHERE `tipus_contacte`.`idtipus_contacte` = 9; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_142 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '22' WHERE `tipus_contacte`.`idtipus_contacte` = 10; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_143 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '23' WHERE `tipus_contacte`.`idtipus_contacte` = 11; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_144 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '24' WHERE `tipus_contacte`.`idtipus_contacte` = 12; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_145 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '50' WHERE `tipus_contacte`.`idtipus_contacte` = 33; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_146 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '51' WHERE `tipus_contacte`.`idtipus_contacte` = 32; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_147 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '52' WHERE `tipus_contacte`.`idtipus_contacte` = 25; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_148 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `ordre` = '53' WHERE `tipus_contacte`.`idtipus_contacte` = 20; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_149 . mysql_error());}

    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = 'Nom alumne' WHERE `tipus_contacte`.`idtipus_contacte` = 6; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_150 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '1r Cog. Alumne' WHERE `tipus_contacte`.`idtipus_contacte` = 4; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_151 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '2n Cog. Alumne.' WHERE `tipus_contacte`.`idtipus_contacte` = 5; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_152 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '1r Cog. mare' WHERE `tipus_contacte`.`idtipus_contacte` = 16; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_153 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '2n Cog. mare' WHERE `tipus_contacte`.`idtipus_contacte` = 17; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_154 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '1r Cog. pare' WHERE `tipus_contacte`.`idtipus_contacte` = 13; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_155 . mysql_error());}
    $sql ="UPDATE `tipus_contacte` SET `dada_contacte` = '2n Cog. pare' WHERE `tipus_contacte`.`idtipus_contacte` = 14; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_156 . mysql_error());}
    
    
    print('<br>Modificacions gestió de les dades d\'alumnes i professors');

    // *********************************************************************************************************
    // Taula per desar les CCC temporalment abans d'aprovar-les

    $sql = "CREATE TABLE `ccc_alumne_principal` (";
    $sql .= "  `idccc_alumne_principal` int(11) NOT NULL,";
    $sql .= "  `idalumne` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `idgrup` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `idprofessor` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `idmateria` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `idfranges_horaries` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `idespais` int(11) NOT NULL DEFAULT '0',";
    $sql .= "  `data` date DEFAULT NULL,";
    $sql .= "  `descripcio_detallada` longtext COLLATE utf8_bin NOT NULL";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_9 . mysql_error());}

    $sql = "ALTER TABLE `ccc_alumne_principal`  ADD PRIMARY KEY (`idccc_alumne_principal`);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_10 . mysql_error());}

    $sql = "ALTER TABLE `ccc_alumne_principal`  MODIFY `idccc_alumne_principal` int(11) NOT NULL AUTO_INCREMENT";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_11 . mysql_error());}

    print('<br>Modificacions per permetre als alumnes la gestió de les seves CCC');

    // *********************************************************************************************************
    // Afegit el camp ip_remota, VARCHAR(20) a la taula log_professors.
    $sql = "ALTER TABLE `log_professors` ADD `ip_remota` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0' AFTER `id_accio`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_13 . mysql_error());}

    print('<br>Modificacions a la taula log_professors');

    // *********************************************************************************************************
    // Afegit un registre a la taula tipus_falta_profesor -> Sortida amb alumnes.
    $sql = "INSERT INTO `tipus_falta_professor` (`idtipus_falta_professor`, `tipus_falta`, `Comentari`) ";
    $sql .= "VALUES (NULL, 'Sortida amb alumnes', NULL);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_14 . mysql_error());}

    print('<br>Modificacions a la taula de tipus_falta_professor');

     // *********************************************************************************************************
    // Modificacions a la taula de missatges al tutor

    $sql = "ALTER TABLE `missatges_tutor` ADD `login_tutor` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL AFTER `idgrup`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_15 . mysql_error());}

    $sql = "ALTER TABLE `missatges_tutor` ADD `num_tutor` TINYINT(4) NOT NULL DEFAULT '1' AFTER `login_tutor`;"; 
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_16 . mysql_error());}

    print('<br>Modificacions a la taula de missatges_tutor');

    // *********************************************************************************************************
    // Modificació per gestionar automatrícula  des de grups-materies 

    $sql = "ALTER TABLE `grups_materies` ADD `contrasenya` VARCHAR(25) NULL DEFAULT NULL AFTER `data_fi`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_17 . mysql_error());}

    $sql = "ALTER TABLE `grups_materies` ADD `automatricula` VARCHAR(1) NULL DEFAULT NULL AFTER `data_fi`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_171 . mysql_error());}    
    
    print('<br>Modificacions a la taula grups_materies per l\'automatrícula');

    // *********************************************************************************************************
    // Modificacions taula plans estudis

    $sql = "ALTER TABLE `plans_estudis` CHANGE `Nom_plan_estudis` `Nom_plan_estudis` VARCHAR(120) CHARACTER SET utf8 COLLATE  utf8_general_ci NULL DEFAULT NULL;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_18 . mysql_error());}

    print('<br>Modificacions taula plans_estudis');

    // *********************************************************************************************************    
    // Modificacions taula moduls_materies_ufs

    $sql = "ALTER TABLE  `moduls_materies_ufs` CHANGE  `codi_materia`  `codi_materia` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_19 . mysql_error());}

    $sql = "ALTER TABLE `moduls_materies_ufs` DROP `automatricula`, DROP `contrasenya`;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_191 . mysql_error());}
    
    print('<br>Modificacions taula moduls_materies_ufs');

    // *********************************************************************************************************
    // Modificacions a la taula unitats formatives

    $sql = "UPDATE `unitats_formatives` SET `data_inici` = '".$fila2[0]."' WHERE `data_inici` = 0000-00-00;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_21 . mysql_error());} 

    $sql = "UPDATE `unitats_formatives` SET `data_fi` = '".$fila2[1]."' WHERE `data_fi` = 0000-00-00;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_21 . mysql_error());}

    $sql = "ALTER TABLE `unitats_formatives` CHANGE `data_inici` `data_inici` DATE NULL DEFAULT NULL; "; 
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_21 . mysql_error());}
        
    $sql = "ALTER TABLE `unitats_formatives` CHANGE `data_fi` `data_fi` DATE NULL DEFAULT NULL; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_21 . mysql_error());}
        
    $sql = "ALTER TABLE `unitats_formatives` CHANGE `codi_uf` `codi_uf` VARCHAR(100) ;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_20 . mysql_error());}

    $sql = "ALTER TABLE `unitats_formatives` CHANGE `nom_uf` `nom_uf` VARCHAR(100);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_21 . mysql_error());}

    print('<br>Modificacions a la taula unitats_formatives');

    // *********************************************************************************************************    
    // Modificacions taula materia

    $sql = "ALTER TABLE  `materia` CHANGE  `codi_materia`  `codi_materia` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_22 . mysql_error());}

    $sql = "ALTER TABLE  `materia` CHANGE  `nom_materia`  `nom_materia` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_23 . mysql_error());}

    print('<br>Modificacions taula materia');

    
    // *********************************************************************************************************    
    // Modificacions taula espais

    $sql = "ALTER TABLE `espais_centre` CHANGE `codi_espai` `codi_espai` VARCHAR(40);";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_22 . mysql_error());}

    print('<br>Modificacions taula espais');    

    // *********************************************************************************************************    
    // Creació de taula guardies signades
    
    
    $sql = "CREATE TABLE IF NOT EXISTS `guardies_signades` ( ";
    $sql .= "`idguardia_signada` int(11) NOT NULL AUTO_INCREMENT, ";
    $sql .= "`idprofessors` int(11) NOT NULL, ";
    $sql .= "`idgrups` int(11) NOT NULL, ";
    $sql .= "`id_mat_uf_pla` int(11) NOT NULL, ";
    $sql .= "`idfranges_horaries` int(11) NOT NULL, ";
    $sql .= "`data` date NOT NULL, ";
    $sql .= "PRIMARY KEY (`idguardia_signada`) ";
    $sql .= ") ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_CREATE_1 . mysql_error());}

    // *********************************************************************************************************    
    // Creació de taula log_emails
    
    $sql = "CREATE TABLE IF NOT EXISTS `log_emails` ( ";
    $sql .= "`idEmail` int(11) NOT NULL AUTO_INCREMENT, ";
    $sql .= "`idAlumne` int(11) NOT NULL, ";
    $sql .= "`idProfessor` int(11) NOT NULL, ";
    $sql .= "`emailDesti` varchar(100) NOT NULL, ";
    $sql .= "`emailMissatge` varchar(2000) NOT NULL, ";
    $sql .= "`timeStamp` datetime NOT NULL, ";
    $sql .= "PRIMARY KEY (`idEmail`) ";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_CREATE_2 . mysql_error());}


    // *********************************************************************************************************    
    // Creació de taula periodes escolars festius    
    
    
    $sql = "CREATE TABLE IF NOT EXISTS `periodes_escolars_festius` ( ";
    $sql .= "`id_festiu` int(11) NOT NULL AUTO_INCREMENT, ";
    $sql .= "`id_periode` int(11) NOT NULL, ";
    $sql .= "`festiu` date NOT NULL, ";
    $sql .= "PRIMARY KEY (`id_festiu`) ";
    $sql .= ") ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_CREATE_3 . mysql_error());}

    // *********************************************************************************************************    
    // Creació de taula qpseguiments
    
    $sql = "CREATE TABLE IF NOT EXISTS `qp_seguiment` ( ";
    $sql .= "`id_seguiment` int(11) NOT NULL AUTO_INCREMENT, ";
    $sql .= "`id_dia_franja` int(11) NOT NULL, ";
    $sql .= "`id_grup_materia` int(11) NOT NULL, ";
    $sql .= "`lectiva` int(1) NOT NULL, ";
    $sql .= "`seguiment` varchar(1000) NOT NULL, ";
    $sql .= "`data` date NOT NULL, ";
    $sql .= "PRIMARY KEY (`id_seguiment`) ";
    $sql .= ") ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=latin1; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_CREATE_4 . mysql_error());}

    // *********************************************************************************************************    
    // Creació de taula tipus motius falta professor

    $sql = "CREATE TABLE IF NOT EXISTS `tipus_motius_falta_professor` ( ";
    $sql .= "`idtipus_motius_professor` int(11) NOT NULL AUTO_INCREMENT, ";
    $sql .= "`tipus_motius` varchar(45) DEFAULT NULL, ";
    $sql .= "`Comentari` varchar(100) DEFAULT NULL, ";
    $sql .= "PRIMARY KEY (`idtipus_motius_professor`) ";
    $sql .= ") ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8; ";
    $result=$db->query($sql);if (!$result) {die(_ERR_CREATE_5 . mysql_error());}

    $sql = "INSERT INTO `tipus_motius_falta_professor` (`idtipus_motius_professor`, `tipus_motius`, `Comentari`) VALUES ";
    $sql .= "(1, 'Permís per formació', NULL), ";
    $sql .= "(2, 'Permís sindical', NULL), ";
    $sql .= "(3, 'Permís altres', NULL), ";
    $sql .= "(4, 'Malaltia', NULL), ";
    $sql .= "(5, 'Retard', NULL), ";
    $sql .= "(6, 'Passar llista guàrdia', NULL), ";
    $sql .= "(7, 'Sortida', NULL); ";
    $result=$db->query($sql);if (!$result) {die(_ERR_INSERT_6 . mysql_error());}



    // *********************************************************************************************************
    // Actualitza la versió

    $sql = "INSERT INTO `versio_bdd` (`versio`) VALUES ('2.5');";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_24 . mysql_error());}

    print('<br>Actualització de la versió a la bdd<br><br>');

    print('Actualització a la versió 2.5 executada correctament');
    

    // ===========================================================================
    // NOMÉS SI EXISTEIX PROFESSOR AMB ID = 0.
    // COMPROVAR EN LA TAULA PROFESSOR
    // Modificació  de l'usuari professor NO_PROF que es va crear per solucionar problemes 
    // amb els CCC que no es gravaven bé. S'ha reconvertit a NO_PROF2
    // Ara  que s'han modificat els combobox ja no ens fa falta i provoca 
    // problemes al crear nous usuaris ja que agafa les dades del id 0 de la taula de professors

    $sql = "SELECT idprofessors FROM professors WHERE idprofessors = '0';";
    $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_25 . mysql_error());}
    if ( mysql_num_rows($result) == 1)
        {
        $sql = "INSERT INTO `professors` (`idprofessors` ,`codi_professor` ,`activat` ,`historic`) VALUES ('1', 'NO_PROF2','N', 'N');";
        $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_26 . mysql_error());}
        
        $sql = "UPDATE `ccc_taula_principal` SET `idprofessor` =1 WHERE `idprofessor` =0;";
        $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_27 . mysql_error());}
        
        $sql = "UPDATE `contacte_professor` SET `id_professor` =1 WHERE `id_professor` =0;";
        $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_28 . mysql_error());}
        
        $sql = "DELETE FROM `professors` WHERE idprofessors=0 ;";
        $result=$db->query($sql);if (!$result) {die(_ERR_UPDATE_29 . mysql_error());}
        

    print('<br>Comprovació elements actualització anterior - OK<br><br>');
        }
    }
else
    {
    print('<h2>Atenció</h2>');
    print('Aquest fitxer no és el correcte per la versió que tens instal.lada actualment.');
    print('Consulta amb l\'administrador o amb info@geisoft.cat' );
    }




//mysql_close(); 

?>


 