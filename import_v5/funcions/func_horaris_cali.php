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
// 					CREACIÓ D'HORARIS INSTITUT CAL.LIPOLIS
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function genera_horaris_cali($db) {

    $sql = "SELECT data_inici,data_fi FROM periodes_escolars WHERE actual = 'S';";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $dataInici = $fila['data_inici'];
    $dataFi = $fila['data_fi'];

    $sql = "SELECT idcarrecs FROM carrecs WHERE nom_carrec = 'TUTOR';";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $idCarrec = $fila['idcarrecs'];

    carrega_CCFF_de_SAGA($db);

    $exporthorarixml = $_SESSION['upload_horaris'];
    $resultatconsulta = simplexml_load_file($exporthorarixml);

    if (!$resultatconsulta) {
        echo "Carrega Horaris fallida";
    } else {
        // Fem un recorregut de les lessons.
        // Per cada lesson generem tot
        foreach ($resultatconsulta->lessons->lesson as $lesson) {
            // Desglossem el nom de matèria
            echo "<br>>>>>>" . $lesson['id'];
            //if ( $lesson['id'] == "LS_49601") break;
            $materia = neteja_apostrofs($lesson->lesson_subject['id']);
            $array1 = explode("-", $materia);
            $elements = count($array1);
            // Assignem mòdul i UFs
            $ufs = "";
            if ($elements >= 2)
                $modul = $array1[1];

            if ($elements == 3)
                $ufs = $array1[2];

            $array2 = explode("_", $array1[0]);
            $grup = $array2[1];

            if ($modul == 'Tutoria') {
                $tutoria = true;
            } else {
                $tutoria = false;
            }

            // Si les tres primer lletres son CAR, afecgir fins que sigui CARAC
            //if (substr($grup,0,3)=='CAR') {$grup=substr($grup,0,3).'AC'.substr($grup,3,strlen($grup)-3);}

            $idGrup = extreu_id('grups', 'nom', 'idgrups', $grup, $db);

            // Una vegada tenim l'id del grup podem extreure fàcilment l'id del 
            // pla des d'equivalències
            $idPla = extreu_id('equivalencies', 'grup_gp', 'pla_saga', $grup, $db);
            $acronimPla = extreu_id('plans_estudis', 'idplans_estudis', 'Acronim_pla_estudis', $idPla, $db);

            $esLoe = esbrinaLOE_cali($acronimPla, $db);
            // Si no és una materia LOE

            echo "<br> Grup: " . $grup .
            " >> Mòdul: " . $modul .
            " >> Ufs:  " . $ufs .
            " >> id Grup: " . $idGrup .
            " >> acrònim: " . $acronimPla;




            if ($idGrup != '') {
                if ($esLoe == 0) {
                    if ($tutoria) {
                        $materia = genera_tutoria($dataInici, $dataFi, $idPla, $acronimPla, $esLoe, $db);

                        $idProfessor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $lesson->lesson_teacher['id'], $db);
                        if ($idProfessor != '') {
                            $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) VALUES ('" . $idProfessor . "','" . $idCarrec . "','" . $idGrup . "',1);";
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                    }
                    $idGrupMateria = generaGrupMateriaNoLoe($materia, $idGrup, $acronimPla, $idPla, $dataInici, $dataFi, $tutoria, $db);
                    //if ($tutoria) {echo "<br>".$materia." >> ".$idGrupMateria;}
                    echo "<br> Abans NO LOE" . $idGrupMateria;
                    if ($lesson->lesson_teacher['id'] != "") {
                        $nouGrupMateriaDesdoblat = assigna_profe($idGrupMateria, $lesson->lesson_teacher['id'], $db);
                        if ($nouGrupMateriaDesdoblat != $idGrupMateria) {
                            //echo "<br>".$idGrupMateria." >> ".$nouGrupMateriaDesdoblat;
                            $idGrupMateria = $nouGrupMateriaDesdoblat;
                        }
                    }
                    //echo "<br>Grup materia: ".$idGrupMateria;
                    creaHorari($lesson, $idGrupMateria, $idGrup, $db);
                } else {

                    if ($tutoria) {
                        $materia = genera_tutoria($dataInici, $dataFi, $idPla, $acronimPla, $esLoe, $db);
                        // Extreiem l'id de la uf
                        $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '" . $materia . "';";
//                        echo "<br>...".$sql2;
                        $result2 = $db->prepare($sql2);
                        $result2->execute();
                        $fila2 = $result2->fetch();
                        $idUf = $fila2['id_mat_uf_pla'];
                        // Crearem el grup_materia i n'extreuirem l'id
                        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                        $sql2 .= "VALUES ('" . $idGrup . "','" . $idUf . "','" . $dataInici . "','" . $dataFi . "');";
//                        echo "<br>...".$sql2;
                        $result2 = $db->prepare($sql2);
                        $result2->execute();

                        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '" . $idGrup . "' AND id_mat_uf_pla = '" . $idUf . "';";
//                        echo "<br>...".$sql2;
                        $result2 = $db->prepare($sql2);
                        $result2->execute();
                        $fila2 = $result2->fetch();

                        creaHorari($lesson, $fila2['idgrups_materies'], $idGrup, $db);

                        $idProfessor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $lesson->lesson_teacher['id'], $db);
                        if ($idProfessor != '') {
                            $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) VALUES ('" . $idProfessor . "','" . $idCarrec . "','" . $idGrup . "',1);";
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                    } else {
                        unset($unitatsFormatives);
                        $unitatsFormatives = array();
                        $modul = str_pad($modul, 3, "0", STR_PAD_LEFT);
                        //echo "<br>Mòdul: ".$modul;
                        if ($ufs != '') {
                            $unitatsFormatives = str_split($ufs);
                            for ($i = 0; $i < count($unitatsFormatives); $i++) {
                                $unitatsFormatives[$i] = str_pad($unitatsFormatives[$i], 2, "0", STR_PAD_LEFT);
                                // Repassem l'acronim
                                $abreviaturaArr = explode("(", $acronimPla);
                                $abreviaturaArr2 = explode(")", $abreviaturaArr[1]);
                                $unitatsFormatives[$i] = $abreviaturaArr2[0] . "_" . $modul . $unitatsFormatives[$i];
                                echo $unitatsFormatives[$i];
                                $unitatsFormatives[$i] = extreu_id('unitats_formatives', 'codi_uf', 'idunitats_formatives', $unitatsFormatives[$i], $db);
                            }
                        } else {

                            $unitatsFormatives = array();
                            $sql = "SELECT idmoduls FROM moduls WHERE idplans_estudis='" . $idPla . "' AND codi_modul='" . $modul . "';";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $fila = $result->fetch();
                            $idModul = $fila['idmoduls'];

                            $sql = "SELECT id_ufs FROM moduls_ufs WHERE id_moduls = '" . $idModul . "';";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $i = 0;
                            foreach ($result->fetchAll() as $fila) {
                                $unitatsFormatives[$i] = $fila['id_ufs'];
                                $i++;
                            }
                        }
                        //foreach ($unitatsFormatives as $ufs) {echo "<br>...".$ufs;}
                        for ($i = 0; $i < count($unitatsFormatives); $i++) {
                            if ($unitatsFormatives[$i] != '') {
//                                echo "<br>" . $i . " > " . $unitatsFormatives[$i] . " > " . $idGrup . " > " . $acronimPla . " > " . $idPla . " > " . $dataInici . " > " . $dataFi;
                                $idGrupMateria = generaGrupMateriaLoe(count($unitatsFormatives), $i, $unitatsFormatives[$i], $idGrup, $acronimPla, $idPla, $dataInici, $dataFi, $db);
                                echo "<br> Abans LOE..." . $idGrupMateria;
                                if ($lesson->lesson_teacher['id'] != "") {
                                    $nouGrupMateriaDesdoblat = assigna_profe($idGrupMateria, $lesson->lesson_teacher['id'], $db);
                                    if ($nouGrupMateriaDesdoblat != $idGrupMateria) {
                                        //echo "<br>".$idGrupMateria." >> ".$nouGrupMateriaDesdoblat;
                                        $idGrupMateria = $nouGrupMateriaDesdoblat;
                                    }
                                    //echo "<br>Grup materia LOE: ".$idGrupMateria;
                                }
                                //echo "<br> Després" . $idGrupMateria;
                                creaHorari($lesson, $idGrupMateria, $idGrup, $db);
                            }
                        }
                        //echo "<br>Nova lesson *********************";  
                    }
                }
            }
        }
    }
    introduir_fase('lessons', 1, $db);
    $page = "./menu.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
}

