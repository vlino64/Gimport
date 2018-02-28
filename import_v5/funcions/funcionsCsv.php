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

function carregaFrangesDies($db) {

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S' ;";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $periode = $fila['idperiodes_escolars'];

    $sql = "SELECT idtorn FROM torn WHERE nom_torn = 'TORN GLOBAL' ;";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $torn = $fila['idtorn'];

    $sql = "INSERT INTO `franges_horaries` (`idfranges_horaries`, `idtorn`, `activada`, `esbarjo`, `hora_inici`, `hora_fi`) VALUES";
    $sql .= "(1, " . $torn . ", 'S', ' ', '08:15:00', '09:15:00'),";
    $sql .= "(2, " . $torn . ", 'S', ' ', '09:15:00', '10:15:00'),";
    $sql .= "(3, " . $torn . ", 'S', ' ', '10:15:00', '11:15:00'),";
    $sql .= "(4, " . $torn . ", 'S', ' ', '11:45:00', '12:45:00'),";
    $sql .= "(5, " . $torn . ", 'S', ' ', '12:45:00', '13:45:00'),";
    $sql .= "(6, " . $torn . ", 'S', ' ', '13:45:00', '14:45:00'),";
    $sql .= "(7, " . $torn . ", 'S', ' ', '15:30:00', '16:25:00'),";
    $sql .= "(8, " . $torn . ", 'S', ' ', '16:25:00', '17:20:00'),";
    $sql .= "(9, " . $torn . ", 'S', ' ', '17:20:00', '18:15:00'),";
    $sql .= "(10, " . $torn . ", 'S', ' ', '18:45:00', '19:40:00'),";
    $sql .= "(11, " . $torn . ", 'S', ' ', '19:40:00', '20:35:00'),";
    $sql .= "(12, " . $torn . ", 'S', ' ', '20:35:00', '21:30:00');";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "SELECT iddies_setmana FROM dies_setmana WHERE iddies_setmana < 6 ;";
    $result = $db->prepare($sql);
    $result->execute();
    foreach ($result->fetchAll() as $fila) {
        $sql2 = "SELECT idfranges_horaries FROM franges_horaries";
        $result2 = $db->prepare($sql2);
        $result2->execute();
        foreach ($result2->fetchAll() as $fila2) {
            $sql3 = "INSERT INTO dies_franges(iddies_setmana,idfranges_horaries,idperiode_escolar) ";
            $sql3 .="VALUES(" . $fila['iddies_setmana'] . "," . $fila2['idfranges_horaries'] . "," . $periode . ")";
            $result3 = $db->prepare($sql3);
            $result3->execute();
        }
    }
}

function extreuGrupsCsv2() {
    // Extreu els grups de csv d'alumnes
    $alumnat = extreuAlumnatCsv();
    $grups = array();
    $k = 0;
    for ($i = 1; $i < count($alumnat); $i++) {
        for ($j = 0; $j < 3; $j++) {
            $grupTmp = $alumnat[$i][$j];
            $present = false;
            foreach ($grups as $grup) {
                if (!strcmp($grup, $grupTmp)) {
                    $present = true;
                    break;
                }
            }
            if (!$present) {
                $grups[$k] = $grupTmp;
                $k++;
            }
        }
    }

    return $grups;
}

function extreuGrupsCsv() {
    // Extraccio de grups del csv de ASC
    $csvFile = $_SESSION['upload_horaris'];

    $data = array();
    $grups = array();
    $data = netejaCsv($csvFile);
    $i = 0;

    foreach ($data as $fila) {
        $array_fila = explode(";", $fila);
        $grupFila = $array_fila[2];
        $trobat = false;
        foreach ($grups as $grup) {
            //echo "<br>>>>>".$arr_prof;
            if (!strcmp($grup, $grupFila)) {
                $trobat = true;
            }
        }
        if (!$trobat) {
            $grups[$i] = $grupFila;
            $i++;
        }
    }
    sort($grups);
    return $grups;
}

function __extreuDia($dia) {
    switch ($dia) {
        case "A":
            $dia = 1;
            break;
        case "B":
            $dia = 2;
            break;
        case "C":
            $dia = 3;
            break;
        case "D":
            $dia = 4;
            break;
        case "E":
            $dia = 5;
            break;
        // Ens hem trobat algun cas que no entra en cap i per tant donava un error
        default:
            $dia = 1;
    }
    return $dia;
}

