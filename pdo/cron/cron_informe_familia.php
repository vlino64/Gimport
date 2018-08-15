<?php
// **********************************************************************
// **********************************************************************
// Realització d'informe setmanal a les famílies
// **********************************************************************
// **********************************************************************


require_once(dirname(dirname(__FILE__)) . '/bbdd/connect.php');
require_once(dirname(dirname(__FILE__)) . '/func/func_dies.php');
require_once(dirname(dirname(__FILE__)) . '/func/constants.php');
require_once(dirname(dirname(__FILE__)) . '/func/generic.php');

ini_set("display_errors", 1);
//$db->exec("set names utf8");

$sql = "SELECT cron_informe_setmanal FROM config;";
$result = $db->query($sql);
$confArr = $result->fetch();
if ($confArr['cron_informe_setmanal'] == 1) {

// NOMÉS ACCEPTARÀ PETICIONS DES DE LOCALHOST.
// Si es connecta des de localhost la variable ve buida
// Si es connecta des d'una altra màquina, la variable porta contingut
    if ($_SERVER['REMOTE_ADDR'] != "") {
        echo "No hauries d'accedir a aquesta p&agrave;gina .....";
    } else {

// NO CAL COMPROVAR A LA BASE DE DADES. S'HA D'ENVIAR A LES FAMÍLIES QUE 
// TENEN HABILITAT L'ACCÈS
// CALCULEM DATA INICI I DATA FI
        $iniciSetmana = strtotime('previous Monday');
        $dataInici = date('Y-m-d', $iniciSetmana);
//$dataInici= '2016-10-10';
        $fiSetmana = strtotime("+ 4 day", $iniciSetmana);
        $dataFi = date('Y-m-d', $fiSetmana);
//$dataFi= '2016-10-14';
//COMPROVEM SI LA SETMANA HA ESTAT TOTA FESTIVA
        $setmanaFestiva = true;
        for ($i = 0; $i < 5 && $setmanaFestiva; $i++) {
            $dataTmp = strtotime("+ $i day", $iniciSetmana);
            $dataTmpFormatada = date('Y-m-d', $dataTmp);
            if (festiu($db, $dataTmpFormatada, periode($db)) == 0)
                $setmanaFestiva = false;
        }

        if (!$setmanaFestiva) {
//
//
// SELECCIONEM ALUMNES ACTIUS. NO CONTROL.LEM ARA SI LA FAMÍLIA 
// TÉ L'ACCÈS HABILITAT
            $sql = "SELECT A.idalumnes, B.Valor, A.acces_familia ";
            $sql .= "FROM alumnes A, contacte_alumne B ";
            $sql .= "WHERE A.idalumnes = B.id_alumne AND ";
            //$sql .= "A.idalumnes = 1538 AND ";
            $sql .= "B.id_tipus_contacte = '28' AND A.activat = 'S' AND A.acces_familia = 'S';";
//    echo "<br>" . $sql;

            $result = $db->query($sql);
//echo "Files".mysql_num_rows($result);
            if (!$result) {
                die(_ERR_LOOK_FOR_ALUM1 . mysql_error());
            }

            foreach ($result->fetchAll() as $fila) {
//echo "<br>".$fila[1];
                $data = array_pad(explode("/", $fila[1], 3), 3, null);
                $data_retocada = $data[2] . "-" . $data[1] . "-" . $data[0];
                if (($data[0] != "") AND ( $data[1] != "") AND ( $data[2] != "")) {
                    if (checkdate($data[1], $data[0], $data[2])) {
//$data_retocada = (string)$data_retocada;
//echo "<br>".$data_retocada." >>> ".$fila[0];
                        $date = date_create($data_retocada);
                        $interval = $date->diff(new DateTime);
                        $age = $interval->y;
//echo $age;
//echo "<br>".$age;
                        if (( $age <= 18 ) AND ( $fila[2] == 'S' ) AND ( $fila[2] != 'F' )) {
                            // SABENT QUE L'ALUMNE COMPLEIX QUE SE LI ENVII EL CORREU
                            // EXTREIEM TOTES LES DADES QUE ENS FAN FALTA
                            $idAlumne = $fila[0];

                            $sql2 = "SELECT Valor FROM contacte_alumne WHERE ";
                            $sql2 .= "id_alumne = " . $idAlumne . " AND id_tipus_contacte = 1;";
                            $result2 = $db->prepare($sql2);
                            $result2->execute();
                            $nomAlumneArr = $result2->fetch();
                            $nomAlumne = $nomAlumneArr['Valor'];
//                echo "<br>>>>>".$nomAlumne;
                            // MIREM ELS CORREUS DE LES FAMÍLIES PER SEPARAT
                            // EM CONSTA QUE HI HA CERTES REPETICIONS QUE S'HAURIEN D'ELIMINAR

                            $correusArr = array();
                            $correusArr[0] = "";
                            $correusArr[1] = "";
                            $i = 0;

                            // AGAFEM PER SEPARAT EL D'U TUTOR I L'ALTRE PER LA PRESÈNCIA DE 
                            // REPETICIONS QUE S'ELIMINARAN EN UNA PROPERA ACTUALITZACIÓ
                            $sql2 = "SELECT B.Valor ";
                            $sql2 .= "FROM alumnes_families A, contacte_families B ";
                            $sql2 .= "WHERE A.idalumnes = " . $idAlumne . " AND ";
                            $sql2 .= "A.idfamilies = B.id_families AND ";
                            $sql2 .= "(B.id_tipus_contacte = 19);";
                            $result2 = $db->prepare($sql2);
                            $result2->execute();
                            foreach ($result2->fetchAll() as $correus) {
                                if (validEmail($correus['Valor']))
                                    $correusArr[0] = $correus['Valor'];
                                $i++;
                            }

                            $sql2 = "SELECT B.Valor ";
                            $sql2 .= "FROM alumnes_families A, contacte_families B ";
                            $sql2 .= "WHERE A.idalumnes = " . $idAlumne . " AND ";
                            $sql2 .= "A.idfamilies = B.id_families AND ";
                            $sql2 .= "(B.id_tipus_contacte = 29);";
                            $result2 = $db->prepare($sql2);
                            $result2->execute();
                            foreach ($result2->fetchAll() as $correus) {
                                if (validEmail($correus['Valor']))
                                    $correusArr[1] = $correus['Valor'];
                                $i++;
                            }

                            $iniciSetmana = strtotime('previous Monday');
                            $arrIncidencies = array();
                            for ($i = 0; $i <= 4; $i++) {
                                for ($j = 0; $j <= 5; $j++) {
                                    $arrIncidencies[$i][$j] = 0;
                                }
                            }
                            for ($i = 0; $i < 5; $i++) {

                                $Setmana = strtotime("+ $i day", $iniciSetmana);
                                $data = date('Y-m-d', $Setmana);
                                if (festiu($db, $data, periode($db)) == 0) {
                                    $arrIncidencies[$i][0] = $data;
                                    $sql = "SELECT idincidencia_alumne,id_tipus_incidencia FROM incidencia_alumne "
                                            . "WHERE idalumnes = " . $idAlumne . " AND data = '" . $data . "';";
                                    $result2 = $db->query($sql);
                                    if ($result2->rowCount() != 0) {
                                        foreach ($result2->fetchAll() AS $incidencies) {
                                            switch ($incidencies['id_tipus_incidencia']) {
                                                case 1:
                                                    $arrIncidencies[$i][1] ++;
                                                    break;
                                                case 2:
                                                    $arrIncidencies[$i][2] ++;
                                                    break;
                                                case 3:
                                                    $arrIncidencies[$i][3] ++;
                                                    break;
                                                case 4:
                                                    $arrIncidencies[$i][4] ++;
                                                    break;
                                            }
                                        }
                                    }
                                    $sql = "SELECT `idccc_taula_principal`, `idprofessor`, `idfranges_horaries`, "
                                            . "`id_falta`, `descripcio_detallada` FROM `ccc_taula_principal` "
                                            . "WHERE idalumne = " . $idAlumne . " AND data = '" . $data . "';";
                                    $result2 = $db->query($sql);

                                    if ($result2->rowCount() != 0) {
                                        foreach ($result2->fetchAll() AS $incidencies) {
                                            $tipusCCC = extreuTipusCCC($incidencies['id_falta'], $db);
                                            $arrIncidencies[$i][5] ++;
                                        }
                                    }
                                }
                            }
                            //include(dirname(__FILE__) . '/cron_informe_familia_send.php');
                            /*                             * ******************************************************** */
// Enviem els correus pertinents, segons la configuració 
                            /*                             * ******************************************************** */
                            $header = 'MIME-Version: 1.0' . "\r\n";
                            $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                            $header .= 'From: ' . getDadesCentre($db)["nom"] . "<no-reply@geisoft.cat>" . '' . "\r\n";

                            $footer = "\r\n ==============\r\n";
                            $footer .= "<br>Nota: Aquest correu s'ha enviat des d'una adre&ccedil;a  de correu electr&ograve;nic que no accepta correus entrants.\r\n";
                            $footer .= "Si us plau, no respongueu aquest missatge\r\n";



                            $subject = "[GEISoft] Informe setmanal de " . $nomAlumne . " ";

                            $content = "<br>";
                            $content .= "____________________________________________________________________<br>";
                            $content .= "ENVIAMENT DE CORREU EN FASE DE PROVES<br>";
                            $content .= "____________________________________________________________________<br>";
                            $content .= "SI DETECT&Egrave;SSIU QUALSEVOL DADA ERR&Ograve;NIA, CONTACTEU AMB EL CENTRE<br>";
                            $content .= "____________________________________________________________________<br><br";
                            $content .= "Benvolguts/des,<br><br>";
                            $content .= "A continuaci&oacute; disposeu d'un resum de les incid&egrave;ncies de <b>" . $nomAlumne . "</b> durant aquest setmana. <br><br>";
                            $content .= "Per ampliar aquesta informaci&oacute;, poden accedir a l'aplicaci&oacute; o contactar amb el tutor/a de l'alumne/a. <br><br>";

                            $tipusIncidencies = array("ABS&Egrave;NCIES<br>", "RETARDS<br>", "JUSTIFICACIONS<br>", "SEGUIMENTS<br>", "CONDUCTES CONTR&Agrave;RIES A LA CONVIV&Egrave;NCIA<br>");
                            for ($i = 1; $i <= 5; $i++) {
                                $content .= $tipusIncidencies[$i - 1];
                                $count = 0;
                                for ($j = 0; $j <= 4; $j++) {
                                    $count = $count + $arrIncidencies[$j][$i];
                                }
                                if ($count == 0)
                                    $content .= "No en consten <br>";
                                for ($j = 0; $j <= 4; $j++) {
                                    if ($arrIncidencies[$j][$i] != 0)
                                        $content .= "   " . $arrIncidencies[$j][0] . ": " . $arrIncidencies[$j][$i] . "<br>";
                                }
                                $content .="<br>";
                            }
                            $content .= "Salutacions.<br><br>";
                            $to = "";
                            $to2 = "";
                            if ($correusArr[0] != "") {
                                $to = $correusArr[0];
                                mail($to, $subject, $content . $footer, $header);
                            }
                            if ($correusArr[1] != "") {
                                $to = $correusArr[1];
                                mail($to, $subject, $content . $footer, $header);
                            }
                            echo "<br>" . $header . "<br>";
                            echo "<br>" . $footer . "<br>";
                            echo "<br>" . $subject . "<br>";
                            echo "<br>" . $content . "<br>";
                        }
                    }
                }
            }
        }
    }
}

function validEmail($email) {
    return !!filter_var($email, FILTER_VALIDATE_EMAIL);
}

function extreuTipusCCC($id_falta, $db) {
    $sql = "SELECT nom_falta FROM ccc_tipus "
            . "WHERE idccc_tipus = " . $id_falta . ";";
    $result = $db->query($sql);
    $ccc = $result->fetch();
    return $ccc['nom_falta'];
}

function periode($db) {
    $sql = "SELECT idperiodes_escolars FROM periodes_escolars "
            . "WHERE actual = 'S'";
    $result = $db->query($sql);
    $periode = $result->fetch();
    return $periode['idperiodes_escolars'];
}

//mysqli_close($conn);
?>