function creaHorari($classe, $idGrupMateria, $idgrup, $db) {
    foreach ($classe->times->time as $franges) {
        //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
        // Extreiem el codi de la franja/dia
        $dia = $franges->assigned_day;
        $franja = $franges->assigned_period;
        $horainici = $franges->assigned_starttime;
        $horafi = $franges->assigned_endtime;
//        if(extreu_fase('segona_carrega'))
//               {
//            $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
//            $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
//            $franja=mysql_result($result,0);
//            }

        if (($horainici != "") && ($horafi != "")) {
            $horainici = $horainici * 100;
            $horainici = arregla_hora_gpuntis($horainici);
            $horafi = $horafi * 100;
            $horafi = arregla_hora_gpuntis($horafi);

            $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $idgrup, $db);

            $sql = "SELECT A.id_dies_franges as id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
            $sql .= "A.iddies_setmana='" . $dia . "' AND B.hora_inici='" . $horainici . "' AND B.hora_fi='" . $horafi . "' ";
            $sql .= "AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='" . $id_torn . "'; ";
            $result = $db->prepare($sql);
            $result->execute();
            $fila = $result->fetch();
            $codi_dia_franja = $fila['id_dies_franges'];
            //echo "<br>".$dia." >> ".$franja." >> ".$idgrup." >> ".$codi_dia_franja;
        } else {
            //Per si hi ha torn superposats....
            $codi_dia_franja = extreu_codi_franja($dia, $franja, $idgrup, $db);
            //echo "<br>".$dia." >> ".$franja." >> ".$idgrup;
            //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
        }
        // Extreiem l'id de l'espai
        $sql = "SELECT idespais_centre FROM espais_centre WHERE codi_espai='" . $franges->assigned_room['id'] . "'; ";
        $result = $db->prepare($sql);
        $result->execute();
        $fila = $result->fetch();
        $codi_espai = $fila['idespais_centre'];
        if ($codi_espai == "") {
            $sql = "SELECT idespais_centre FROM espais_centre WHERE descripcio='Sense determinar'; ";
            $result = $db->prepare($sql);
            $result->execute();
            $fila = $result->fetch();
            $codi_espai = $fila['idespais_centre'];
        }
        // Inserim la unitat classe
        $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('" . $codi_dia_franja . "','" . $codi_espai . "','" . $idGrupMateria . "')";
        //echo "<br>>>>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
    }
}