function __creaSessionsEso($sessions, $idgrup_materia, $codi_noroom, $periode) {
    require_once('../../bbdd/connect.php');

    $unitatsclasse = explode(" ", $sessions);

    foreach ($unitatsclasse as $uclasse) {
        $franja = substr($uclasse, 0, strpos($uclasse, "(")) + 1;
        $dies = substr($uclasse, (strpos($uclasse, "(") + 1), ((strpos($uclasse, ")")) - (strpos($uclasse, "(") + 1)));
        $arrayDies = explode(",", $dies);
        foreach ($arrayDies as $dia) {
            $dia = extreuDia($dia);
            if (($dia != "") && ($franja != "") && ($periode != "")) {
                //echo "<br>".$franja." >> ".$dia;
                $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $franja . "'";
                $sql.=" AND idperiode_escolar='" . $periode . "' ;";
                //echo "<br>".$sql;
                $result = mysql_query($sql);
                if (!$result) {
                    die(SELECT_DIES . mysql_error());
                }
                $fila = mysql_fetch_row($result);
                $diaFranja = $fila[0];

                $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                $sql.="(" . $diaFranja . "," . $codi_noroom . "," . $idgrup_materia . ")";
                //echo "<br>".$sql;
                $result = mysql_query($sql);
                if (!$result) {
                    die(INSERT_UC_ESO . mysql_error());
                }
            }
        }
        //echo "<br>=====================";
    }
}

function _creaSessionsCCFF($sessions, $arrayUfs, $codi_noroom, $periode) {
    require_once('../../bbdd/connect.php');

    $unitatsclasse = explode(" ", $sessions);

    foreach ($unitatsclasse as $uclasse) {
        $franja = substr($uclasse, 0, strpos($uclasse, "(")) + 1;
        $dies = substr($uclasse, (strpos($uclasse, "(") + 1), ((strpos($uclasse, ")")) - (strpos($uclasse, "(") + 1)));
        $arrayDies = explode(",", $dies);
        foreach ($arrayDies as $dia) {

            $dia = extreuDia($dia);
            //echo "<br>".$franja." >> ".$dia;
            if (($dia != "") && ($franja != "") && ($periode != "")) {
                $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $franja . "'";
                $sql.=" AND idperiode_escolar='" . $periode . "' ;";
                //echo "<br>".$sql;
                $result = mysql_query($sql);
                if (!$result) {
                    die(SELECT_DIES . mysql_error());
                }
                $fila = mysql_fetch_row($result);
                $diaFranja = $fila[0];
                for ($i = 0; $i < count($arrayUfs); $i++) {
                    $sql = "INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                    $sql.="(" . $diaFranja . "," . $codi_noroom . "," . $arrayUfs[$i][0] . ")";
                    //echo "<br>".$sql;
                    $result = mysql_query($sql);
                    if (!$result) {
                        die(INSERT_UC_FP . mysql_error());
                    }
                }
            }
        }

        //echo "<br>=====================";
    }
}

function _extreuGrupsCsvtmp() {
    $csvFile = $_SESSION['upload_horaris'];

    $data = array();
    $grups = array();
    $data = netejaCsv($csvFile);
    $i = 0;

    foreach ($data as $fila) {
        $array_fila = explode(";", $fila);
        $grupFila = $array_fila[2];
        $array_grup = explode(",", $grupFila);
        foreach ($array_grup as $arr_grup) {
//            echo "<br>>>>>".$arr_grup;            
            $trobat = false;
            foreach ($grups as $grup) {
                //echo "<br>>>>>".$arr_prof;
                if (!strcmp($grup, $arr_grup)) {
                    $trobat = true;
                }
            }
            if (!$trobat) {
                $grups[$i] = $arr_grup;
                $i++;
            }
        }
    }
    sort($grups);
    return $grups;
}

function netejaCsv($csvFile) {
    $data2 = array();
    $data = array();
    $data = file($csvFile);
    $linies = count($data);
    $j = 0;
    for ($i = 0; $i < $linies; $i++) {
        if (($i > 0) && (strlen($data[$i]) > 2)) { // Poso 2 perquè el csv posa algun caràcter especial que no es veu
            $data2[$j] = $data[$i];
            $data2[$j] = substr($data2[$j], 1);
            $data2[$j] = str_replace("\",\"", ";", $data2[$j]);
            //$data2[$j] = neteja_apostrofs($data2[$j]);
            //echo "<br>>>".$data2[$j];
            $j++;
        }
    }
    return $data2;
}

