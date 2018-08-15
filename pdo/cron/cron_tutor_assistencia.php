<?php

// **********************************************************************
// **********************************************************************
// Realització d'informe  setmanal als tutors
// **********************************************************************
// **********************************************************************

require_once(dirname(dirname(__FILE__)) . '/bbdd/connect.php');

$db->exec("set names utf8");

// Comprovem si s'ha d'executar
$sql2 = "SELECT cron_mail_tutor FROM config; ";
$result2 = $db->query($sql2);
if (!$result2) {
    die(_SELECT_MAIL_PROF . mysqli_error($conn));
}
$fila2 = $result2->fetchAll();
$executarse = $fila2[0]['cron_mail_tutor'];

if ($executarse == 1) {


// NOMÉS ACCEPTARÀ PETICIONS DES DE LOCALHOST.
// Si es connecta des de localhost la variable ve buida
// Si es connecta des d'una altra màquina, la variable porta contingut
    if ($_SERVER['REMOTE_ADDR'] != "" ) {
        echo "No hauries d'accedir a aquesta p&agrave;gina .....";
    } else {
        $iniciSetmana = strtotime('previous Monday');
        $dataInici = date('Y-m-d', $iniciSetmana);
        //$dataInici= '2016-10-10';
        $fiSetmana = strtotime("+ 4 day", $iniciSetmana);
        $dataFi = date('Y-m-d', $fiSetmana);
        //$dataFi= '2016-10-14';

        $sql = "SELECT idprofessors, idgrups ";
        $sql .= "FROM professor_carrec ";
        $sql .= "WHERE ";
        $sql .= "(idcarrecs = 1 OR idcarrecs = 2) AND ";
        //$sql .= "idprofessors = 418 AND ";
        $sql .= "principal = 1 ;";
        $result = $db->query($sql);
        if (!$result) {
            die(_SELECT_PROF . mysqli_error($conn));
        }

        foreach ($result->fetchAll() as $fila) {

            $i = 0;
            $arrAlumnat = array();
            $idProf = $fila[0];
            $idGrup = $fila[1];
            $dataNum = strtotime('previous Monday');

// Extreiem el correu a enviar
            $sql2 = "SELECT Valor ";
            $sql2 .= "FROM contacte_professor ";
            $sql2 .= "WHERE ";
            $sql2 .= "id_professor = " . $idProf . " AND ";
            $sql2 .= "id_tipus_contacte = 34 ;";
            $result2 = $db->query($sql2);
            if (!$result2) {
                die(_SELECT_MAIL_PROF . mysqli_error($conn));
            }
            $fila2 = $result2->fetchAll();
            $correuProf = $fila2[0]['Valor'];


// Extreiem el nom del professor
            $sql2 = "SELECT Valor ";
            $sql2 .= "FROM contacte_professor ";
            $sql2 .= "WHERE ";
            $sql2 .= "id_professor = " . $idProf . " AND ";
            $sql2 .= "id_tipus_contacte = 1 ;";
//echo "<br>".$sql2."<br>";
            $result2 = $db->query($sql2);
            if (!$result2) {
                die(_SELECT_MAIL_PROF . mysqli_error($conn));
            }
            $fila2 = $result2->fetchAll();
            $nomProf = $fila2[0]['Valor'];

// Extreiem el nom del grup
            $sql = "SELECT nom ";
            $sql .= "FROM grups  ";
            $sql .= "WHERE ";
            $sql .= "idgrups = " . $idGrup . ";";
            $result2 = $db->query($sql);
            if (!$result2) {
                die(_SELECT_NOM_GRUP . mysqli_error($conn));
            }
            $fila2 = $result2->fetchAll();
            $nomGrup = $fila2[0]['nom'];


            $sql = "SELECT idgrups_materies ";
            $sql .= "FROM grups_materies  ";
            $sql .= "WHERE ";
            $sql .= "id_grups = " . $idGrup . ";";
//echo "<br>".$sql;
            $result2 = $db->query($sql);
            if (!$result2) {
                die(_SELECT_PROF_GRUP_MAT . mysqli_error($conn));
            }

            foreach ($result2->fetchAll() as $fila2) {
// Per cada grup materia de cada professor, dia a dia  les classes que té
                $grupMateria = $fila2[0];
                $sql = "SELECT A.idalumnes AS idalum ";
                $sql .= "FROM alumnes_grup_materia AGM, alumnes A ";
                $sql .= "WHERE ";
                $sql .= "AGM.idalumnes = A.idalumnes AND ";
                $sql .= "A.activat = 'S' AND ";
                $sql .= "idgrups_materies = " . $grupMateria . " ;";
                $result3 = $db->query($sql);
                if (!$result3) {
                    die(_SELECT_DAYS_TIMES . mysqli_error($conn));
                }

                foreach ($result3->fetchAll() as $fila3) {
                    $idAlumne = $fila3[0];
                    $present = false;
                    foreach ($arrAlumnat as $alumnes) {
                        if ($alumnes[0] == $idAlumne)
                            $present = true;
                    }
                    if (!$present) {
                        $arrAlumnat[$i][0] = $idAlumne;
// Completem amb el nom complet
                        $sql = "SELECT Valor FROM contacte_alumne WHERE id_alumne = " . $idAlumne . " AND id_tipus_contacte = 1;";
                        $result4 = $db->query($sql);
                        if (!$result4) {
                            die(_SELECT_DAYS_TIMES . mysqli_error($conn));
                        }
                        $fila4 = $result4->fetchAll();
                        $arrAlumnat[$i][1] = $fila4[0]['Valor'];
                        $i++;
                    }
                }
            }

// Completem l'array amb Absències/Retards/Justificacions/CCC/Seguiments

            $count = 0;
            foreach ($arrAlumnat as $alumnes) {
                $count++;
            }

            for ($i = 0; $i < $count; $i++) {
                $idAlumne = $arrAlumnat[$i][0];
                $nomAlumne = $arrAlumnat[$i][1];

                if (($idAlumne != "") && ($nomAlumne != "")) {
                    $sql = "SELECT COUNT(idincidencia_alumne) FROM incidencia_alumne WHERE id_tipus_incidencia = 1 AND ";
                    $sql .= "idalumnes = " . $idAlumne . " AND data >= '" . $dataInici . "' AND data <= '" . $dataFi . "';";
                    $result2 = $db->query($sql);
                    if (!$result2) {
                        die(_SELECT_INC_ALUMNES1 . mysqli_error($conn));
                    }
                    $fila2 = $result2->fetchAll();
                    $arrAlumnat[$i][2] = $fila2[0]['COUNT(idincidencia_alumne)'];
//            echo "<br>".$arrAlumnat[$i][2];
                    $sql = "SELECT COUNT(idincidencia_alumne) FROM incidencia_alumne WHERE id_tipus_incidencia = 2 AND ";
                    $sql .= "idalumnes = " . $idAlumne . " AND data >= '" . $dataInici . "' AND data <= '" . $dataFi . "';";
                    $result2 = $db->query($sql);
                    if (!$result2) {
                        die(_SELECT_INC_ALUMNES2 . mysqli_error($conn));
                    }
                    $fila2 = $result2->fetchAll();
                    $arrAlumnat[$i][3] = $fila2[0]['COUNT(idincidencia_alumne)'];
//            echo "<br>".$arrAlumnat[$i][3];
                    $sql = "SELECT COUNT(idincidencia_alumne) FROM incidencia_alumne WHERE id_tipus_incidencia = 3 AND ";
                    $sql .= "idalumnes = " . $idAlumne . " AND data >= '" . $dataInici . "' AND data <= '" . $dataFi . "';";
                    $result2 = $db->query($sql);
                    if (!$result2) {
                        die(_SELECT_INC_ALUMNES3 . mysqli_error($conn));
                    }
                    $fila2 = $result2->fetchAll();
                    $arrAlumnat[$i][4] = $fila2[0]['COUNT(idincidencia_alumne)'];
//            echo "<br>".$arrAlumnat[$i][4];
                    $sql = "SELECT COUNT(idincidencia_alumne) FROM incidencia_alumne WHERE id_tipus_incidencia = 4 AND ";
                    $sql .= "idalumnes = " . $idAlumne . " AND data >= '" . $dataInici . "' AND data <= '" . $dataFi . "';";
                    $result2 = $db->query($sql);
                    if (!$result2) {
                        die(_SELECT_INC_ALUMNES4 . mysqli_error($conn));
                    }
                    $fila2 = $result2->fetchAll();
                    $arrAlumnat[$i][5] = $fila2[0]['COUNT(idincidencia_alumne)'];
//            echo "<br>".$arrAlumnat[$i][5];
                    $sql = "SELECT COUNT(idccc_taula_principal) FROM ccc_taula_principal WHERE ";
                    $sql .= "idalumne = " . $idAlumne . " AND data >= '" . $dataInici . "' AND data <= '" . $dataFi . "';";
                    $result2 = $db->query($sql);
                    if (!$result2) {
                        die(_SELECT_INC_ALUMNES5 . mysqli_error($conn));
                    }
                    $fila2 = $result2->fetchAll();
                    $arrAlumnat[$i][6] = $fila2[0]['COUNT(idccc_taula_principal)'];
                }
            }

            include(dirname(__FILE__) . '/cron_tutor_assistencia_send.php');
        }
    }
}


//mysqli_close($conn);
?>