function assigna_profe($idGrupMateria, $codiProfe, $db) {
    $idProfessor = extreu_id('equivalencies', 'codi_prof_gp', 'prof_ga', $codiProfe, $db);

    $id_materia = extreu_id('grups_materies', 'idgrups_materies', 'id_mat_uf_pla', $idGrupMateria, $db);

    $nouIdGrupMateria = $idGrupMateria;
    // Si ja està posat el grupmateria i el professor, no s'ha de fer res. Encara que semble mentida pot haver
    // lessons amb tota la informació de grup/materia/classe  iguals
    $sql = "SELECT COUNT(idprof_grup_materia) as compta FROM prof_agrupament WHERE idagrups_materies='" . $idGrupMateria . "' AND idprofessors = '" . $idProfessor . "';";
    echo "<br>" . $sql;
    $result = $db->prepare($sql);
    $result->execute();
    $files = $result->fetch();
    $fila = $files['compta'];
    if ($fila == 0) {
        $sql2 = "SELECT COUNT(idprof_grup_materia) as compta FROM prof_agrupament WHERE idagrups_materies='" . $idGrupMateria . "';";
        echo "<br>" . $sql2;
        $result2 = $db->prepare($sql2);
        $result2->execute();
        $fila2 = $result2->fetch();
        //Si entra aqui és que hi ha una o més assignacions a altres professors d'aquest grup-materia
        if ($fila2['compta'] != 0) {
            $idGrupMateriaOriginal = $idGrupMateria;
            $desdoblem = comprova_desdoblament($idProfessor, $idGrupMateria, $db);
            echo "<br>Desdoblem ..." . $desdoblem;
            if ($desdoblem == 1) {
                //echo "<br>Id grup materia abans: ".$idGrupMateriaOriginal;
                $idGrupMateria = treu_darrer_desdoblament($idGrupMateriaOriginal, $db);
                //echo "<br>Id grup materia després: ".$idGrupMateria;
                $idGrupMateria = creadesdoblament($idGrupMateria, $id_materia, $idProfessor, $db);
                //echo "<br>Id grup materia desprésssss: ".$idGrupMateria;
                $nouIdGrupMateria = $idGrupMateria;
            } else {
                $nouIdGrupMateria = $desdoblem;
            }
        }

        if ($idProfessor != '') {
            $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $idProfessor . "','" . $nouIdGrupMateria . "');";
            $result = $db->prepare($sql);
            $result->execute();
        }
    }
    return $nouIdGrupMateria;
}

function creaMateriaGenerica($materia, $acronimPla) {
    $arrayMateria = explode("-", $materia);
    $materiaGenerica = $acronimPla . "_" . $arrayMateria[1];
    return $materiaGenerica;
}