function _gestionaProfessorESO($profFila, $idgrup_materia, $es_nou_grup_materia) {
    $array_prof = explode(",", $profFila);
    foreach ($array_prof as $arr_prof) {
        $idProfessor = extreu_id("equivalencies", "nom_prof_gp", "prof_ga", $arr_prof);
        if ($idProfessor != "") {
            if ($es_nou_grup_materia) {
                $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $idProfessor . "','" . $idgrup_materia . "');";
                //echo "<br>>>>>>>".$sql;
                $result = mysql_query($sql);
                if (!$result) {
                    die(_INSERINT_PROF_GRUP_MATERIA . mysql_error());
                }
            } else {
                // Hem de comprovar que la relació no estiguija establerta
                $sql = "SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '" . $idProfessor . "' AND idagrups_materies ='" . $idgrup_materia . "';";
                //echo "<br>>>>>>>".$sql;
                $result = mysql_query($sql);
                if (!$result) {
                    die(_SELECT_PROF_GRUP_MATERIA . mysql_error());
                }
                if (mysql_num_rows($result) == 0) {
                    $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $idProfessor . "','" . $idgrup_materia . "');";
                    //echo "<br>>>>>>>".$sql;
                    $result = mysql_query($sql);
                    if (!$result) {
                        die(_INSERINT_PROF_GRUP_MATERIA(2) . mysql_error());
                    }
                }
            }
        }
    }
}

function _gestionaProfessorCCFF($profFila, $arrayUfs) {
    $array_prof = explode(",", $profFila);
    foreach ($array_prof as $arr_prof) {
        $idProfessor = extreu_id("equivalencies", "nom_prof_gp", "prof_ga", $arr_prof);
        if ($idProfessor != "") {
            for ($i = 0; $i < count($arrayUfs); $i++) {
                if ($arrayUfs[$i][1] == 1) {
                    $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $idProfessor . "','" . $arrayUfs[$i][0] . "');";
                    //echo "<br> Inserint".$sql;
                    $result = mysql_query($sql);
                    if (!$result) {
                        die(_INSERINT_PROF_GRUP_MATERIA . mysql_error());
                    }
                } else {
                    // Hem de comprovar que la relació no estiguija establerta
                    $sql = "SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '" . $idProfessor . "' AND idagrups_materies ='" . $arrayUfs[$i][0] . "';";
                    //echo "<br>>>>>>>".$sql;
                    $result = mysql_query($sql);
                    if (!$result) {
                        die(_SELECT_PROF_GRUP_MATERIA . mysql_error());
                    }
                    if (mysql_num_rows($result) == 0) {
                        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('" . $idProfessor . "','" . $arrayUfs[$i][0] . "');";
                        //echo "<br>>>>>>>".$sql;
                        $result = mysql_query($sql);
                        if (!$result) {
                            die(_INSERINT_PROF_GRUP_MATERIA(2) . mysql_error());
                        }
                    }
                }
            }
        }
    }
}

function extreuProfessoratCsv() {
    $csvFile = $_SESSION['upload_horaris'];
    $data = array();
    $professorat = array();
    $data = netejaCsv($csvFile);
    $i = 0;
    foreach ($data as $fila) {
        //echo "<br>>".$fila;
        $array_fila = explode(";", $fila);
        $profFila = $array_fila[3];

        $array_prof = explode(",", $profFila);
        foreach ($array_prof as $arr_prof) {
            $trobat = false;
            foreach ($professorat as $prof) {
                //echo "<br>>>>>".$arr_prof;
                if (!strcmp($prof, $arr_prof)) {
                    $trobat = true;
                }
            }
            if (!$trobat) {
//                echo "<br>".$fila;
//                echo "<br>".$arr_prof;
                $professorat[$i] = $arr_prof;
                $i++;
            }
        }
    }
    sort($professorat);
    return $professorat;
}

