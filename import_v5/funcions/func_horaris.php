<?php

/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:funcions_saga.php
 * Autor: Víctor Lino
 * Descripció: Funcions relacionades amb tasques d'importació de dades de SAGA
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2
// 					CREACIÓ D'HORARIS
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function crea_horaris_ASC_mixt($db) {

    introduir_fase('lessons', 0, $db);

    $dates = treuDatesUnitatsFormatives($db);
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];


    if (!extreu_fase('segona_carrega', $db)) {
        buidatge('desdecreahoraris', $db);
        echo "<br>Tot Netejat";
    }

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='Sense determinar'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $codi_noroomArr = $result->fetch();
    $codi_noroom = $codi_noroomArr['idespais_centre'];

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual='S'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $periodeArr = $result->fetch();
    $periode = $periodeArr['idperiodes_escolars'];


    //$resultatconsulta=simplexml_load_file($exportsagaxml);

    $csvFile = $_SESSION['upload_horaris'];

    $data = array();
    $data = netejaCsv($csvFile);
    foreach ($data as $fila) {
        $idMateria = "";
        $idModul = "";
        $idGrup = "";
        $idgrup_materia = "";
        //echo "<br>>".$fila;
        $array_fila = explode(";", $fila);

        // Exdtreiem el codi de la materia/unitats formatives
        $codiMateria = $array_fila[0];
        $materia = $array_fila[1];
        //$nom_materia=neteja_apostrofs($nom_materia);
        $codiGrup = $array_fila[2];
        $codiGrup = neteja_apostrofs($codiGrup);
        $codiMateriaUnits = $codiMateria . "-" . $materia;
        $codiMateriaUnits = neteja_apostrofs($codiMateriaUnits);
        //echo "<br>".$codiMateriaUnits;
        $idMateria = extreu_id('materia', 'codi_materia', 'idmateria', $codiMateriaUnits, $db);

        $idProfessor = extreu_id("equivalencies", "nom_prof_gp", "prof_ga", $array_fila[3], $db);
        //echo "<br>>>>".$idMateria." >>".$codiMateria." >> ".$materia." >> ".$codiGrup." >> ".$array_fila[3]." >> ".$array_fila[4];
        if ($idMateria == "") {
            $idModul = extreu_id('equivalencies', 'materia_gp', 'materia_saga', $codiMateriaUnits, $db);
            if ($idModul != "") {
                $idPla = extreu_id('equivalencies', 'materia_gp', 'pla_saga', $codiMateriaUnits, $db);
            }
        }
        //echo "<br>".$idMateria." >> ".$idModul;   
        // Extreiem les sessions    
        $sessions = $array_fila[4];

        // Extreiem l'identificador del grup
        $idGrup = extreu_id('grups', 'nom', 'idgrups', $codiGrup, $db);
        //echo "<br>".$codiGrup." >> ".$idGrup;
        // Gestionem el grup-matèria

        if (($idMateria != "") AND ( $idGrup != "")) {
            //echo "<br>Entra materia";
            // Comprovem que el grup materia no existeix
            $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $idMateria . "';";
            $result = $db->prepare($sql);
            $result->execute();

            $present = $result->rowCount();
            $fila2 = $result->fetch();
            $idgrup_materia = $fila2['idgrups_materies'];
            if ($present == 1) {
                $es_nou_grup_materia = 0;
                $idgrup_materia = creadesdoblament($idgrup_materia, $materia, $db);
            }
            if ($present == 0) {
                $es_nou_grup_materia = 1;
                $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('" . $idGrup . "','" . $idMateria . "');";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $idMateria . "';";
                $result = $db->prepare($sql);
                $result->execute();
                $idgrup_materiaArr = $result->fetch();
                $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
            }
            // Gestionem el professor... o professors ja que en poden haver 2 omés
            gestionaProfessorESO($array_fila[3], $idgrup_materia, $es_nou_grup_materia, $db);
            creaSessionsEso($sessions, $idgrup_materia, $codi_noroom, $periode, $db);
        } else if (($idModul != "") AND ( $idGrup != "")) {
            //echo "<br>Entra mòdul";
            $arrayUfs = array();
            for ($j = 0; $j < count($arrayUfs); $j++) {
                $arrayUfs[$j][0] = "";
                $arrayUfs[$j][1] = "";
            }

            // Extreiem les unitats formatives dels móduls eliminnat els "DESD" per no fer massa crides
            //$sql = "SELECT id_ufs FROM moduls_ufs WHERE id_moduls =  '".$idModul."'; ";
            $sql = "SELECT A.id_ufs AS idufs FROM moduls_ufs A,unitats_formatives B WHERE A.id_moduls =  '" . $idModul . "' AND ";
            $sql .= "A.id_ufs = B.idunitats_formatives AND B.nom_uf NOT LIKE '%DESD%'; ";
            //echo "<br>" . $sql;
            $resultat = $db->prepare($sql);
            $resultat->execute();
            // Repetir+a el bucle per cada Uf d'aquest módul
            $i = 0;
            foreach ($resultat->fetchAll() as $fila2) {
                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $fila2['idufs'] . "';";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $present = $result->rowCount();
                $fila3 = $result->fetch();
                $idgrup_materia = $fila3['idgrups_materies'];
                if ($present == 1) {
                    $es_nou_grup_materia = 0;
                    $idgrup_materia = creadesdoblament($idgrup_materia, $idModul, $db);
                }
                if ($present == 0) {
                    $es_nou_grup_materia = 1;
                    // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                    // La posteriors comencen d'aquesta data fins a final de curs.
                    if (primera_uf($fila2['idufs'], $db) == 1) {
                        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                        $sql .= "VALUES ('" . $idGrup . "','" . $fila2['idufs'] . "','" . $data_inici . "','" . $data_tmp2 . "');";
                    } else {
                        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                        $sql .= "VALUES ('" . $idGrup . "','" . $fila2['idufs'] . "','" . $data_tmp2 . "','" . $data_fi . "');";
                    }
                }
                //echo "<br>".$sql;  
                $result = $db->prepare($sql);
                $result->execute();
                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $fila2['idufs'] . "';";
//                   echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $fila3 = $result->fetch();
                $idgrup_materia = $fila3['idgrups_materies'];
//                   echo "<br>".$idgrup_materia;
//                   echo "<br>>>>".$i;

                $arrayUfs[$i][0] = $idgrup_materia;
                $arrayUfs[$i][1] = $es_nou_grup_materia;
                $i++;
//                 echo "<br>>>>".$i;  
            }

//            for ($j=0;$j<count($arrayUfs);$j++)
//                {
//                echo "<br>=== ".$arrayUfs[$j][0]." >> ".$arrayUfs[$j][1];
//                }
            gestionaProfessorCCFF($array_fila[3], $arrayUfs, $db);
            creaSessionsCCFF($sessions, $arrayUfs, $codi_noroom, $periode, $db);
        }
    }
    crea_tutories_ASC($csvFile, $db);
    crea_guardies_ASC($csvFile, $db);
    introduir_fase('lessons', 1, $db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}

