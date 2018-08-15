<?php

// **********************************************************************
// **********************************************************************
// Realització d'informe  de qui passa llista o no0
// **********************************************************************
// **********************************************************************


require_once(dirname(dirname(__FILE__)) . '/bbdd/connect.php');

$db->exec("set names utf8");

echo "No hauries d'accedir a aquesta pàgina ...";

// Comprovem si s'ha d'executar
$sql2 = "SELECT cron_passa_llista FROM config; ";
$result2 = $db->query($sql2);
if (!$result2) {
    die(_SELECT_MAIL_PROF . mysqli_error($conn));
}
$fila2 = $result2->fetchAll();
$executarse = $fila2[0]['cron_passa_llista'];
//echo "<br>".$executarse;

if ($executarse == 1) {
// NOMÉS ACCEPTARÀ PETICIONS DES DE LOCALHOST.
// Si es connecta des de localhost la variable ve buida
// Si es connecta des d'una altra màquina, la variable porta contingut
    if ($_SERVER['REMOTE_ADDR'] != "") {
        echo "No hauries d'accedir a aquesta p&agrave;gina .....";
    } else {
        $iniciSetmana = strtotime('previous Monday');
        $dataInici = date('Y-m-d', $iniciSetmana);
        $fiSetmana = strtotime("+ 4 day", $iniciSetmana);
        $dataFi = date('Y-m-d', $fiSetmana);


        $sql = "SELECT P.idprofessors, CP.Valor ";
        $sql .= "FROM professors P, contacte_professor CP ";
        $sql .= "WHERE ";
        $sql .= "P.idprofessors    = CP.id_professor AND ";
        //$sql .= "P.idprofessors = 438 AND ";
        $sql .= "CP.id_tipus_contacte = 1 AND P.activat = 'S';";

        $result = $db->query($sql);
        if (!$result) {
            die(_SELECT_PROF . mysqli_error($conn));
        }

        foreach ($result->fetchAll() as $fila) {
            $i = 0;
            $arrprofessorat = array();
            $idProf = $fila[0];
            $nomProf = $fila[1];
            $dataNum = strtotime('previous Monday');

            // Extreiem el correu a enviar
            $sql2 = "SELECT Valor ";
            $sql2 .= "FROM contacte_professor ";
            $sql2 .= "WHERE ";
            $sql2 .= "id_professor = " . $idProf . " AND ";
            $sql2 .= "id_tipus_contacte = 34 ;";
            //echo "<br>".$sql2."<br>";
            $result2 = $db->query($sql2);
            if (!$result2) {
                die(_SELECT_MAIL_PROF . mysqli_error($conn));
            }
            $fila2 = $result2->fetchAll();
            $correuProf = $fila2[0]['Valor'];

            for ($dies = 1; $dies <= 5; $dies++) {
                $data = date('Y-m-d', $dataNum);
                // Per cada professor cada dia
                // Comprovem si és laborable ************************************
                // Mirem si està dintre del periode lectiu
                $datatime = strtotime($data);
                $sqlx = "SELECT data_inici, data_fi,idperiodes_escolars FROM periodes_escolars WHERE actual = 'S';";

                $result3x = $db->query($sqlx);
                if (!$result3x) {
                    die(_SELECT_PERIODE_ESCOLAR . mysqli_error($conn));
                }
                $periode = $result3x->fetchAll();
                if (($datatime <= strtotime($periode[0]['data_inici'])) || ($datatime >= strtotime($periode[0]['data_fi']))) {
                    $laborable = 0;
                }

                // Mirem si és un festiu
                $sqlx = "SELECT COUNT(id_festiu) AS festiu FROM periodes_escolars_festius ";
                $sqlx .= "WHERE festiu = '" . $data . "' AND id_periode = " . $periode[0]['idperiodes_escolars'] . ";";
                $result3x = $db->query($sqlx);
                if (!$result3x) {
                    die(_SELECT_FESTIU . mysqli_error($conn));
                }
                $festiu = $result3x->fetchAll();
                if ($festiu[0]['festiu'] == 0) {
                    $laborable = 1;
                } else {
                    $laborable = 0;
                }
                //*******************************************************************
                if ($laborable == 1) {
                    $sql = "SELECT PGM.idagrups_materies ";
                    $sql .= "FROM prof_agrupament PGM  ";
                    $sql .= "WHERE ";
                    $sql .= "PGM.idprofessors = " . $idProf . ";";
                    $result = $db->query($sql);
                    if (!$result) {
                        die(_SELECT_PROF_GRUP_MAT . mysqli_error($conn));
                    }

                    foreach ($result->fetchAll() as $fila2) {
                        // Per cada grup materia de cada professor, dia a dia  les classes que té
                        $grupMateria = $fila2[0];
                        $sql = "SELECT B.id_dies_franges, C.iddies_setmana, C.dies_setmana, B.idfranges_horaries, D.hora_inici, E.idgrups, E.nom, F.id_mat_uf_pla ";
                        $sql .= "FROM unitats_classe A, dies_franges B, dies_setmana C, franges_horaries D,  ";
                        $sql .= "grups E, moduls_materies_ufs F, grups_materies G ";
                        $sql .= "WHERE ";
                        $sql .= "A.id_dies_franges    = B.id_dies_franges AND ";
                        $sql .= "B.iddies_setmana     = C.iddies_setmana AND ";
                        $sql .= "B.idfranges_horaries = D.idfranges_horaries AND ";
                        $sql .= "A.idgrups_materies   = G.idgrups_materies AND ";
                        $sql .= "G. id_grups	  = E.idgrups AND 		";
                        $sql .= "G.id_mat_uf_pla      = F.id_mat_uf_pla AND ";
                        $sql .= "G.idgrups_materies   = " . $grupMateria . " AND ";
                        $sql .= "B.iddies_setmana     = " . $dies . " ";
                        $sql .= "ORDER BY B.iddies_setmana DESC ,D.hora_inici DESC;";

                        $result3 = $db->query($sql);
                        if (!$result3) {
                            die(_SELECT_DAYS_TIMES . mysqli_error($conn));
                        }

                        foreach ($result3->fetchAll() as $fila3) {
                            $idDiaFranja = $fila3[0];
                            $idDia = $fila3[1];
                            $dia = $fila3[2];
                            $idFranja = $fila3[3];
                            $franja = $fila3[4];
                            $idGrup = $fila3[5];
                            $grup = $fila3[6];
                            $idMateria = $fila3[7];

                            // Extreu nom matèria ******************************
                            $sqlx = "SELECT COUNT(idmateria) AS idmateria FROM materia WHERE idmateria = " . $idMateria . ";";
                            $resultx = $db->query($sqlx);
                            if (!$resultx) {
                                die(_NOM_MAT . mysqli_error($conn));
                            }
                            $filax = $resultx->fetchAll();
                            if ($filax[0]['idmateria'] > 0) {
                                $sqly = "SELECT nom_materia FROM materia WHERE idmateria = " . $idMateria . ";";
                                $resulty = $db->query($sqly);
                                if (!$resulty) {
                                    die(_NOM_MAT2 . mysqli_error($conn));
                                }
                                $filay = $resulty->fetchAll();
                                $nomMateria = $filay[0]['nom_materia'];
                            } else { // SELECT COUNT(*) FROM grups_materies WHERE id_mat_uf_pla = 1169 AND id_grups = 718 AND data_inici <= 2017-08-09 AND data_fi >= 2017-08-09
                                $sqly = "SELECT COUNT(idunitats_formatives) AS idUF FROM unitats_formatives ";
                                $sqly .= "WHERE idunitats_formatives = " . $idMateria . ";";
                                $resulty = $db->query($sqly);
                                if (!$resulty) {
                                    die(_NOM_MAT3 . mysqli_error($conn));
                                }
                                $filay = $resulty->fetchAll();
                                if ($filay[0]['idUF'] > 0) {
                                    // Comprovem que és la UF actual
                                    $sqlz = "SELECT COUNT(*) AS GrupM FROM grups_materies ";
                                    $sqlz .= "WHERE id_mat_uf_pla = " . $idMateria . " AND id_grups = " . $idGrup . " AND ";
                                    $sqlz .= "data_inici <= '" . $data . "' AND data_fi >= '" . $data . "';";
                                    $resultz = $db->query($sqlz);
                                    if (!$resultz) {
                                        die(_NOM_MAT4 . mysqli_error($conn));
                                    }
                                    $filaz = $resultz->fetchAll();
                                    if ($filaz[0]['GrupM'] > 0) {
                                        $sqlt = "SELECT nom_uf FROM unitats_formatives WHERE idunitats_formatives = " . $idMateria . ";";
                                        $resultt = $db->query($sqlt);
                                        if (!$resultt) {
                                            die(_NOM_MAT5 . mysqli_error($conn));
                                        }
                                        $filat = $resultt->fetchAll();
                                        $nomMateria = $filat[0]['nom_uf'];
                                    } else {
                                        $nomMateria = "NO SUBJECT";
                                    }
                                } else {
                                    $nomMateria = "NO SUBJECT";
                                }
                            }


                            // *************************************************
                            // Comprova si ha passat llista ******************
                            $sqlx = "SELECT COUNT(*) AS valor FROM log_professors WHERE dia_franja = " . $idFranja . " AND grup_materia = " . $grupMateria . " ";
                            $sqlx .= "AND data_llista >= '" . $data . "' AND id_accio = 5;  ";
                            //echo "<br>".$sql;
                            $resultx = $db->query($sqlx);
                            if (!$resultx) {
                                die(_SELECT_PASSA_LLISTA . mysqli_error($conn));
                            }
                            $filax = $resultx->fetchAll();
                            $llistax = $filax[0]['valor'];
                            if ($llistax > 0) {
                                $llista = 1;
                            } else {
                                $sqlx = "SELECT COUNT(*) AS valor FROM log_professors WHERE dia_franja = " . $idFranja . " AND grup_materia = " . $grupMateria . " ";
                                $sqlx .= "AND data_llista >= '" . $data . "' AND id_accio = 6;  ";
                                //echo "<br>".$sql;
                                $resultx = $db->query($sqlx);
                                if (!$resultx) {
                                    die(_SELECT_PASSA_LLISTA . mysqli_error($conn));
                                }
                                $filax = $resultx->fetchAll();
                                $llistaGuardiax = $filax[0]['valor'];
                                if ($llistaGuardiax > 0) {
                                    $llista = 2;
                                } else {
                                    $llista = 0;
                                }
                            }
                            // ***********************************************

                            if ((($llista == 0) || ($llista == 2)) && (strcmp($nomMateria, "NO SUBJECT"))) {
                                $arrprofessorat[$i][0] = $data;
                                $arrprofessorat[$i][1] = $dia;
                                $arrprofessorat[$i][2] = $franja;
                                $arrprofessorat[$i][3] = $grup;
                                $arrprofessorat[$i][4] = $nomMateria;
                                if ($llista == 2) {
                                    $arrprofessorat[$i][5] = "Guàrdia";
                                } else {
                                    $arrprofessorat[$i][5] = "";
                                }
                                echo "<br>" . $nomProf . " > " . $arrprofessorat[$i][0] . " > " . $arrprofessorat[$i][1] . " > " . $arrprofessorat[$i][2] . " > " . $arrprofessorat[$i][3] . " > " . $arrprofessorat[$i][4] . " > " . $arrprofessorat[$i][5];
                                $i++;
                            }
                        }
                    }
                }
                $dataNum = strtotime("+ 1 day", $dataNum);
            }

            $count = 0;
            foreach ($arrprofessorat as $professor) {
                $count++;
            }
            if ($count > 0) {
                include(dirname(__FILE__) . '/cron_passa_llista_send.php');
            } else {
                //echo "<br> NO TÉ CAP INCIDÈNCIA".$count;
            }
        }
    }
}
?>