function emparella_grups_actualitzacio_csv() {
    require_once('../../pdo/bbdd/connect.php');

    $arr_grups_csv = extreuGrupsCsv2();

    print("<form method=\"post\" action=\"./recull_emparellaments_grups_csv.php\" enctype=\"multipart/form-data\" id=\"profform\">");

    print("<table align=\"center\" width=\"60%\">");
    print("<tr><td align=\"center\" colspan=\"3\"><h3>INSTRUCCIONS</h3></td></tr>");
    print("<tr><td align=\"center\" colspan=\"3\">Fes l'emparellament dels grups.</td></tr> ");
    print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Grup Gassist </td><td></td><td>Grups csv</td></tr>");
    $pos = 1;
    //echo "<br>".$sql;
    $sql = "SELECT idgrups,nom FROM grups WHERE 1 ORDER BY nom; ";
    $result = $db->prepare($sql);
    $result->execute();
    //if (!$result) {die(_ERR_SELECT_DATA_TYPE. mysql_error());} 
    foreach ($result->fetchAll() as $fila) {
        print("<tr ");
        if ((($pos / 5) % 2) == "0") {
            print("bgcolor=\"#3f3c3c\"");
        }
        print("><td><input type=\"text\" name=\"nom_grup_" . $pos . "\" value=\"" . $fila['nom'] . "\" SIZE=\"50\" READONLY></td>");
        print("<td><input type=\"text\" name=\"id_grup_" . $pos . "\" value=\"" . $fila['idgrups'] . "\" SIZE=\"6\" HIDDEN></td>");

        print("<td><select name=\"id_grup_CSV_" . $pos . "\" ");
        print(">");
        print("<option value=\"0\">Cap equivalència</option>");
        foreach ($arr_grups_csv as $grupscsv) {
            print("<option value=\"" . $grupscsv . "\">" . $grupscsv . "</option>");
        }
        print("</select></td>");

        print("</tr> ");
        $pos++;
    }
    $pos--;
    print("<tr><td align=\"center\" colspan=\"3\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
    print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
    print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
    print("</table>");
    print("</form>");
}

function comprova_matricula_grup($idAlumne, $id_grup, $db) {

    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = " . $id_grup . ";";
    $result = $db->prepare($sql);
    $result->execute();
    $present = 0;
    foreach ($result->fetchAll() as $fila) {
        if ($present == 0) {
            $sql2 = "SELECT idalumnes_grup_materia AS compta FROM alumnes_grup_materia WHERE idalumnes = " . $idAlumne . " AND ";
            $sql2.= "idgrups_materies = " . $fila['idgrups_materies'] . ";";
            //echo "<br>".$sql2;
            $result2 = $db->prepare($sql2);
            $result2->execute();
            $present = $result2->rowCount();
        }
    }
    if ($present == 0) {
        alta_grup_actualitzacio($idAlumne, $id_grup, $db);
    }
}

function actualitzar_alumnat_csv($relacioGrups, $db) {
    $camps = array();
    $camps = recuperacampdedades($camps, $db);

    // Preparem el fitxer

    $_SESSION['alumnat.csv'] = '../uploads/alumnat.csv';
    $myFile = "../uploads/alumnat.csv";
    $today = date("d-m-Y");
    $time = date("H-i-s");
    $myNewFile = $myFile . "_" . $today . "_" . $time;
    $fh = fopen($myFile, 'w') or die("can't open file");
    $stringData = "nom_i_cognoms,usuari,login,password\n";
    fwrite($fh, $stringData);

    //Desactivem tots els alumnes
    $sql = "ALTER TABLE `contacte_families` CHANGE `Valor` `Valor` VARCHAR(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "UPDATE `alumnes` SET activat = 'N';";
    $result = $db->prepare($sql);
    $result->execute();

    $alumnat = extreuAlumnatCsv();
    for ($l = 1; $l < count($alumnat); $l++) {
        $grup1 = $alumnat[$l][0];
        $grup2 = $alumnat[$l][1];
        $grup3 = $alumnat[$l][2];
        $idAlumne = $alumnat[$l][3];
        $cognom1 = $alumnat[$l][4];
        $cognom2 = $alumnat[$l][5];
        $nom = $alumnat[$l][6];
        $dataNaixement = $alumnat[$l][7];
        $adressa = $alumnat[$l][8];
        $localitat = $alumnat[$l][9];
        $tutor1Nom = $alumnat[$l][10];
        $tutor1Cognom1 = $alumnat[$l][11];
        $tutor1Cognom2 = $alumnat[$l][12];
        $tutor1mobil = $alumnat[$l][13];
        $tutor1email = $alumnat[$l][14];
        $tutor2Nom = $alumnat[$l][15];
        $tutor2Cognom1 = $alumnat[$l][16];
        $tutor2Cognom2 = $alumnat[$l][17];
        $tutor2mobil = $alumnat[$l][18];
        $tutor2email = $alumnat[$l][19];
        $altres = $alumnat[$l][20];

        $user = $cognom1 . " " . $cognom2 . ", " . $nom;

        $nom_complet = $user;
        $pass = "";
        if ($idAlumne != "") {
            if (alumne_ja_existeix($idAlumne, $db)) {
                //Hem d'activar l'alumne
                $sql = "UPDATE `alumnes` SET activat = 'S' WHERE codi_alumnes_saga = '" . $idAlumne . "' ;";
                //echo $sql."<br>";
                $result = $db->prepare($sql);
                $result->execute();

                //comprovem si està matriuclat als grups que indica el fitxer
                $id = extreu_id('alumnes', 'codi_alumnes_saga', 'idalumnes', $idAlumne, $db);
                if ($grup1 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup1);
                    comprova_matricula_grup($id, $id_grup, $db);
                }
                if ($grup2 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup2);
                    comprova_matricula_grup($id, $id_grup, $db);
                }
                if ($grup3 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup3);
                    comprova_matricula_grup($id, $id_grup, $db);
                }
            } else {
                // Hem de crear l'alumne

                genera($user, $pass, $nom, $cognom1, $cognom2, true, $db);
                // En aquest cas no utiltzem a generació del password. Agafem com password el nom d'usuari
                // i que actualitzen la password qun facin login
                $pass = md5($user);
                //echo $user.">>>".$pass;

                $stringData = $nom . "," . $cognom1 . " " . $cognom2 . "," . $user . "," . $user . "\n";
                fwrite($fh, $stringData);

                $sql = "INSERT INTO `alumnes`(codi_alumnes_saga,activat) ";
                $sql.="VALUES ('" . $idAlumne . "','S');";
                //echo $sql."<br>";

                $result = $db->prepare($sql);
                $result->execute();
                //echo "S'ha insertat ".$nom;echo "<br>";

                $id = extreu_id('alumnes', 'codi_alumnes_saga', 'idalumnes', $idAlumne, $db);
                //echo "<br>".$id;

                $sql = "INSERT INTO `contacte_alumne`(id_alumne,id_tipus_contacte,Valor) ";
                $sql.="VALUES ('" . $id . "','" . $camps['nom_complet'] . "','" . $nom_complet . "'),";
                $sql.="('" . $id . "','" . $camps['login'] . "','" . $user . "'),";
                $sql.="('" . $id . "','" . $camps['iden_ref'] . "','" . $idAlumne . "'),";
                $sql.="('" . $id . "','" . $camps['nom_alumne'] . "','" . $nom . "'),";
                $sql.="('" . $id . "','" . $camps['cognom1_alumne'] . "','" . $cognom1 . "'),";
                $sql.="('" . $id . "','" . $camps['cognom2_alumne'] . "','" . $cognom2 . "'),";
                $sql.="('" . $id . "','" . $camps['data_naixement'] . "','" . $dataNaixement . "'),";
                $md5pass = md5($user);
                $sql.="('" . $id . "','" . $camps['contrasenya'] . "','" . $md5pass . "');";
                //echo $sql."<br>";
                $result = $db->prepare($sql);
                $result->execute();


                // Generem famílies
                // Crea una família sense dades i retorna el seu id
                $id_families = crea_families($db);

                // Segon si té o no germans es modifica la sql	
                $sql = "INSERT INTO `alumnes_families`(idalumnes,idfamilies) ";
                $sql.="VALUES ";
                $sql.="('" . $id . "','" . $id_families . "'); ";
                //            echo $sql."<br>";
                $result = $db->prepare($sql);
                $result->execute();

                //Inserim les dades de contacte de la família si no té german que ja s'hagin donat s'alta

                $nom_complet_pare = $tutor1Nom . " " . $tutor1Cognom1 . " " . $tutor1Cognom2;
                $nom_complet_mare = $tutor2Nom . " " . $tutor2Cognom1 . " " . $tutor2Cognom2;

                $sql = "INSERT INTO `contacte_families`(id_families,id_tipus_contacte,Valor) ";
                $sql.="VALUES ";
                $sql.="('" . $id_families . "','" . $camps["nom_pare"] . "','" . $tutor1Nom . "') ";
                $sql.=",('" . $id_families . "','" . $camps["cognom1_pare"] . "','" . $tutor1Cognom1 . "') ";
                $sql.=",('" . $id_families . "','" . $camps["cognom2_pare"] . "','" . $tutor1Cognom2 . "') ";
                $sql.=",('" . $id_families . "','" . $camps["nom_complet"] . "','" . $nom_complet_pare . "') ";
                $sql.=",('" . $id_families . "','" . $camps["nom_complet"] . "','" . $nom_complet_pare . "') ";
                $sql.=",('" . $id_families . "','" . $camps["mobil_sms"] . "','" . $tutor1mobil . "') ";
                $sql.=",('" . $id_families . "','" . $camps["email1"] . "','" . $tutor1email . "') ";
                if ($tutor2Nom != "") {
                    $sql.=",('" . $id_families . "','" . $camps["nom_mare"] . "','" . $tutor2Nom . "') ";
                    $sql.=",('" . $id_families . "','" . $camps["cognom1_mare"] . "','" . $tutor2Cognom1 . "') ";
                    $sql.=",('" . $id_families . "','" . $camps["cognom2_mare"] . "','" . $tutor2Cognom2 . "') ";
                    $sql.=",('" . $id_families . "','" . $camps["nom_complet"] . "','" . $nom_complet_mare . "') ";
                    $sql.=",('" . $id_families . "','" . $camps["mobil_sms2"] . "','" . $tutor2mobil . "') ";
                    $sql.=",('" . $id_families . "','" . $camps["email1"] . "','" . $tutor2email . "') ";
                }
                $sql.=",('" . $id_families . "','" . $camps["adreca"] . "','" . $adressa . "') ";
                $sql.=",('" . $id_families . "','" . $camps["nom_municipi"] . "','" . $localitat . "') ";
                $sql.=",('" . $id_families . "','" . $camps["telefon"] . "','" . $altres . "') ";
                //echo $sql."<br>";
                $result = $db->prepare($sql);
                $result->execute();

                //Assignem els grups    
                // I ara el matriculem al grup indicat i totes les seves matèries

                if ($grup1 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup1);
//                    echo"<br>".$id_grup." >> ".$grup1." >> ".$id;
                    if ($id_grup != 0) {
                        alta_grup_actualitzacio($id, $id_grup, $db);
                    }
                }
                if ($grup2 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup2);
                    if ($id_grup != 0) {
                        alta_grup_actualitzacio($id, $id_grup, $db);
                    }
                }
                if ($grup3 != "") {
                    $id_grup = extreuGrup($relacioGrups, $grup3);
                    if ($id_grup != 0) {
                        alta_grup_actualitzacio($id, $id_grup, $db);
                    }
                }
            }
        }
    }

    fclose($fh);
    if (!copy($myFile, $myNewFile)) {
        echo "failed to copy";
    }

    die("<script>location.href = './index.php'</script>");
}