function generaGrupMateriaLoe($nombredUfs, $i, $idUf, $idGrup, $acronimPla, $idPla, $dataInici, $dataFi, $db) {
    // Generem el grup materia i el retornem: primer comprovem que no existeixi ja

    $dataInici_tmp = date_create($dataInici);
    date_add($dataInici_tmp, date_interval_create_from_date_string("240 days"));
    $dataInici_tmp = date_format($dataInici_tmp, "Y-m-d");

    $dataFi_tmp = date_create($dataInici);
    date_add($dataFi_tmp, date_interval_create_from_date_string("239 days"));
    $dataFi_tmp = date_format($dataFi_tmp, "Y-m-d");

//    echo "<br>",$dataInici." >> ".$dataFi;
//    echo "<br>",$dataInici_tmp." >> ".$dataFi_tmp;

    $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '" . $idGrup . "' AND id_mat_uf_pla = '" . $idUf . "';";
    $result2 = $db->prepare($sql2);
    $result2->execute();

    if ($result2->rowCount() == 0) {
        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql2 .= "VALUES ('" . $idGrup . "','" . $idUf . "',";
        if ($nombredUfs == 1) {
            $sql2 .= "'" . $dataInici . "','" . $dataFi . "')";
        } else {
            if ($i != 0) {
                $sql2 .= "'" . $dataInici_tmp . "','" . $dataFi . "')";
            } else {
                $sql2 .= "'" . $dataInici . "','" . $dataFi_tmp . "')";
            }
        }
        //echo "<br>...".$sql2;
        $result2 = $db->prepare($sql2);
        $result2->execute();

        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '" . $idGrup . "' AND id_mat_uf_pla = '" . $idUf . "';";
        $result2 = $db->prepare($sql2);
        $result2->execute();
    }
    $fila2 = $result2->fetch();
    return $fila2['idgrups_materies'];
}

function generaGrupMateriaNoLoe($materia, $idGrup, $acronimPla, $idPla, $dataInici, $dataFi, $tutoria, $db) {
    //echo "<br>".$idGrup." >> ".$materia." >> ".$idPla;
    // Una materia genèrica és el seu nom sense el grup al que pertany
    if (!$tutoria) {
        $materiaGenerica = creaMateriaGenerica($materia, $acronimPla);
    } else {
        $materiaGenerica = $materia;
    }
//    echo "<br>".$materiaGenerica;
    // Comporvem si la materia ja existeix
    $sql = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '" . $materiaGenerica . "'";
//    echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $files = $result->rowCount();
//    echo "<br>".$files;    
    if ($files == 1) {
        $idMateria = $fila['id_mat_uf_pla'];
    } else {
        //Hem de crear la materia a les dues taules i exterure el seu id
        $sql2 = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
        $sql2 .= "VALUES ('" . $idPla . "','" . $materiaGenerica . "','S')";
        $result2 = $db->prepare($sql2);
        $result2->execute();

        $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '" . $materiaGenerica . "';";
        $result2 = $db->prepare($sql2);
        $result2->execute();
        $fila2 = $result2->fetch();
        $idMateria = $fila2[0];

        $sql2 = "INSERT INTO materia(idmateria,codi_materia,nom_materia) ";
        $sql2 .= "VALUES ('" . $idMateria . "','" . $materiaGenerica . "','" . $materiaGenerica . "')";
        $result2 = $db->prepare($sql2);
        $result2->execute();
    }
    // Generem el grup materia i el retornem: primer comprovem que no existeixi ja

    $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '" . $idGrup . "' AND id_mat_uf_pla = '" . $idMateria . "';";
    $result2 = $db->prepare($sql2);
    $result2->execute();
    $fila2 = $result2->fetch();

    if ($result2->rowCount() == 0) {
        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql2 .= "VALUES ('" . $idGrup . "','" . $idMateria . "','" . $dataInici . "','" . $dataFi . "')";
        $result2 = $db->prepare($sql2);
        $result2->execute();

        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '" . $idGrup . "' AND id_mat_uf_pla = '" . $idMateria . "';";
        $result2 = $db->prepare($sql2);
        $result2->execute();
        $fila2 = $result2->fetch();
    }

    return $fila2['idgrups_materies'];
}

function esbrinaLOE_cali($idPla, $db) {
    $sql = "SELECT COUNT(*) AS compta FROM plans_estudis WHERE Acronim_pla_estudis LIKE  '%" . $idPla . "%'  AND Nom_plan_estudis LIKE  '%LOE%';";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    if ($fila['compta'] == 0) {
        return 0;
    } else {
        return 1;
    }
}

?>