function crea_tutories_ASC($csvFile, $db) {

    $data = array();
    $data = netejaCsv($csvFile);
    foreach ($data as $fila) {
        $idMateria = "";
        $idModul = "";
        $idGrup = "";
        $idgrup_materia = "";
        //echo "<br>>".$fila;
        $array_fila = explode(";", $fila);

        // Exdtreiem el codi de la materia/unitats formatives
        $materia = $array_fila[1];
        if ($materia == "TUTORIA") {
            //$nom_materia=neteja_apostrofs($nom_materia);
            $codiGrup = $array_fila[2];
            $codiGrup = neteja_apostrofs($codiGrup);
            $idGrup = extreu_id('grups', 'nom', 'idgrups', $codiGrup, $db);

            $idProfessor = extreu_id("equivalencies", "nom_prof_gp", "prof_ga", $array_fila[3], $db);

            if (($idProfessor != "") && ( $idGrup != "")) {
                $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) "
                        . "VALUES (" . $idProfessor . ",1," . $idGrup . ", 1);";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
    }
}

function crea_guardies_ASC($csvFile, $db) {

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='Sense determinar'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $codi_noroomArr = $result->fetch();
    $codi_noroom = $codi_noroomArr['idespais_centre'];

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual='S'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $periodeArr = $result->fetch();
    $periode = $periodeArr['idperiodes_escolars'];

    $data = array();
    $data = netejaCsv($csvFile);
    foreach ($data as $fila) {
        $idMateria = "";
        $idModul = "";
        $idGrup = "";
        $idgrup_materia = "";
        //echo "<br>>".$fila;
        $array_fila = explode(";", $fila);

        // Exdtreiem el codi de la materia/unitats formatives
        $materia = $array_fila[1];
        if (!strcmp($materia, "GUÀRDIA")) {
            $sessions = $array_fila[4];
            $profArr = array();
            $profArr = explode(",", $array_fila[3]);
            foreach ($profArr as $professor) {
                $idProfessor = extreu_id("equivalencies", "nom_prof_gp", "prof_ga", $professor, $db);
                creaGuardies($sessions, $idProfessor, $codi_noroom, $periode, $db);
            }
        }
    }
}

function crea_horaris_gp_mixt($exportsagaxml, $exporthorarixml, $db) {

    introduir_fase('lessons', 0, $db);

    $dates = treuDatesUnitatsFormatives($db);
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];

    if (!extreu_fase('segona_carrega', $db)) {
        buidatge('desdecreahoraris', $db);
    }

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='Sense determinar'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $codi_noroom = $fila['idespais_centre'];

    $resultatconsulta = simplexml_load_file($exportsagaxml);
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);

    if (!$resultatconsulta) {
        echo "Carrega fallida Saga >> " . $exportsagaxml;
    } else if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->lessons->lesson as $classe) {
            $professor = $classe->lesson_teacher['id'];
            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);

            $materia = $classe->lesson_subject['id'];
            $materia = neteja_apostrofs($materia);
            $grup = $classe->lesson_classes['id'];
            $id_materia = extreu_id('equivalencies', 'materia_gp', 'materia_saga', $materia, $db);
            //echo "<br>".$materia." >>> ".$id_materia;;
            // Si és un modul de CCFF LOE
            if ($id_materia != "") {
                //echo "Ha entrat, és un módul del pla ";
                // ********************************************************
                // **********  SI ÉS UN MÒDUL DE CCFF  *****************
                // ******************************************************** 
                $grup = $classe->lesson_classes['id'];
                //Primer hem d'extreure el pla d'estudis en funció de la classe de la taula d'equivalències
                $id_pla = extreu_id('equivalencies', 'grup_gp', 'pla_saga', $grup, $db);
                //echo $id_pla;
                $sql = "SELECT materia_saga FROM equivalencies WHERE materia_gp='" . $materia . "' AND pla_saga='" . $id_pla . "';";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $fila = $result->fetch();
                $id_materia = $fila['materia_saga'];

                //echo "<br>".$classe[id]." ---> ".$professor." >> ".$id_professor." >> ".$materia." >> ".$id_materia." >> ".$grup;

                if (($grup != '') AND ( $id_materia != '')) {
                    // Extreiem les unitats formatives dels móduls
                    $sql = "SELECT A.id_ufs AS id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '" . $id_materia . "' ";
                    $sql .= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                    //echo "<br>".$sql;
                    $resultUfs = $db->prepare($sql);
                    $resultUfs->execute();
                    // Repetir+a el bucle per cada Uf d'aquest módul
                    foreach ($resultUfs->fetchAll() as $UFS) {
                        // Cerquem en la taula grups
                        $sql = "SELECT idgrups FROM grups WHERE codi_grup='" . $grup . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $fila = $result->fetch();
                        $idgrup = $fila['idgrups'];
                        if ($idgrup == '') {
                            // Cerquem en la taula equivalencies
                            $sql = "SELECT grup_ga FROM equivalencies WHERE grup_gp='" . $grup . "';";
                            //echo "<br>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                            $fila = $result->fetch();
                            $idgrup = $fila['grup_ga'];
                        }
                        if ($idgrup != '') {
                            // Afegit per fe comprovacions
                            $sql = "SELECT nom_uf,codi_uf FROM unitats_formatives WHERE idunitats_formatives='" . $UFS['id_ufs'] . "';";
                            //echo "<br>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                            $fila = $result->fetch();
                            //print("<br> dades uf: ".$fila2[0]."  -  ".$fila2[1]);
                            // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                            $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idgrup . "' AND id_mat_uf_pla='" . $UFS['id_ufs'] . "';";
                            //echo "<br>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                            $idGrupMateria = $result->fetch();
                            $present = $result->rowCount();
                            if ($present == 1) {
                                $idgrup_materia = $idGrupMateria['idgrups_materies'];
                                $es_nou_grup_materia = 0;
                            }
                            if ($present == 0) {
                                $es_nou_grup_materia = 1;
                                // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                                // La posteriors comencen d'aquesta data fins a final de curs.
                                if (primera_uf($UFS['id_ufs'], $db) == 1) {
                                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                    $sql .= "VALUES ('" . $idgrup . "','" . $UFS['id_ufs'] . "','" . $data_inici . "','" . $data_tmp2 . "');";
                                    //echo "<br>      ".$sql;
                                } else {
                                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                    $sql .= "VALUES ('" . $idgrup . "','" . $UFS['id_ufs'] . "','" . $data_tmp2 . "','" . $data_fi . "');";
                                }

                                $result = $db->prepare($sql);
                                $result->execute();
                                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idgrup . "' AND id_mat_uf_pla='" . $UFS['id_ufs'] . "';";
//                                echo "<br>>>>>>>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                                $idGrupMateria = $result->fetch();
                                $idgrup_materia = $idGrupMateria['idgrups_materies'];
                            }
                            // Assignem el profe al grup materia si no existeix ja
                            if ($id_professor != '') {
                                if ($es_nou_grup_materia) {
                                    $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";

                                    $result = $db->prepare($sql);
                                    $result->execute();
                                } else {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
                                    if ($comprovacio == -1) {
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia, $id_materia, $db);
                                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                        //echo "<br>>>>>>>".$sql;
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                    } else {
                                        $idgrup_materia = $comprovacio;
                                    }
                                }
                            }
                            foreach ($classe->times->time as $franges) {
                                //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
                                // Extreiem el codi de la franja/dia
                                $dia = $franges->assigned_day;
                                $franja = $franges->assigned_period;
                                $horainici = $franges->assigned_starttime;
                                $horafi = $franges->assigned_endtime;
//**********************
// No mancaria valorar si tot és correcte. 
// Si el dia i la franja són normals i no es tracta d'una segona càrrega                              
//*********************                               
                                if (extreu_fase('segona_carrega', $db)) {
                                    $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
                                    $franja = $fila['id_taula_franges'];
                                }

                                if (($horainici != "") && ($horafi != "")) {
                                    $horainici = $horainici * 100;
                                    $horainici = arregla_hora_gpuntis($horainici);
                                    $horafi = $horafi * 100;
                                    $horafi = arregla_hora_gpuntis($horafi);

                                    $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $idgrup, $db);

                                    $sql = "SELECT A.id_dies_franges AS id_dia_franja FROM dies_franges A, franges_horaries B WHERE ";
                                    $sql .= "A.iddies_setmana='" . $dia . "' AND B.hora_inici='" . $horainici . "' AND B.hora_fi='" . $horafi . "' ";
                                    $sql .= "AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='" . $id_torn . "'; ";
                                    //echo "<br>".$sql;
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
                                    $codi_dia_franja = $fila['id_dia_franja'];
                                } else {
                                    //Per si hi ha torn superposats....
                                    //echo "<br>Ha entrat";
                                    $codi_dia_franja = extreu_codi_franja($dia, $franja, $idgrup, $db);

                                    //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                }


                                // Extreiem l'id de l'espai
                                $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $franges->assigned_room['id'] . "'; ";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $fila = $result->fetch();
                                $codi_espai = $fila['idespais_centre'];
                                if ($codi_espai == "") {
                                    $codi_espai = $codi_noroom;
                                }
                                // Inserim la unitat classe
                                $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" .
                                        $codi_dia_franja . "','" . $codi_espai . "','" . $idgrup_materia . "')";
                                //echo "<br>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                    }
                }
            } else {
                // Comprovem si és una materia
                $id_materia = extreu_id('materia', 'codi_materia', 'idmateria', $materia, $db);
                // ********************************************************
                // **********  SI ÉS UNA MATEIA DE L'ESO  *****************
                // ********************************************************

                if ($id_materia != "") {

                    if (($grup != '') AND ( $id_materia != '')) {
                        // Cerquem en la taula grups
                        $sql = "SELECT idgrups FROM grups WHERE codi_grup='" . $grup . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $fila = $result->fetch();
                        $idgrup = $fila['idgrups'];
                        if ($idgrup == '') {
                            // Cerquem en la taula equivalencies
                            $sql = "SELECT grup_ga FROM equivalencies WHERE grup_gp='" . $grup . "';";
                            //echo "<br>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                            $fila = $result->fetch();
                            $idgrup = $fila['grup_ga'];
                        }
                        if ($idgrup != '') {
                            // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                            $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idgrup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $fila = $result->fetch();
                            $present = $result->rowCount();
                            if ($present == 1) {
                                $idgrup_materia = $fila['idgrups_materies'];
                                $es_nou_grup_materia = 0;
                            }
                            if ($present == 0) {
                                $es_nou_grup_materia = 1;
                                $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('" . $idgrup . "','" . $id_materia . "');";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idgrup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $fila = $result->fetch();
                                $idgrup_materia = $fila['idgrups_materies'];
                            }
                            // Assignem el profe al grup materia si no existeix ja
                            if ($id_professor != '') {
                                if ($es_nou_grup_materia) {
                                    $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                    //echo "<br>>>>>>>".$sql;
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                } else {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
                                    if ($comprovacio == -1) {
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia, $id_materia, $db);
                                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
//                                        echo "<br>>>>>>>".$sql;
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                    } else {
                                        $idgrup_materia = $comprovacio;
                                    }
                                }
                            }
                            foreach ($classe->times->time as $franges) {
                                //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
                                // Extreiem el codi de la franja/dia
                                $dia = $franges->assigned_day;
                                $franja = $franges->assigned_period;
                                $horainici = $franges->assigned_starttime;
                                $horafi = $franges->assigned_endtime;

                                if (extreu_fase('segona_carrega', $db)) {
                                    $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
                                    $franja = $fila['id_taula_franges'];
                                }

                                if (($horainici != "") && ($horafi != "")) {
                                    $horainici = $horainici * 100;
                                    $horainici = arregla_hora_gpuntis($horainici);
                                    $horafi = $horafi * 100;
                                    $horafi = arregla_hora_gpuntis($horafi);

                                    $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $idgrup, $db);

                                    $sql = "SELECT A.id_dies_franges AS idDiesFranges FROM dies_franges A, franges_horaries B WHERE ";
                                    $sql .= "A.iddies_setmana='" . $dia . "' AND B.hora_inici='" . $horainici . "' AND B.hora_fi='" . $horafi . "' ";
                                    $sql .= "AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='" . $id_torn . "'; ";
                                    //echo "<br>   ".$sql;
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
                                    $codi_dia_franja = $fila['idDiesFranges'];
                                } else {
                                    //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                    //Per si hi ha torn superposats....

                                    $codi_dia_franja = extreu_codi_franja($dia, $franja, $idgrup, $db);
                                }
                                //$result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                //$codi_dia_franja=mysql_result($result,0);
                                // Extreiem l'id de l'espai
                                $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $franges->assigned_room['id'] . "'; ";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $fila = $result->fetch();
                                $codi_espai = $fila['idespais_centre'];
                                if ($codi_espai == "") {
                                    $codi_espai = $codi_noroom;
                                }
                                // Inserim la unitat classe
                                $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $idgrup_materia . "')";
                                //echo "<br>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                    }
                }
            }
        }
    }
    crea_tutories_GP($exporthorarixml, $db);
    crea_guardies_GP($exporthorarixml, $db, $codi_noroom);

    introduir_fase('lessons', 1, $db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}

function crea_tutories_GP($exporthorarixml, $db) {

    $resultatconsulta2 = simplexml_load_file($exporthorarixml);

    foreach ($resultatconsulta2->lessons->lesson as $classe) {

        // Valorem si es tracta d'una guardia o una tutoria
        // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
        $professor = $classe->lesson_teacher['id'];
        $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);
        $grup = $classe->lesson_classes['id'];
        $id_grup = extreu_id('grups', 'codi_grup', 'idgrups', $grup, $db);
        if ($id_grup == '') {
            $id_grup = extreu_id('equivalencies', 'grup_gp', 'grup_ga', $grup, $db);
        }
        $materia = $classe->lesson_subject['id'];
        $materia = neteja_apostrofs($materia);
        if (($id_professor != '') AND ( $id_grup != '') AND ( !strcmp($materia, "SU_TUT"))) {

            // Comprovem si no existeix ja
            $sql = "SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='" . $id_professor . "' AND idcarrecs='1' AND idgrups='" . $id_grup . "';";
            $result = $db->prepare($sql);
            $result->execute();
            if ($result->rowCount() < 1) {
                $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('" . $id_professor . "','1','" . $id_grup . "');";
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
    }
}

function crea_guardies_GP($exporthorarixml, $db, $codi_noroom) {

    $resultatconsulta2 = simplexml_load_file($exporthorarixml);

    foreach ($resultatconsulta2->lessons->lesson as $classe) {
        // Valorem si es tracta d'una guardia o una tutoria
        // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
        $professor = $classe->lesson_teacher['id'];
        $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);
        $grup = $classe->lesson_classes['id'];
        $id_grup = extreu_id('grups', 'codi_grup', 'idgrups', $grup, $db);
        if ($id_grup == '') {
            $id_grup = extreu_id('equivalencies', 'grup_gp', 'grup_ga', $grup, $db);
        }
        $materia = $classe->lesson_subject['id'];
        $materia = neteja_apostrofs($materia);
        //echo "<br>>".$id_professor." >> ".$materia;

        if (($id_professor != '') AND ( !strcmp($materia, "SU_GU"))) {
            foreach ($classe->times->time as $franges) {
                $dia = $franges->assigned_day;
                $franja = $franges->assigned_period;
                $codi_dia_franja = extreu_codi_franja_guardies($dia, $franja, $db);
                //               $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$franges->assigned_day."' AND idfranges_horaries='".$franges->assigned_period."' ";
                //               $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                //               $codi_dia_franja=mysql_result($result,0);
                // Extreiem l'id de l'espai
                $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $franges->assigned_room['id'] . "'; ";
                $result = $db->prepare($sql);
                $result->execute();
                $fila = $result->fetch();
                $codi_espai = $fila['idespais_centre'];
                if ($codi_espai == "") {
                    $codi_espai = $codi_noroom;
                }
                // Inserim la unitat classe
                $sql = "INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('" . $id_professor . "','" . $codi_dia_franja . "','" . $codi_noroom . "')";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
    }
}

function crea_horaris_PN_mixt($exportsagaxml, $exporthorarixml, $db) {

    introduir_fase('lessons', 0, $db);

    $dates = treuDatesUnitatsFormatives($db);
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];

    if (!extreu_fase('segona_carrega', $db)) {
        buidatge('desdecreahoraris', $db);
    }

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='Sense determinar'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $codi_noroom = $fila['idespais_centre'];

    $resultatconsulta = simplexml_load_file($exportsagaxml);
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);

    if (!$resultatconsulta) {
        echo "Carrega fallida Saga >> " . $exportsagaxml;
    } else if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        echo "Carregues correctess";
        foreach ($resultatconsulta2->sesionesLectivas->sesion as $franja) {
            //echo "<br>===============================";
            $esMateria = false;
            $esModul = false;
            $sessio = $franja['id'];
            $materia = $franja->materia;
            $professor = $franja->profesor;
            //Treurem l'id del professor de la taual d'equivalències
            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);
            // Comprovem si és matèria o mòdul
            $id_materia = extreu_id('materia', 'codi_materia', 'idmateria', $materia, $db);
            if ($id_materia != "") {
                $esMateria = true;
            } else {
                $id_materia = extreu_id('equivalencies', 'materia_gp', 'materia_saga', $materia, $db);
                if ($id_materia != "") {
                    $esModul = true;
                }
            }

            if ($esMateria) {
//                echo "<br>es materia...";
                //Peñalara 2008
                //$grup=neteja_item_grup_materia($franja->grupo);
                //$grup=$franja->grupo;
                // peñalara posterior 2008
                $grup = neteja_item_grup_materia($franja->grupoMateria, $db);
                $id_grup = extreu_id('grups', 'codi_grup', 'idgrups', $grup, $db);

                //Hem de mirar el grup a equivalencies i sinó a grups
                if ($id_grup == '') {
                    $id_grup = extreu_id('equivalencies', 'grup_gp', 'grup_ga', $grup, $db);
                }

                //Si tots dos existeixen, seguim endavant
                //echo "<br>Materia - grup :".$id_materia." - ".$materia." - ".$id_grup." - ".$grup;
                if (($id_materia != '') AND ( $id_grup != '')) {
                    // Hem de comprovar si aquest grup-materia ja existeix.
                    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                    //echo "<br> Nova".$sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                    $id_grup_materiaArr = $result->fetch();
                    $present = $result->rowCount();
                    if ($present == 1) {
                        $id_grup_materia = $id_grup_materiaArr['idgrups_materies'];
                        $es_nou_grup_materia = 0;
                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                        $id_grup_materia_original = $id_grup_materia;
                        $comprovacio = comprova_desdoblament($id_professor, $id_grup_materia, $db);
                        if ($comprovacio == -1) {
//                                            echo "<br>Id grup materia abans: ".$id_grup_materia_original;
                            $id_grup_materia = treu_darrer_desdoblament($id_grup_materia_original, $db);
//                                            echo "<br>Id grup materia després: ".$id_grup_materia;
                            $id_grup_materia = creadesdoblament($id_grup_materia, $id_materia, $db);
//                                            echo "<br>Id grup materia desprésssss: ".$id_grup_materia;
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $id_grup_materia . "');";
//                                            echo "<br>> DESDOBLAMENT >>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        } else {
                            $idgrup_materia = $comprovacio;
                        }
                    }
                    if ($present == 0) {
                        $es_nou_grup_materia = 1;
                        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                        $sql .= "VALUES ('" . $id_grup . "','" . $id_materia . "','" . $data_inici . "','" . $data_fi . "');";

                        $result = $db->prepare($sql);
                        $result->execute();

                        $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $id_grup_materiaArr = $result->fetch();
                        $id_grup_materia = $id_grup_materiaArr['idgrups_materies'];

                        if ($id_professor != "") {
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $id_grup_materia . "');";
                            //echo "<br>>>>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                    }
                    foreach ($resultatconsulta2->horario->tramo as $horari) {
                        // A Peñalara els dies i franges comencen per 0, però l'aplicació comença a pintar els horaris dels diferents dies per 1
                        // es fa la modificació per tal que em doni la dada correctament
                        $dia = $horari['dia'] + 1;
                        $franja = $horari['indice'] + 1;
                        //echo "<br>>>>>>".$horari[dia]." >> ".$horari[indice];    
                        if (extreu_fase('segona_carrega', $db)) {
                            $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $franjaArr = $result->fetch();
                            $franja = $franjaArr['id_taula_franges'];
                        }

                        $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $franja . "' ";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $codi_dia_franjaArr = $result->fetch();
                        $codi_dia_franja = $codi_dia_franjaArr['id_dies_franges'];
                        foreach ($horari->aula as $horari2) {
                            $sessio2 = $horari2->sesion;
                            //echo "<br>Segona lectura de sessio".$sessio2." >> ".$sessio;
                            if (!strcmp($sessio2, $sessio)) {
                                $espai = $horari2['id'];
                                //echo "<br>>>".$codi_dia_franja." >> ".$dia." >>>>".$hora."<br>";		
                                // Extreiem l'id de l'espai
                                $sql = "SELECT idespais_centre FROM espais_centre WHERE descripcio='" . $espai . "'; ";
                                //echo "<br>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                                $codiEspaiArr = $result->fetch();
                                $codi_espai = $codiEspaiArr['idespais_centre'];
                                if ($codi_espai == "") {
                                    $codi_espai = $codi_noroom;
                                }

                                $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $id_grup_materia . "')";
                                //echo "<br>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                    }
                }
            } else if ($esModul) {
//                echo "<br>es modul...";
                $grup = neteja_item_grup_materia($franja->grupoMateria);
                // Extreiem l'id del módul i l'id del pla d'estudis
                $sql = "SELECT grup_ga,pla_saga FROM equivalencies WHERE grup_gp='" . $grup . "'";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $filaArr = $result->fetch();
                $id_grup = $filaArr['grup_ga'];
                $id_pla = $filaArr['pla_saga'];

                // Amb l'id del pla d'estudis i la materia, podem treure l'id real del mòdul
                $sql = "SELECT materia_saga FROM equivalencies WHERE pla_saga='" . $id_pla . "' AND materia_gp='" . $materia . "'";
                $result = $db->prepare($sql);
                $result->execute();
                $filaArr = $result->fetch();
                $id_modul = $filaArr['materia_saga'];

                //echo "<br>".$sessio." >>>".$grup." ".$id_pla." ".$id_modul."<br>";;
                // per cada unitat formativa el módul, hem de fer tot el procés
                $sql = "SELECT A.id_ufs AS id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '" . $id_modul . "' ";
                $sql .= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                $result = $db->prepare($sql);
                $result->execute();
                // Per cada unitat formativa
                foreach ($result->fetchAll() as $fila) {
                    // Comprovem si aquest binomi grup materia ja existeix
                    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $fila['id_ufs'] . "';";
                    //echo "<br>".$sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                    $id_grup_materiaArr = $result->fetch();
                    $present = $result->rowCount();
                    if ($present >= 1) {
                        $id_grup_materia = $id_grup_materiaArr['idgrups_materies'];
                        $es_nou_grup_materia = 0;
                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                        $id_grup_materia_original = $id_grup_materia;
                        $comprovacio = comprova_desdoblament($id_professor, $id_grup_materia, $db);
                        if ($comprovacio == -1) {
                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                            $id_grup_materia = treu_darrer_desdoblament($id_grup_materia_original, $db);
                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                            $id_grup_materia = creadesdoblament($id_grup_materia, $id_modul, $db);
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $id_grup_materia . "');";
                            //echo "<br>>>>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        } else {
                            $idgrup_materia = $comprovacio;
                        }
                    }
                    if ($present == 0) {
                        $es_nou_grup_materia = 1;
                        // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                        // La posteriors comencen d'aquesta data fins a final de curs.
                        if (primera_uf($fila[0], $db) == 1) {
                            $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                            $sql .= "VALUES ('" . $id_grup . "','" . $fila['id_ufs'] . "','" . $data_inici . "','" . $data_tmp2 . "');";
                        } else {
                            $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                            $sql .= "VALUES ('" . $id_grup . "','" . $fila['id_ufs'] . "','" . $data_tmp2 . "','" . $data_fi . "');";
                        }
                        //echo $sql;
                        $result = $db->prepare($sql);
                        $result->execute();

                        // Extreiem l'identificador d'aquest grup materia
                        $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $fila[0] . "';";
                        //echo "<br>".$sql;
                        $result = $db->prepare($sql);
                        $result->execute();
                        $id_grup_materiaArr = $result->fetch();
                        $id_grup_materia = $id_grup_materiaArr['idgrups_materies'];

                        // Si és un nou grup matèria, li assignem el profesor
                        if ($id_professor != "") {
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $id_grup_materia . "');";
                            //echo "<br>>>>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                    }
                    // Ja sabem que la uf està introduida i que només hi és una vegada   
                    // Ara hem d'introuir el grup matèria
                    foreach ($resultatconsulta2->horario->tramo as $horari) {
                        //echo "<br>Ha entrat 1739";
                        // A Peñalara els dies i franges comencen per 0, però l'aplicació comença a pintar els horaris dels diferents dies per 1
                        // es fa la modificació per tal que em doni la dada correctament
                        $dia = $horari['dia'] + 1;
                        $franja = $horari['indice'] + 1;
                        $franja_tmp = $franja;
                        //echo "<br>franja -->".$franja;

                        if (extreu_fase('segona_carrega', $db)) {
                            $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";

                            $result = $db->prepare($sql);
                            $result->execute();
                            $franjaArr = $result->fetch();
                            $franja = $franjaArr['id_taula_franges'];
                            if ($franja == "") {
                                $franja = $franja_tmp;
                            }
                            //echo "<br>franja -->".$franja;
                        }

                        $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $franja . "' ";
                        //echo "<br>".$sql;
                        $result = $db->prepare($sql);
                        $result->execute();
                        $codi_dia_franjaArr = $result->fetch();
                        $codi_dia_franja = $codi_dia_franjaArr['id_dies_franges'];

                        foreach ($horari->aula as $horari2) {
                            $sessio2 = $horari2->sesion;
                            //echo "<br>Segona lectura de sessio".$sessio2;
                            if (!strcmp($sessio2, $sessio)) {
                                $espai = $horari2['id'];
                                // Inserim el registre que relaciona professor i grup materia

                                // Extreiem l'id de l'espai
                                $sql = "SELECT idespais_centre FROM espais_centre WHERE descripcio='" . $espai . "'; ";
                                //echo "<br>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                                $codi_espaiArr = $result->fetch();
                                $codi_espai = $codi_espaiArr['idespais_centre'];
                                if ($codi_espai == "") {
                                    $codi_espai = $codi_noroom;
                                }

                                $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $id_grup_materia . "')";
                                //echo $sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                    }
                }
            }
        }
    }
    crea_tutories_PN($exporthorarixml, $db);
    crea_guardies_PN($exporthorarixml, $db);

    introduir_fase('lessons', 1, $db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}

function crea_guardies_PN($exporthorarixml, $db) {

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='Sense determinar'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $codi_noroom = $fila['idespais_centre'];

    $resultatconsulta = simplexml_load_file($exporthorarixml);

    if ($resultatconsulta) {
        foreach ($resultatconsulta->horario->tramo as $horari) {
            // A Peñalara els dies i franges comencen per 0, però l'aplicació comença a pintar els horaris dels diferents dies per 1
            // es fa la modificació per tal que em doni la dada correctament
            $dia = $horari['dia'] + 1;
            $franja = $horari['indice'] + 1;
            $franja_tmp = $franja;

            if (extreu_fase('segona_carrega', $db)) {
                $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";

                $result = $db->prepare($sql);
                $result->execute();
                $franjaArr = $result->fetch();
                $franja = $franjaArr['id_taula_franges'];
                if ($franja == "") {
                    $franja = $franja_tmp;
                }
                //echo "<br>franja -->".$franja;
            }

            $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $franja . "' ";
            $result = $db->prepare($sql);
            $result->execute();
            $codi_dia_franjaArr = $result->fetch();
            $codi_dia_franja = $codi_dia_franjaArr['id_dies_franges'];
            foreach ($horari->guardia as $horari2) {
                if (!strcmp($horari2->nombre, "Guàrdia")) {

                    $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $horari2->profesor, $db);
                    $sql = "INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('" . $id_professor . "','" . $codi_dia_franja . "','" . $codi_noroom . "')";
                    //echo "<br>".$sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
}

function crea_tutories_PN($exporthorarixml, $db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    if ($resultatconsulta) {
        foreach ($resultatconsulta->grupos->grupo as $grups) {
            // Extreiem l'id del módul i l'id del pla d'estudis
            $sql = "SELECT grup_ga,pla_saga FROM equivalencies WHERE grup_gp='" . $grups->nombre . "'";
            $result = $db->prepare($sql);
            $result->execute();
            $filaArr = $result->fetch();
            $id_grup = $filaArr['grup_ga'];

            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $grups->profesorTutor, $db);

            if (($id_professor != "") && ($id_grup != "")) {
                $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('" . $id_professor . "','1','" . $id_grup . "');";
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
    }
}

function _profeJaAssignat($idgrupMateria, $idprofessor, $db) {
    $sql = "SELECT COUNT(*) as count FROM prof_agrupament "
            . "WHERE idprofessors = " . $idprofessor . " AND idagrups_materies = " . $idgrupMateria . ";";
    $result = $db->prepare($sql);
    $result->execute();
    if ($result->rowCount() > 0)
        return true;
    else
        return false;
}

function extreu_grup_HW($exporthorarixml, $grupHW, $db) {
    $resultatconsulta4 = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta4) {
        echo "Carrega fallida Saga >> " . $exporthorarixml;
    }
    $abreviatura = 0;
    foreach ($resultatconsulta4->DATOS->GRUPOS->GRUPO as $grup) {
        //echo "<br>".$grup[num_int_gr]." >> ".$grupHW;
        if (!strcmp($grup['num_int_gr'], $grupHW)) {
            $abreviatura = $grup['abreviatura'];
//            echo "<br>Ha entrat >>> ".$abreviatura;
        }
    }
    return $abreviatura;
}

// Modificat per adequar-lo a aSc ioder incorporar desdoblaments en els que coincideixin tant professors com materies
// Seria el cas d'un professor que fa una hora amb tot el grup, una hora amb mig grup i una altra  hora amb
// l'altre mig grup. per tant s'haurien de crear tres arupaments diferents per la mateixa materìa i professor. 
// Per tant tres grups materia    

function comprova_desdoblament($id_professor, $idgrup_materia, $db) {
    $taula = -1;
    // Comprova si aquest grup materia està assignat i si ho està , si és al professor que ens ha arribat
//    echo "<br>".$id_professor." >>".$idgrup_materia;
    // Separo el grup i matèria
    $sql = "SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='" . $idgrup_materia . "';";
//    echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
//    echo "<br>grup".$fila[0]." >> materia: ".$fila[1];
    // Comprovem amb la variable taula si es materia o unitat formativa
    // Extreim el codi de la materia
    $sql2 = "SELECT codi_uf AS codi,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '" . $fila['id_mat_uf_pla'] . "';";
//    echo "<br>".$sql2;
    $result2 = $db->prepare($sql2);
    $result2->execute();
    $present = $result2->rowCount();
    if ($present > 0) {
        $taula = 1;
    } else {
        $sql2 = "SELECT codi_materia AS codi,nom_materia,idmateria FROM materia WHERE idmateria = '" . $fila['id_mat_uf_pla'] . "';";
//     echo "<br>".$sql2;
        $result2 = $db->prepare($sql2);
        $result2->execute();
        $present = $result2->rowCount();
        if ($present > 0) {
            $taula = 2;
        }
    }

    $fila2 = $result2->fetch();
//    echo "<br> La taula és...".$taula;
    if ($taula == 1) {
        $sql = "SELECT A.idunitats_formatives AS id, A.codi_uf FROM unitats_formatives A, grups_materies B, grups C ";
        $sql .= "WHERE codi_uf LIKE '%" . $fila2['codi'] . "%' AND B.id_grups = C.idgrups ";
        $sql .= "AND B.id_mat_uf_pla=A.idunitats_formatives AND C.idgrups='" . $fila['id_grups'] . "' ORDER BY idunitats_formatives ;";
    }
    if ($taula == 2) {
        // Treu totles les materies que tenen el patró
        //$sql = "SELECT idmateria FROM materia WHERE codi_materia LIKE '%".$fila2[0]."%' ORDER BY idmateria ;";

        $sql = "SELECT A.idmateria AS id FROM materia A, grups_materies B, grups C ";
        $sql .= "WHERE codi_materia LIKE '%" . $fila2['codi'] . "%' AND B.id_grups = C.idgrups ";
        $sql .= "AND B.id_mat_uf_pla=A.idmateria AND C.idgrups='" . $fila['id_grups'] . "' ORDER BY idmateria ;";
    }
//    echo "<br>.. Comprova ...".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $assignat = 0;
    foreach ($result->fetchAll() AS $fila3) {
        // Per cada unitat formativa exteriem el grup materia forçant que pertany al grup
        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE ";
        $sql2 .= "(id_grups = '" . $fila['id_grups'] . "' AND id_mat_uf_pla = '" . $fila3['id'] . "');";
        $result2 = $db->prepare($sql2);
        $result2->execute();

        foreach ($result2->fetchAll() AS $fila4) {
            $sql3 = "SELECT idprofessors FROM prof_agrupament WHERE idagrups_materies='" . $fila4['idgrups_materies'] . "';";
//         echo "<br>".$sql3;
            $result3 = $db->prepare($sql3);
            $result3->execute();
            $fila5 = $result3->fetch();
            if (($fila5['idprofessors'] == $id_professor)) {
                $idGrupMateria = $fila4['idgrups_materies'];
//                        echo "<br>".$idGrupMateria;

                $assignat = 1;
            }
        }
    }
    if ($assignat == 0) {
        return -1;
    } else {
        return $idGrupMateria;
    }
}

function creadesdoblament($idgrup_materia, $modul, $db) {
    // rep el el grup materia del darrer desdoblament del grup afectat
    // Separo el grup i matèria
//    echo "<br>>>>>".$idgrup_materia." >>> ".$modul;
    $sql = "SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='" . $idgrup_materia . "';";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();

    // Comprovem amb la variable taula si es materia o unitat formativa
    $sql = "SELECT nom_uf,nom_uf AS nom ,idunitats_formatives AS id FROM unitats_formatives WHERE idunitats_formatives = '" . $fila['id_mat_uf_pla'] . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $present = $result->rowCount();
    $taula = -1;
    if ($present) {
        $taula = 1;
    }
    if ($present == 0) {
        $sql = "SELECT codi_materia,nom_materia AS nom,idmateria AS id FROM materia WHERE idmateria = '" . $fila['id_mat_uf_pla'] . "';";
//        echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
        $present = $result->rowCount();
        if ($present) {
            $taula = 2;
        }
    }


    $fila2 = $result->fetch();


    if ($taula == 1) {
        $sql = "SELECT A.idunitats_formatives AS id,A.codi_uf,A.nom_uf FROM unitats_formatives A,grups_materies B, grups C ";
        $sql .= "WHERE A.nom_uf LIKE '%" . $fila2['nom'] . "%'AND A.idunitats_formatives = B.id_mat_uf_pla ";
        $sql .= "AND B.id_grups = C.idgrups AND C.idgrups= '" . $fila['id_grups'] . "' ";
        $sql .= "ORDER BY idunitats_formatives DESC LIMIT 1;";
//        echo "<br>".$sql;
    }
    if ($taula == 2) {
        // COMENTARI .10/02/2018 He canviat codi_materia per nom_materia per adequar a GPuntis
        $sql = "SELECT A.idmateria AS id,A.codi_materia,A.nom_materia FROM materia A,grups_materies B, grups C ";
        $sql .= "WHERE nom_materia LIKE '%" . $fila2['nom'] . "%'AND A.idmateria = B.id_mat_uf_pla ";
        $sql .= "AND B.id_grups = C.idgrups AND C.idgrups= '" . $fila['id_grups'] . "' ";
        $sql .= "ORDER BY idmateria DESC LIMIT 1;";
    }

    $result = $db->prepare($sql);
    $result->execute();
    $fila3 = $result->fetch();
    //echo "<br>".$sql;
    // Comprovem si el nou nom ja existeix degut a que s'ha creat per un altre grup 
    $id_materia = $fila3['id'];
    //echo "<br>".$fila3[0]." >>> ".$fila3[1]." >>> ".$fila3[2]; 
    $nou_nom = genera_nom_desdoblament($fila3);
    $nou_nom[1] = substr($nou_nom[1], 0, 99);
//    echo "<br>".$nou_nom[0]." >>> ".$nou_nom[1]." >>> ".$nou_nom[2]; 
    $sql = "SELECT COUNT(codi_materia) AS count FROM moduls_materies_ufs WHERE codi_materia = '" . $nou_nom[1] . "';";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila4 = $result->fetch();
    if ($fila4['count'] == 0) {
        $id_pla = extreu_id('moduls_materies_ufs', 'id_mat_uf_pla', 'idplans_estudis', $fila2['id'], $db);

        // Situem a la taula moduls_materies_ufs
        $sql = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
        $sql .= "VALUES (" . $id_pla . ",'" . $nou_nom[1] . "','S');";
//        echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();

        $id_materia = extreu_id('moduls_materies_ufs', 'codi_materia', 'id_mat_uf_pla', $nou_nom[1], $db);


        if ($taula == 1) {
            $sql = "INSERT INTO unitats_formatives(idunitats_formatives,codi_uf,nom_uf,data_inici,data_fi) ";
            $sql .= "VALUES ('" . $id_materia . "','" . $nou_nom[1] . "','" . $nou_nom[2] . "','" . $fila[2] . "','" . $fila[3] . "');";
//            echo "<br>".$sql;
            $result = $db->prepare($sql);
            $result->execute();

            $sql = "INSERT INTO moduls_ufs(id_moduls,id_ufs) VALUES ('" . $modul . "','" . $id_materia . "');";
            //echo "<br>".$sql;
            $result = $db->prepare($sql);
            $result->execute();
        }
        if ($taula == 2) {

            $sql = "INSERT INTO materia(idmateria,codi_materia,nom_materia) ";
            $sql .= "VALUES ('" . $id_materia . "','" . $nou_nom[1] . "','" . $nou_nom[2] . "');";
            //echo "<br>".$sql;
            $result = $db->prepare($sql);
            $result->execute();
        }
    } else {
        //Com que existeix, trec l'id i l'assino al grup geneant un nou grup materia
        $sql = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '" . $nou_nom[1] . "';";
        //echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
        $fila4 = $result->fetch();
        $id_materia = $fila4['id_mat_uf_pla'];
    }
    if ($taula == 1) {
        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql .= "VALUES ('" . $fila['id_grups'] . "','" . $id_materia . "','" . $fila['data_inici'] . "','" . $fila['data_fi'] . "');";
    } else {
        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) ";
        $sql .= "VALUES ('" . $fila['id_grups'] . "','" . $id_materia . "');";
    }

    //echo "<br>".$sql;        
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
    $sql .= "(id_grups = '" . $fila['id_grups'] . "' AND id_mat_uf_pla = '" . $id_materia . "');";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $grup_materiav = $result->fetch();

    $idgrup_materia = $grup_materiav['idgrups_materies'];
    //echo "<br> id grup materia".$idgrup_materia;
    return $idgrup_materia;
}

function treu_darrer_desdoblament($idgrup_materia, $db) {

    // Separo el grup i matèria
    $sql = "SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='" . $idgrup_materia . "';";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();

    // Comprovem amb la variable taula si es materia o unitat formativa
    $sql = "SELECT codi_uf AS codi,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '" . $fila['id_mat_uf_pla'] . "';";
//    echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $present = $result->rowCount();
    if ($present) {
        $taula = 1;
    }
    if ($present == 0) {
        $sql = "SELECT codi_materia AS codi,nom_materia,idmateria FROM materia WHERE idmateria = '" . $fila['id_mat_uf_pla'] . "';";
//        echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
        $present = $result->rowCount();
        if ($present) {
            $taula = 2;
        }
    }
//    echo "<br>>>>".$taula;
    $fila2 = $result->fetch();

    if ($taula == 1) {
        $sql = "SELECT A.idgrups_materies AS id, C.codi_uf ";
        $sql .= "FROM grups_materies A, grups B, unitats_formatives C ";
        $sql .= "WHERE C.codi_uf LIKE '%" . $fila2['codi'] . "%' AND B.idgrups = " . $fila['id_grups'] . " AND ";
        $sql .= " B.idgrups = A.id_grups AND C.idunitats_formatives = A.id_mat_uf_pla";
        $sql .= " ORDER BY idunitats_formatives DESC LIMIT 1;";
    }
    if ($taula == 2) {
        $sql = "SELECT A.idgrups_materies AS id, C.nom_materia ";
        $sql .= "FROM grups_materies A, grups B, materia C ";
        // Al fer peñalara he canviat nom_materia per codi matèria ja que 
        // A nom_materia se li afegeixen parèntesi. Pot ser a GP untis salti el problema
        // Hauria de ser el codi el que agafem
        $sql .= "WHERE C. codi_materia LIKE '%" . $fila2['codi'] . "%' AND B.idgrups =" . $fila['id_grups'] . " AND ";
        $sql .= " B.idgrups = A.id_grups AND C.idmateria = A.id_mat_uf_pla";
        $sql .= " ORDER BY idmateria DESC LIMIT 1;";
    }
//    echo "<br> treu...".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila3 = $result->fetch();

    return $fila3['id'];
}

function genera_nom_desdoblament($vector) {
//    echo "<br>vector que arriba".$vector[0]." ".$vector[1]." ".$vector[2];
    if (substr($vector[1], 0, 4) != "DESD") {
        //Si és el primer desdoblament
        $vector[1] = "DESD_" . $vector[1];
        $vector[2] = "DESD_" . $vector[2];
        //echo "<br>".$vector[1]." >> ".$vector[2];
    } else {
        //Si és el segon desdoblament
        $arrel = explode("_", $vector[1]);
        $arrel2 = explode("_", $vector[2]);
        $index = strlen($arrel[0]);
        if ($index == 4) {
            $arrel[0] = "DESD1";
            $arrel2[0] = "DESD1";
        } else {
            if ($index == 5) { // Si és del primers deu desdoblament
                $repeticio = intval(substr($arrel[0], 4, 1));
            } else {  // Si hi ha més de deu desdoblaments. Axò passa quan s'obliden una reunió o quelcom semblant
                $repeticio = intval(substr($arrel[0], 4, 2));
            }
            $repeticio ++;
            $arrel[0] = "DESD" . $repeticio;
            $arrel2[0] = "DESD" . $repeticio;
        }
        $vector[1] = implode("_", $arrel);
        $vector[2] = implode("_", $arrel2);
    }
    return $vector;
}

function treuDatesUnitatsFormatives($db) {
    $intervalDies = "350 days";
    $dates = array();

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S' ;";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $periode = $fila['idperiodes_escolars'];

    // Extreiem data inici i data fi dels periodes escolars
    $sql = "SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S';";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $dates[0] = $fila['data_inici'];
    $data_tmp = date_create($dates[0]);
    date_add($data_tmp, date_interval_create_from_date_string($intervalDies));
    $dates[1] = date_format($data_tmp, "Y-m-d");
    $dates[2] = $fila['data_fi'];

    return $dates;
}

function crea_horaris_KW_mixt($exportsagaxml, $exporthorarixml, $db) {

    introduir_fase('lessons', 0, $db);

    $dates = treuDatesUnitatsFormatives($db);
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];

    if (!extreu_fase('segona_carrega', $db)) {
        buidatge('desdecreahoraris', $db);
        //echo "<br>Tot Netejat";
    }

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $codi_noroomArr = $result->fetch();
    $codi_noroom = $codi_noroomArr['idespais_centre'];

    $resultatconsulta = simplexml_load_file($exportsagaxml);
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    $resultatconsulta3 = simplexml_load_file($exporthorarixml);

    if (!$resultatconsulta) {
        echo "Carrega fallida Saga >> " . $exportsagaxml;
    } else if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->SOLUCT->SOLUCF as $unitatClasse) {
            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $unitatClasse['PROF'], $db);
            $idGrup = extreu_id('equivalencies', 'grup_gp', 'grup_ga', $unitatClasse['CODGRUPO'], $db);
            $id_espai = extreu_id('espais_centre', 'codi_espai', 'idespais_centre', $unitatClasse['AULA'], $db);
            if ($id_espai == "") {
                $id_espai = $codi_noroom;
            }

            // Extreiem el codi de la materia/unitats formatives
            $codiMateria = $unitatClasse['ASIG'];
            $idMateria = extreu_id('materia', 'codi_materia', 'idmateria', $codiMateria, $db);
            if ($idMateria == "") {
                $idModul = extreu_id('equivalencies', 'materia_gp', 'materia_saga', $codiMateria, $db);
                if ($idModul != "") {
                    $idPla = extreu_id('equivalencies', 'materia_gp', 'pla_saga', $codiMateria, $db);
                }
            }

            $sql = "SELECT id_dies_franges,idperiode_escolar FROM dies_franges WHERE iddies_setmana='" . $unitatClasse['DIA'] . "' AND idfranges_horaries='" . $unitatClasse['HORA'] . "';";
            $result = $db->prepare($sql);
            $result->execute();
            $fila = $result->fetch();
            $idDiaFranja = $fila['id_dies_franges'];
            $periode = $fila['idperiode_escolar'];

            // Comprovem dades
//            echo "<br> id espai".$id_espai;
            // Gestionem el grup-matèria
            if (($idMateria != "") AND ( $idGrup != "")) {
                // Comprovem que el grup materia no existeix
                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $idMateria . "';";
                //echo "<br>".$sql;
                $result = $db->prepare($sql);
                $result->execute();
                $present = $result->rowCount();
                $fila2 = $result->fetch();
                $idgrup_materia = $fila2['idgrups_materies'];
                if ($present == 1) {
                    $es_nou_grup_materia = 0;
                    //$idgrup_materia = creadesdoblament($idgrup_materia,$materia,$idProfessor);
                }
                if ($present == 0) {
                    $es_nou_grup_materia = 1;
                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('" . $idGrup . "','" . $idMateria . "');";
                    //echo "<br>".$sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $idMateria . "';";
                    $result = $db->prepare($sql);
                    $result->execute();
                    $idgrup_materiaArr = $result->fetch();
                    $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                }

                $id_materia = $idMateria;
                // Assignem el profe al grup materia si no existeix ja
                if ($id_professor != '') {
                    if ($es_nou_grup_materia) {
                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                        //echo "<br>>>>>>>".$sql;
                        $result = $db->prepare($sql);
                        $result->execute();
                    } else {
                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                        $idgrup_materia_original = $idgrup_materia;
                        $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
                        if ($comprovacio == -1) {
                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                            $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                            $idgrup_materia = creadesdoblament($idgrup_materia, $id_materia, $db);
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
//                                        echo "<br>>>>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        } else {
                            $idgrup_materia = $comprovacio;
                        }
                    }
                }

                // Generem la unitat classe  
                // Quan no està assignada la posa amb dia 0, i franja 0 i per tant no ho troba  a la taula
                // de franges
                if ($idDiaFranja != "") {
                    $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                    $sql .= "('" . $idDiaFranja . "','" . $id_espai . "','" . $idgrup_materia . "')";
                    //echo "<br>" . $sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                }
            } else if (($idModul != "") AND ( $idGrup != "")) {
//                echo "<br>Hem entrat";
                $arrayUfs = array();
                for ($i = 0; $i < count($arrayUfs); $i++) {
                    $arrayUfs[$i][0] = "";
                    $arrayUfs[$i][1] = "";
                }
                $i = 0;
                // Extreiem les unitats formatives dels móduls eliminnat els "DESD" per no fer massa crides
                //$sql = "SELECT id_ufs FROM moduls_ufs WHERE id_moduls =  '".$idModul."'; ";
                $sql = "SELECT A.id_ufs as idufs FROM moduls_ufs A,unitats_formatives B WHERE A.id_moduls =  '" . $idModul . "' AND ";
                $sql .= "A.id_ufs = B.idunitats_formatives AND B.nom_uf NOT LIKE '%DESD%' ";
                //echo "<br>".$sql;
                $resultat = $db->prepare($sql);
                $resultat->execute();
                // Repetir+a el bucle per cada Uf d'aquest módul
                foreach ($resultat->fetchAll() as $fila2) {
                    // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim

                    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $fila2['idufs'] . "';";
                    //echo "<br>".$sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                    $present = $result->rowCount();
                    $fila3 = $result->fetch();
                    ;
                    $idgrup_materia = $fila3['idgrups_materies'];
                    if ($present == 1) {
                        //if ($codiMateria == "VEH001") {echo "<br>Va a desdoblament********************";}
                        $es_nou_grup_materia = 0;
                        //$idgrup_materia = creadesdoblament($idgrup_materia,$idModul,$idProfessor);
                    }
                    if ($present == 0) {
                        $es_nou_grup_materia = 1;
                        // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                        // La posteriors comencen d'aquesta data fins a final de curs.
                        if (primera_uf($fila2['idufs'], $db) == 1) {
                            $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                            $sql .= "VALUES ('" . $idGrup . "','" . $fila2['idufs'] . "','" . $data_inici . "','" . $data_tmp2 . "');";
                            //if ($codiMateria == "VEH001") {echo "<br>".$sql;} 
                        } else {
                            $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                            $sql .= "VALUES ('" . $idGrup . "','" . $fila2['idufs'] . "','" . $data_tmp2 . "','" . $data_fi . "');";
                            //if ($codiMateria == "VEH001") {echo "<br>>>>>".$sql;} 
                        }

                        $resultat = $db->prepare($sql);
                        $resultat->execute();
                        $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $idGrup . "' AND id_mat_uf_pla='" . $fila2['idufs'] . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $idgrup_materiaArr = $result->fetch();
                        $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                    }
                    $arrayUfs[$i][0] = $idgrup_materia;
                    $arrayUfs[$i][1] = $es_nou_grup_materia;
                    $i++;
                }

                for ($i = 0; $i < count($arrayUfs); $i++) {
                    $idgrup_materia = $arrayUfs[$i][0];
                    // Assignem el profe al grup materia si no existeix ja
                    if ($id_professor != '') {
                        if ($es_nou_grup_materia) {
                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                            //echo "<br>>>>>>>".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();
                        } else {
                            // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                            $idgrup_materia_original = $idgrup_materia;
                            $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
                            if ($comprovacio == -1) {
                                //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                                //echo "<br>Id grup materia després: ".$idgrup_materia;
                                $idgrup_materia = creadesdoblament($idgrup_materia, $idModul, $db);
                                $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
//                                        echo "<br>>>>>>>".$sql;
                                $result = $db->prepare($sql);
                                $result->execute();
                            } else {
                                $idgrup_materia = $comprovacio;
                            }
                        }
                    }
                    // Generem la unitat classe 
                    // Quan no està assignada la posa amb dia 0, i franja 0 i per tant no ho troba  a la taula
                    // de franges
                    if ($idDiaFranja != "") {
                        $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                        $sql .= "('" . $idDiaFranja . "','" . $id_espai . "','" . $idgrup_materia . "')";
                        $resultat = $db->prepare($sql);
                        $resultat->execute();
                    }
                }
            }
        }

        crea_guardies_KW($exporthorarixml, $db);
        introduir_fase('lessons', 1, $db);
        $page = "./menu.php";
        $sec = "0";
        header("Refresh: $sec; url=$page");
    }
}


function crea_horaris_HW_mixt($exportsagaxml, $exporthorarixml, $db) {

    introduir_fase('lessons', 0, $db);

    $dates = treuDatesUnitatsFormatives($db);
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];


    if (!extreu_fase('segona_carrega', $db)) {
        buidatge('desdecreahoraris', $db);
        //echo "<br>Tot Netejat";
    }

    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result = $db->prepare($sql);
    $result->execute();
    $codi_noroomArr = $result->fetch();
    $codi_noroom = $codi_noroomArr['idespais_centre'];

    $resultatconsulta = simplexml_load_file($exportsagaxml);
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    $resultatconsulta3 = simplexml_load_file($exporthorarixml);

    if (!$resultatconsulta) {
        echo "Carrega fallida Saga >> " . $exportsagaxml;
    } else if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->HORARIOS->HORARIOS_PROFESORES->HORARIO_PROF as $professor) {
            //echo "<br> Nombre professor: ".$professor[hor_num_int_pr];
            $prof = $professor['hor_num_int_pr'];
            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $prof, $db);
            //$id_grup = extreu_id('equivalencies','grup_gp','grup_ga',$grup);
            foreach ($professor->ACTIVIDAD as $uniclasse) {
                //echo "<br> >>>> Nombre activitat: ".$uniclasse[num_act];
                $materia = $uniclasse['asignatura'];
                //$materia=neteja_apostrofs($materia);
                $id_materia = extreu_id('equivalencies', 'materia_gp', 'materia_saga', $materia, $db);
                if ($id_materia != "") {
                    $esloe = 1;
                } else {
                    $esloe = 0;
                    $id_materia = extreu_id('materia', 'codi_materia', 'idmateria', $materia, $db);
                }
                foreach ($uniclasse->GRUPOS_ACTIVIDAD as $grups) {
                    if ($grups['tot_gr_act'] == 0) {
                        break;
                    } else if ($grups['tot_gr_act'] == 1) {
                        $nom_curt_grup = extreu_grup_HW($exporthorarixml, $grups['grupo_1'], $db);
                    } else {
                        $nombre_grups = $grups['tot_gr_act'];

                        $codi_agrupament = "";
                        for ($i = 1; $i <= $nombre_grups; $i++) {
                            $grup = $grups['grupo_' . $i];
                            if ($grup == "") {
                                break;
                            }
                            $nom_curt_grup = extreu_grup_HW($exporthorarixml, $grup, $db);
                            if ($i == 1) {
                                $codi_agrupament = $nom_curt_grup;
                            } else {
                                $codi_agrupament = $codi_agrupament . "_" . $nom_curt_grup;
                            }
                        }
                        $nom_curt_grup = $codi_agrupament;
                    }

                    $id_grup = extreu_id('grups', 'codi_grup', 'idgrups', $nom_curt_grup, $db);
                }
//                echo "<br>".$prof." >> ".$materia." >> ".$nom_curt_grup;
                if ($esloe) {
                    // ********************************************************
                    // **********  SI ÉS UN MÒDUL DE CCFF  *****************
                    // ******************************************************** 
                    $id_pla = extreu_id('equivalencies', 'materia_gp', 'pla_saga', $materia, $db);
                    //echo $id_pla;
                    $sql = "SELECT materia_saga FROM equivalencies WHERE materia_gp='" . $materia . "' AND pla_saga='" . $id_pla . "';";
                    $resultat = $db->prepare($sql);
                    $resultat->execute();
                    $fila = $resultat->fetch();
                    ;
                    // Treu l'id del módul a gassist
                    $id_materia = $fila['materia_saga'];

                    //echo "<br>".$classe[id]." ---> ".$professor." >> ".$id_professor." >> ".$materia." >> ".$id_materia." >> ".$grup;

                    if (($id_grup != '') AND ( $id_materia != '')) {
                        // Extreiem les unitats formatives dels móduls sense comptar els dedoblaments

                        $sql = "SELECT A.id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '" . $id_materia . "' ";
                        $sql .= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                        //echo "<br>".$sql;
                        $resultat = $db->prepare($sql);
                        $resultat->execute();
                        // Repetir+a el bucle per cada Uf d'aquest módul
                        foreach ($resultat->fetchAll() as $fila) {
                            // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                            $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $fila[0] . "';";
                            //echo "<br> comprovem si ja existeix".$sql;
                            $result = $db->prepare($sql);
                            $result->execute();

                            $present = $result->rowCount();
                            if ($present == 1) {
                                $idgrup_materiaArr = $result->fetch();
                                $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                                $es_nou_grup_materia = 0;
                            }
                            if ($present == 0) {
                                $es_nou_grup_materia = 1;
                                // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                                // La posteriors comencen d'aquesta data fins a final de curs.
                                if (primera_uf($fila[0], $db) == 1) {
                                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                    $sql .= "VALUES ('" . $id_grup . "','" . $fila[0] . "','" . $data_inici . "','" . $data_tmp2 . "');";
                                    //echo "<br>      ".$sql;
                                } else {
                                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                    $sql .= "VALUES ('" . $id_grup . "','" . $fila[0] . "','" . $data_tmp2 . "','" . $data_fi . "');";
                                }

                                $result = $db->prepare($sql);
                                $result->execute();
                                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $fila[0] . "';";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $idgrup_materiaArr = $result->fetch();
                                $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                                //echo "<br>Id grup materia molt abans: ".$idgrup_materia;
                            }
                            // Assignem el profe al grup materia si no existeix ja
//                            echo "<br>Professor: ".$id_professor; 
//                             echo "<br> es nou grup materia?".$es_nou_grup_materia;
//                            echo "<br>======";
//                            echo "<br>materia: ".$id_materia." grup: ".$id_grup." grupmateria: ".$idgrup_materia." professor: ".$id_professor;
                            if ($id_professor != '') {
                                if ($es_nou_grup_materia) {
                                    $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                } else {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
//                                    echo "<br> Si no és nou comencem a comprovar el desdoblament";
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
//                                    echo "<br>comporvació: ".$comprovacio;
                                    if ($comprovacio == -1) {
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia, $id_materia, $db);
                                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                        //echo "<br>Inserim el nou desdoblament" . $sql;
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                    } else {
                                        $idgrup_materia = $comprovacio;
                                    }
                                }
                            }
//                            echo "<br>materia: ".$id_materia." grup: ".$id_grup." grupmateria: ".$idgrup_materia." professor: ".$id_professor;
                            foreach ($resultatconsulta3->DATOS->TRAMOS_HORARIOS->TRAMO as $franges) {
                                if (!strcmp($uniclasse['tramo'], $franges['num_tr'])) {
                                    $dia = $franges['numero_dia'];
                                    $horainici = $franges['hora_inicio'];
                                    $horafi = $franges['hora_final'];
                                    $horainici = $horainici . ":00";
                                    if (strlen($horainici) == 7) {
                                        str_pad($horainici, 8, "0", STR_PAD_LEFT);
                                    }
                                    $horafi = $horafi . ":00";
                                    if (strlen($horafi) == 7) {
                                        str_pad($horafi, 8, "0", STR_PAD_LEFT);
                                    }

                                    if (extreu_fase('segona_carrega', $db)) {
                                        $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                        $franjaArr = $result->fetch();
                                        $franja = $franjaArr['id_taula_franges'];
                                    }

                                    if (($horainici != "") && ($horafi != "")) {
                                        $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $id_grup, $db);

                                        $sql = "SELECT A.id_dies_franges AS idDiaFranja FROM dies_franges A, franges_horaries B WHERE ";
                                        $sql .= "A.iddies_setmana='" . $dia . "' AND B.hora_inici='" . $horainici . "' AND B.hora_fi='" . $horafi . "' ";
                                        $sql .= "AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='" . $id_torn . "'; ";
                                        //echo "<br>".$sql;
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                        $codi_dia_franjaArr = $result->fetch();
                                        $codi_dia_franja = $codi_dia_franjaArr['idDiaFranja'];
                                    } else {
                                        //Per si hi ha torn superposats....
                                        $codi_dia_franja = extreu_codi_franja($dia, $franja, $id_grup, $db);

                                        //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                    }

                                    // Extreiem l'id de l'espai
                                    $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $uniclasse['aula'] . "'; ";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $codi_espaiArr = $result->fetch();
                                    $codi_espai = $codi_espaiArr['idespais_centre'];
                                    if ($codi_espai == "") {
                                        $codi_espai = $codi_noroom;
                                    }
                                    // Inserim la unitat classe
                                    $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $idgrup_materia . "')";
//                                    echo "<br>".$sql;
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                }
                            }
                        }
                    }
                } else {
                    // Comprovem si és una materia
                    // ********************************************************
                    // **********  SI ÉS UNA MATERIA DE L'ESO  *****************
                    // ********************************************************
                        if ($id_professor == 815){
                        echo "<br>".$id_grup." >> ".$nom_curt_grup." >> ".$id_materia;
                        }
                    if ($id_materia != "") {

                        if (($nom_curt_grup != '') AND ( $id_materia != '')) {
                            if ($id_grup != '') {
                                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                                $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $idgrup_materiaArr = $result->fetch();
                                $present = $result->rowCount();
                                if ($present == 1) {
                                    $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                                    $es_nou_grup_materia = 0;
                                }
                                if ($present == 0) {
                                    $es_nou_grup_materia = 1;
                                    $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('" . $id_grup . "','" . $id_materia . "');";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "' AND id_mat_uf_pla='" . $id_materia . "';";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $idgrup_materiaArr = $result->fetch();
                                    $idgrup_materia = $idgrup_materiaArr['idgrups_materies'];
                                }

                                // Assignem el profe al grup materia si no existeix ja
                                if ($id_professor != '') {
                                    if ($es_nou_grup_materia) {
                                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                        //echo "<br>>>>>>>" . $sql;
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                    } else {
                                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                        $idgrup_materia_original = $idgrup_materia;
                                        $comprovacio = comprova_desdoblament($id_professor, $idgrup_materia, $db);
                                        if ($comprovacio == -1) {
                                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                            $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original, $db);
                                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                                            $idgrup_materia = creadesdoblament($idgrup_materia, $id_materia, $db);
                                            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                            //echo "<br>>>>>>>" . $sql;
                                            $result = $db->prepare($sql);
                                            $result->execute();
                                        } else {
                                            $idgrup_materia = $comprovacio;
                                        }
                                    }
                                }
                                foreach ($resultatconsulta3->DATOS->TRAMOS_HORARIOS->TRAMO as $franges) {
                                    if (!strcmp($uniclasse['tramo'], $franges['num_tr'])) {
                                        $dia = $franges['numero_dia'];
                                        $horainici = $franges['hora_inicio'];
                                        $horafi = $franges['hora_final'];
                                        $horainici = $horainici . ":00";
                                        if (strlen($horainici) == 7) {
                                            str_pad($horainici, 8, "0", STR_PAD_LEFT);
                                        }
                                        $horafi = $horafi . ":00";
                                        if (strlen($horafi) == 7) {
                                            str_pad($horafi, 8, "0", STR_PAD_LEFT);
                                        }

                                        if (extreu_fase('segona_carrega', $db)) {
                                            $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='" . $franja . "';";
                                            $result = $db->prepare($sql);
                                            $result->execute();
                                            $franjaArr = $result->fetch();
                                            $franja = $franjaArr['id_taula_franges'];
                                        }

                                        if (($horainici != "") && ($horafi != "")) {
                                            $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $id_grup, $db);

                                            $sql = "SELECT A.id_dies_franges AS idDiesFranges FROM dies_franges A, franges_horaries B WHERE ";
                                            $sql .= "A.iddies_setmana='" . $dia . "' AND B.hora_inici='" . $horainici . "' AND B.hora_fi='" . $horafi . "' ";
                                            $sql .= "AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='" . $id_torn . "'; ";
                                            //echo "<br>".$sql;
                                            $result = $db->prepare($sql);
                                            $result->execute();
                                            $codi_dia_franjaArr = $result->fetch();
                                            $codi_dia_franja = $codi_dia_franjaArr['idDiesFranges'];
                                        } else {
                                            //Per si hi ha torn superposats....
                                            $codi_dia_franja = extreu_codi_franja($dia, $franja, $id_grup, $db);

                                            //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                        }

                                        // Extreiem l'id de l'espai
                                        $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $uniclasse['aula'] . "'; ";
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                        $codi_espaiArr = $result->fetch();
                                        $codi_espai = $codi_espaiArr['idespais_centre'];
                                        if ($codi_espai == "") {
                                            $codi_espai = $codi_noroom;
                                        }
                                        // Inserim la unitat classe
                                        $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $idgrup_materia . "')";
                                        $result = $db->prepare($sql);
                                        $result->execute();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // Si és un modul de CCFF LOE
        }
    }
    crea_tutories_HW($exporthorarixml, $db);
    crea_guardies_HW($exporthorarixml, $db);
    introduir_fase('lessons', 1,$db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}

function crea_tutories_HW($exporthorarixml, $db) {

    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    if ($resultatconsulta2) {

        foreach ($resultatconsulta2->DATOS->GRUPOS->GRUPO as $grup_xml) {
            // Valorem si es tracta d'una guardia o una tutoria
            // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
            $professor = $grup_xml['num_pr_tutor_principal'];
            $id_professor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);

            $grup_tutor = $grup_xml['num_int_gr'];
            $id_grup = extreu_id('equivalencies', 'grup_gp', 'grup_ga', $grup_tutor, $db);
            if (($id_grup != '') AND ( $id_professor != '')) {
                $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) VALUES (" . $id_professor . ",1," . $id_grup . ",1);";
                //echo "<br>" . $sql;
                $result = $db->prepare($sql);
                $result->execute();
            }
        }
    }
}

function crea_guardies_KW($exporthorarixml, $db) {

    $idGuardia = "GUA";
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    if ($resultatconsulta2) {

        foreach ($resultatconsulta2->SOLUCT->SOLUCF as $guardia) {
            if (!strcmp($guardia['ASIG'], $idGuardia)) {
                $aula = $guardia['AULA'];
                $idAula = extreu_id('espais_centre', 'codi_espai', 'idespais_centre', $aula, $db);
                $dia = $guardia['DIA'];
                $hora = $guardia['HORA'];
                $sql = "SELECT id_dies_franges FROM dies_franges WHERE "
                        . "iddies_setmana = " . $dia . " AND idfranges_horaries = " . $hora . ";";
                $result = $db->prepare($sql);
                $result->execute();
                $diaFranjaArr = $result->fetch();
                $diaFranja = $diaFranjaArr['id_dies_franges'];
                $professor = $guardia['PROF'];
                $idProfessor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $professor, $db);

                if ($idProfessor != '' && $diaFranja != '' && $idAula != '') {
                    $sql = " INSERT INTO guardies(idprofessors, id_dies_franges ,idespais_centre) "
                            . "VALUES (" . $idProfessor . "," . $diaFranja . "," . $idAula . ")";
//                    echo "<br>" . $sql;
                    $result = $db->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
}

function crea_guardies_HW($exporthorarixml, $db) {

    $idGuardia = 0;
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    if ($resultatconsulta2) {

        foreach ($resultatconsulta2->DATOS->ASIGNATURAS->ASIGNATURA as $materia) {

            if ($materia['abreviatura'] == "GUA")
                $idGuardia = $materia['num_int_as'];
        }
        foreach ($resultatconsulta2->HORARIOS->HORARIOS_ASIGNATURAS->HORARIO_ASIG as $guardia) {
            if (!strcmp($guardia['hor_num_int_as'], $idGuardia)) {
                foreach ($guardia->ACTIVIDAD as $sessions) {
                    $franja = extreuDadesHW(0, $sessions['tramo'], $exporthorarixml, $db);
                    $professor = extreuDadesHW(1, $sessions['profesor'], $exporthorarixml, $db);
                    $espai = extreuDadesHW(2, $sessions['aula'], $exporthorarixml, $db);

                    $sql = " INSERT INTO guardies(idprofessors, id_dies_franges ,idespais_centre) "
                            . "VALUES (" . $professor . "," . $franja . "," . $espai . ")";
                    $result = $db->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
}

function extreuDadesHW($codi, $valor, $exporthorarixml, $db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    if ($resultatconsulta) {
        switch ($codi) {
            case 0:
                foreach ($resultatconsulta->DATOS->TRAMOS_HORARIOS->TRAMO as $franges) {
                    if (!strcmp($valor, $franges['num_tr'])) {
                        $dia = $franges['numero_dia'];
                        $sql = "SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris = '" . $franges['numero_hora'] . "';";
                        //echo "<br>" . $sql;
                        $result = $db->prepare($sql);
                        $result->execute();
                        $codifranjaArr = $result->fetch();
                        $codifranja = $codifranjaArr['id_taula_franges'];

                        $sql = "SELECT id_dies_franges FROM dies_franges WHERE ";
                        $sql .= "iddies_setmana='" . $dia . "'  ";
                        $sql .= "AND idfranges_horaries = '" . $codifranja . "'; ";
                        //echo "<br>".$sql;
                        $result = $db->prepare($sql);
                        $result->execute();
                        $codi_dia_franjaArr = $result->fetch();
                        $codi_dia_franja = $codi_dia_franjaArr['id_dies_franges'];
                        return $codi_dia_franja;
                    }
                }
                break;
            case 1:
                foreach ($resultatconsulta->DATOS->PROFESORES->PROFESOR as $professor) {
                    if (!strcmp($valor, $professor['num_int_pr'])) {
                        $sql = " SELECT prof_ga FROM equivalencies WHERE codi_prof_gp = '" . $valor . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $fila = $result->fetch();
                        return $fila['prof_ga'];
                    }
                }
                break;
            case 2:
                foreach ($resultatconsulta->DATOS->AULAS->AULA as $espai) {
                    if (!strcmp($valor, $espai['num_int_au'])) {
                        $sql = " SELECT idespais_centre FROM espais_centre WHERE codi_espai = '" . $valor . "';";
                        $result = $db->prepare($sql);
                        $result->execute();
                        $fila = $result->fetch();
                        return $fila['idespais_centre'];
                    }
                }
                break;
        }
    }
}

function primera_uf($id_uf, $db) {

// Aquesta funció retorna el darrer nombre del codi de la unitat formativa per saber
// Si és la primera uf d'un módul

    $sql = "SELECT codi_uf FROM unitats_formatives WHERE idunitats_formatives='" . $id_uf . "';";
//echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
//echo "<br>".$fila[0];
    return substr($fila['codi_uf'], -1);
}

?>