function extreuGrup($relacioGrups, $grup) {
    foreach ($relacioGrups as $grups) {
        //Si el grup coincideix
        if ($grups[2] == $grup) {
            return $grups[0];
        }
    }
    return 0;
}

function alta_grup_actualitzacio($id_alumne, $id_grup, $db) {
    $sql = "SELECT idgrups_materies FROM grups_materies WHERE id_grups='" . $id_grup . "';";
//    echo "<br>>>>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    foreach ($result->fetchAll() as $fila) {
        $sql2 = "INSERT INTO alumnes_grup_materia(idalumnes,idgrups_materies) VALUES ('" . $id_alumne . "','$fila[0]');";
//        echo "<br>".$sql2;
        $result2 = $db->prepare($sql2);
        $result2->execute();
    }
}

//function _altaAlumne()
//    {
//    $camps=array();
//   $camps =recuperacampdedades($camps,$db);
//    //Desactivem tots els alumnes
//    $sql="UPDATE `alumnes` SET activat = 'N';";
//    //echo $sql."<br>";
//    $result=mysql_query($sql);
//    if (!$result) {die(_ERR_INSERT_ALUM . mysql_error());}            
//    
//    // Preparem el fitxer
//    $myFile = "../uploads/alumnat.csv";
//    $fh = fopen($myFile, 'w') or die("can't open file");
//    $stringData="nom_i_cognoms,usuari,login,password\n";		
//    fwrite($fh, $stringData);
//    
//    $alumnat = extreuAlumnatCsv();
//    for( $l = 1 ; $l < count($alumnat)  ; $l++ )        
//        {
//        $idAlumne       = $alumnat[$l][3];
//        $cognom1        = $alumnat[$l][4];
//        $cognom2        = $alumnat[$l][5];
//        $nom            = $alumnat[$l][6];
//        $dataNaixement  = $alumnat[$l][7];
//        $adressa        = $alumnat[$l][8];
//        $localitat      = $alumnat[$l][9];
//        $tutor1Nom      = $alumnat[$l][10];
//        $tutor1Cognom1  = $alumnat[$l][11];
//        $tutor1Cognom2  = $alumnat[$l][12];
//        $tutor1mobil    = $alumnat[$l][13];
//        $tutor1email    = $alumnat[$l][14];                            
//        $tutor2Nom      = $alumnat[$l][15];
//        $tutor2Cognom1  = $alumnat[$l][16];
//        $tutor2Cognom2  = $alumnat[$l][17];
//        $tutor2mobil    = $alumnat[$l][18]; 
//        $tutor2email    = $alumnat[$l][19];
//        $altres         = $alumnat[$l][20];
//
//        $user=$nom." ".$cognom1." ".$cognom2;
//        $nom_complet=$user;
//        $pass="";
//        //echo "<br>".$idAlumne;
//        if ($idAlumne != "")
//            {
//            if (alumne_ja_existeix($idAlumne))
//                {
//                //Hem d'activar l'alumne
//                $sql="UPDATE `alumnes` SET activat = 'S' WHERE codi_alumnes_saga = '".$idAlumne."' ;";
//                //echo $sql."<br>";
//                $result=mysql_query($sql);
//                if (!$result) {die(_ERR_INSERT_ALUM . mysql_error());}            
//                }
//            else
//                {
//                // Hem de crear l'alumne
//        
//                genera($user,$pass,$nom,$cognom1,$cognom2);
//                // En aquest cas no utiltzem a generació del password. Agafem com password el nom d'usuari
//                // i que actualitzen la password qun facin login
//                $pass=md5($user);
//                //echo $user.">>>".$pass;
//       
//                $sql="INSERT INTO `alumnes`(codi_alumnes_saga,activat) ";
//                $sql.="VALUES ('".$idAlumne."','S');";
//                //echo $sql."<br>";
//
//                $result=mysql_query($sql);
//                if (!$result) 
//                        {
//                        die(_ERR_INSERT_ALUM . mysql_error());
//                        }
//                //echo "S'ha insertat ".$nom;echo "<br>";
//
//
//
//                $id=extreu_id(alumnes,codi_alumnes_saga,idalumnes,$idAlumne);
//                //echo "<br>".$id;
//
//                $sql="INSERT INTO `contacte_alumne`(id_alumne,id_tipus_contacte,Valor) ";
//                $sql.="VALUES ('".$id."','".$camps[nom_complet]."','".$nom_complet."'),";
//                $sql.="('".$id."','".$camps[login]."','".$user."'),";
//                $sql.="('".$id."','".$camps[iden_ref]."','".$idAlumne."'),";
//                $sql.="('".$id."','".$camps[nom_alumne]."','".$nom."'),";
//                $sql.="('".$id."','".$camps[cognom1_alumne]."','".$cognom1."'),";
//                $sql.="('".$id."','".$camps[cognom2_alumne]."','".$cognom2."'),";
//                $sql.="('".$id."','".$camps[data_naixement]."','".$dataNaixement."'),";
//                $md5pass=md5($user);
//                $sql.="('".$id."','".$camps[contrasenya]."','".$md5pass."');";
//    //            echo $sql."<br>";
//                $result=mysql_query($sql);
//                if (!$result) 
//                        {
//                        die(_ERR_INSERT_ALUM_CONTACT . mysql_error());
//                        }
//                //print("L'alumne/a d'alta: ".$nom_complet." Nom d'usuari: ".$user."<br>");
//                //Escrivim en el csv
//                $stringData=$nom.",".$cognom1." ".$cognom2.",".$user.",".$user."\n";		
//                fwrite($fh, $stringData);
//
//                // Generem famílies
//                // Crea una família sense dades i retorna el seu id
//                $id_families = crea_families();
//
//                // Segon si té o no germans es modifica la sql	
//                $sql="INSERT INTO `alumnes_families`(idalumnes,idfamilies) ";
//                $sql.="VALUES ";
//                $sql.="('".$id."','".$id_families."'); ";
//    //            echo $sql."<br>";
//                $result=mysql_query($sql);
//                if (!$result) 
//                        {
//                        die(_ERR_INSERT_FAMILY.(2). mysql_error());
//                        }		
//
//                //Inserim les dades de contacte de la família si no té german que ja s'hagin donat s'alta
//
//                $nom_complet_pare=$tutor1Nom." ".$tutor1Cognom1." ".$tutor1Cognom2;
//                $nom_complet_mare=$tutor2Nom." ".$tutor2Cognom1." ".$tutor2Cognom2;
//
//                $sql="INSERT INTO `contacte_families`(id_families,id_tipus_contacte,Valor) ";
//                $sql.="VALUES ";
//                $sql.="('".$id_families."','".$camps["nom_pare"]."','".$tutor1Nom."') ";
//                $sql.=",('".$id_families."','".$camps["cognom1_pare"]."','".$tutor1Cognom1."') ";
//                $sql.=",('".$id_families."','".$camps["cognom2_pare"]."','".$tutor1Cognom2."') ";
//                $sql.=",('".$id_families."','".$camps["nom_complet"]."','".$nom_complet_pare."') ";
//                $sql.=",('".$id_families."','".$camps["nom_complet"]."','".$nom_complet_pare."') ";
//                $sql.=",('".$id_families."','".$camps["mobil_sms"]."','".$tutor1mobil."') ";
//                $sql.=",('".$id_families."','".$camps["email1"]."','".$tutor1email."') ";
//                if ($tutor2Nom != "")
//                        {
//                        $sql.=",('".$id_families."','".$camps["nom_mare"]."','".$tutor2Nom."') ";
//                        $sql.=",('".$id_families."','".$camps["cognom1_mare"]."','".$tutor2Cognom1."') ";
//                        $sql.=",('".$id_families."','".$camps["cognom2_mare"]."','".$tutor2Cognom2."') ";
//                        $sql.=",('".$id_families."','".$camps["nom_complet"]."','".$nom_complet_mare."') ";
//                        $sql.=",('".$id_families."','".$camps["mobil_sms2"]."','".$tutor2mobil."') ";
//                        $sql.=",('".$id_families."','".$camps["email1"]."','".$tutor2email."') ";
//
//                        }
//                $sql.=",('".$id_families."','".$camps["adreca"]."','".$adressa."') ";
//                $sql.=",('".$id_families."','".$camps["nom_municipi"]."','".$localitat."') ";
//                $sql.=",('".$id_families."','".$camps["telefon"]."','".$altres."') ";
//                //echo $sql."<br>";
//                $result=mysql_query($sql);
//                if (!$result) 
//                        {
//                        die(_ERROR_INSERT_FAMILY.(3). mysql_error());
//                        }
//                }
//            }
//        
//        }
//        fclose($fh);
//        introduir_fase('families',1);
//        introduir_fase('alumnat',1);
//    
//        die("<script>location.href = './menu.php'</script>");
//    }        


function extreuAlumnatCsv() {
    $csvFile = $_SESSION['upload_alumnes'];

    $data = array();
    $alumnat = array();
    //$data = netejaCsv($csvFile);
    $data = file($csvFile);
    $i = 0;
    foreach ($data as $fila) {
//      echo "<br>>>".$i." >> ".$fila;
        $fila = neteja_apostrofs($fila);
        if (strlen($fila) > 5) {
            $alumnat[$i] = explode(";", $fila);
            $i++;
        }
    }

    return $alumnat;
}

function extreuMateriesCsv() {
    $csvFile = $_SESSION['upload_horaris'];

    $data = array();
    $materies = array();
    $data = netejaCsv($csvFile);
    $i = 0;
    foreach ($data as $fila) {
        //echo "<br>>".$fila;
        $array_fila = explode(";", $fila);
        $codiMateria = $array_fila[0];
        $materia = $array_fila[1];
        $trobat = false;
        for ($fila = 0; $fila <= count($materies) - 1; $fila++) {
            if ((!strcmp($codiMateria, $materies[$fila][0])) && (!strcmp($materia, $materies[$fila][1]))) {
                $trobat = true;
                break;
            }
        }
        if (!$trobat) {
            $materies[$i][0] = $codiMateria;
            $materies[$i][1] = $materia;
            //echo "<br>nou: ".$materies[$i][0]." /// ".$materies[$i][1]." >>> ".$i;
            $i++;
        }
    }
    sort($materies);
    return $materies;
}

?>
