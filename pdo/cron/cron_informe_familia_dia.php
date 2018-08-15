<?php
// **********************************************************************
// **********************************************************************
// Realització d'informe diari a les famílies
// **********************************************************************
// **********************************************************************

require_once(dirname(dirname(__FILE__)) . '/bbdd/connect.php');
require_once(dirname(dirname(__FILE__)) . '/func/func_dies.php');
require_once(dirname(dirname(__FILE__)) . '/func/constants.php');
require_once(dirname(dirname(__FILE__)) . '/func/generic.php');


ini_set("display_errors", 1);
//$db->exec("SET NAMES utf8");
// Comprovem si s'ha d'executar
$sql2 = "SELECT cron_informe_diari FROM config; ";
$result2 = $db->query($sql2);
$fila2 = $result2->fetchAll();
$executarse = $fila2[0]['cron_informe_diari'];
//echo "<br>".$executarse;

if ($executarse == 1) {
// NOMÉS ACCEPTARÀ PETICIONS DES DE LOCALHOST.
// Si es connecta des de localhost la variable ve buida
// Si es connecta des d'una altra màquina, la variable porta contingut
    if ($_SERVER['REMOTE_ADDR'] != "") {
        echo "No hauries d'accedir a aquesta p&agrave;gina .....";
    } else {

        // Inicialitzem les variables
        $absencies = 0;
        $retards = 0;
        $justificacions = 0;
        $seguiments = 0;
        $CCC = 0;
        $nombreIncidencies = 0;


// NO CAL COMPROVAR A LA BASE DE DADES. S'HA D'ENVIAR A LES FAMÍLIES QUE 
// TENEN HABILITAT L'ACCÈS
// CALCULEM DATA INICI I DATA FI
        $data = strtotime('today');
        $dataAvui = date('Y-m-d', $data);
        //$dataAvui = "2018-03-27";
        $dataOrdenada = date('d-m-Y', $data);
        //$dataOrdenada = "27-03-2018";
        if (festiu($db, $dataAvui, periode($db)) == 0) {

            // SELECCIONEM ALUMNES ACTIUS. NO CONTROL.LEM ARA SI LA FAMÍLIA 
            // TÉ L'ACCÈS HABILITAT
            $sql = "SELECT A.idalumnes, B.Valor, A.acces_familia ";
            $sql .= "FROM alumnes A, contacte_alumne B ";
            $sql .= "WHERE A.idalumnes = B.id_alumne AND ";
            //$sql .= "A.idalumnes = 795 AND ";
            $sql .= "B.id_tipus_contacte = '28' AND A.activat = 'S';";
            //echo "<br>".$sql;

            $result = $db->query($sql);
            //echo "Files".mysql_num_rows($result);
            if (!$result) {
                die(_ERR_LOOK_FOR_ALUM1 . mysql_error());
            }

            foreach ($result->fetchAll() as $fila) {
                //echo "<br>".$fila[1];
                // Incialitzem variables
                $absencies = 0;
                $retards = 0;
                $justificacions = 0;
                $seguiments = 0;
                $CCC = 0;
                $nombreIncidencies = 0;

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

                            $arrIncidencies = array();
                            for ($i = 0; $i <= 4; $i++) {
                                for ($j = 0; $j <= 4; $j++) {
                                    $arrIncidencies[$i][$j] = 0;
                                }
                            }

                            $sql = "SELECT idincidencia_alumne,id_tipus_incidencia,"
                                    . "idfranges_horaries,id_mat_uf_pla,comentari FROM incidencia_alumne "
                                    . "WHERE idalumnes = " . $idAlumne . " AND data = '" . $dataAvui . "';";

                            $result2 = $db->query($sql);
                            if ($result2->rowCount() != 0) {

                                foreach ($result2->fetchAll() AS $incidencies) {
                                    if ($incidencies['idfranges_horaries'] != 0)
                                        $franja = extreuFranja($incidencies['idfranges_horaries'], $db);
                                    else
                                        $franja = "Sense franja assignada";
                                    switch ($incidencies['id_tipus_incidencia']) {
                                        case 1:
                                            $absencies++;
                                            $arrIncidencies[$nombreIncidencies][0] = 0;
                                            $arrIncidencies[$nombreIncidencies][1] = $franja;
                                            break;
                                        case 2:
                                            $retards++;
                                            $arrIncidencies[$nombreIncidencies][0] = 1;
                                            $arrIncidencies[$nombreIncidencies][1] = $franja;
                                            break;
                                        case 3:
                                            $justificacions++;
                                            $arrIncidencies[$nombreIncidencies][0] = 2;
                                            $arrIncidencies[$nombreIncidencies][1] = $franja;
                                            break;
                                        case 4:
                                            $seguiments++;
                                            $arrIncidencies[$nombreIncidencies][0] = 3;
                                            $arrIncidencies[$nombreIncidencies][1] = $franja;
                                            $arrIncidencies[$nombreIncidencies][2] = $incidencies['comentari'];
                                            break;
                                    }
                                    $nombreIncidencies++;
                                }
                            }
                            $sql = "SELECT `idccc_taula_principal`, `idprofessor`, `idfranges_horaries`, "
                                    . "`id_falta`, `descripcio_detallada` FROM `ccc_taula_principal` "
                                    . "WHERE idalumne = " . $idAlumne . " AND data = '" . $dataAvui . "';";
                            $result2 = $db->query($sql);
                            if ($result2->rowCount() != 0) {
                                foreach ($result2->fetchAll() AS $incidencies) {
                                    if ($incidencies['idfranges_horaries'] != 0)
                                        $franja = extreuFranja($incidencies['idfranges_horaries'], $db);
                                    else
                                        $franja = "Sense franja assignada";
                                    $professor = extreuProfessor($incidencies['idprofessor'], $db);
                                    $tipusCCC = extreuTipusCCC($incidencies['id_falta'], $db);
                                    $CCC++;
                                    $arrIncidencies[$nombreIncidencies][0] = 4;
                                    $arrIncidencies[$nombreIncidencies][1] = $franja;
                                    $arrIncidencies[$nombreIncidencies][2] = $professor;
                                    $arrIncidencies[$nombreIncidencies][3] = $tipusCCC;
                                    $arrIncidencies[$nombreIncidencies][4] = $incidencies['descripcio_detallada'];
                                    $nombreIncidencies++;
                                }
                            }
                            //include(dirname(__FILE__) . '/cron_informe_familia_dia_send.php');

                            if ($nombreIncidencies != 0) {

                                /*                                 * ******************************************************** */
                                // Enviem els correus pertinents, segons la configuració 
                                /*                                 * ******************************************************** */
                                $header = 'MIME-Version: 1.0' . "\r\n";
                                $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                                $header .= 'From: ' . getDadesCentre($db)["nom"] . "<no-reply@geisoft.cat>" . '' . "\r\n";

                                $footer = "\r\n ==============\r\n";
                                $footer .= "<br>Nota: Aquest correu s'ha enviat des d'una adre&ccedil;a  de correu electr&ograve;nic que no accepta correus entrants.\r\n";
                                $footer .= "Si us plau, no respongueu aquest missatge\r\n";



                                $subject = "[GEISoft] Informe de " . $nomAlumne . " del dia " . $dataOrdenada . " .";

                                $content = "<br>";
                                $content .= "____________________________________________________________________<br>";
                                $content .= "ENVIAMENT DE CORREU EN FASE DE PROVES<br>";
                                $content .= "____________________________________________________________________<br>";
                                $content .= "SI DETECT&Egrave;SSIU QUALSEVOL DADA ERR&Ograve;NIA, CONTACTEU AMB EL CENTRE<br>";
                                $content .= "____________________________________________________________________<br><br";
                                $content .= "Benvolguts/des,<br><br>";
                                $content .= "A continuaci&oacute; disposeu d'un resum de les incid&egrave;ncies de <b>" . $nomAlumne . "</b> corresponent al dia d'avui. <br><br>";
                                $content .= "Per ampliar aquesta informaci&oacute;, poden accedir a l'aplicaci&oacute; o contactar amb el tutor/a de l'alumne/a. <br><br>";

                                $tipusIncidencies = array("ABS&Egrave;NCIES", "RETARDS", "JUSTIFICACIONS", "SEGUIMENTS", "CONDUCTES CONTR&Agrave;RIES A LA CONVIV&Egrave;NCIA");

                                $content .= "<b>RESUM</b> :<br>";

                                $content .= "&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[0] . " : " . $absencies . "<br>";
                                $content .= "&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[1] . " : " . $retards . "<br>";
                                $content .= "&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[2] . " : " . $justificacions . "<br>";
                                $content .= "&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[3] . " : " . $seguiments . "<br>";
                                $content .= "&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[4] . " : " . $CCC . "<br>";
                                $content .= "<br><br>";

                                $content .= "<b>DETALL</b> :<br>";
                                //$totalIncidencies = $absencies + $retards + $justificacions + $seguiments + $CCC;
                                $totalIncidencies = $nombreIncidencies;

                                for ($i = 0; $i < 5; $i++) {

                                    $incidencies = 0;
                                    for ($j = 0; $j < $totalIncidencies; $j++) {
                                        if ($arrIncidencies[$j][0] == $i)
                                            $incidencies++;
                                    }
                                    if ($incidencies != 0) {
                                        $content .= "<br>&nbsp;&nbsp;&nbsp;" . $tipusIncidencies[$i] . "<br>";
                                        for ($j = 0; $j < $totalIncidencies; $j++) {

                                            if ($arrIncidencies[$j][0] == $i) {
                                                $content .= "Franja: " . $arrIncidencies[$j][1] . "<br>";
                                                if ($i == 3)
                                                    $content .= "  - " . $arrIncidencies[$j][2];
                                                if ($i == 4) {
                                                    $content .= "Professor: " . $arrIncidencies[$j][2];
                                                    $content .= "<br>Tipus: " . $arrIncidencies[$j][3];
                                                    $content .= "<br>Descripció: " . $arrIncidencies[$j][4];
                                                    $content .= "<br>_____________________________<br>";
                                                }
                                            }
                                        }
                                    }
                                }
                                $content .= "<br>Salutacions.<br><br>";
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
                                //echo "<br>" . $to . "<br>";
                                //echo "<br>" . $header . "<br>";
                                //echo "<br>" . $idAlumne . "<br>";
                                //echo "<br>" . "  ABS:".$absencies ."  RET:".$retards ."  JUST:".$justificacions ."  SEG:".$seguiments ."  CCC:".$CCC . "<br>";
                                //echo "<br>" . $subject . "<br>";
                                //echo "<br>" . $content . "<br>";


                                /*                                 * ******************************************************** */
                                /*                                 * ******************************************************** */
                            }
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

function extreuFranja($franja, $db) {
    $sql = "SELECT hora_inici,hora_fi "
            . "FROM franges_horaries "
            . "WHERE idfranges_horaries = " . $franja . " ;";
    //echo "<br>" . $sql;
    $result = $db->query($sql);
    $horaris = $result->fetch();
    $horariInici = explode(":", $horaris[0]);
    $horaris[0] = $horariInici[0] . ":" . $horariInici[1];
    $horariFi = explode(":", $horaris[1]);
    $horaris[1] = $horariFi[0] . ":" . $horariFi[1];
    $franja = $horaris[0] . " - " . $horaris[1];
    return $franja;
}

function extreuProfessor($idprofessor, $db) {
    $sql = "SELECT Valor FROM contacte_professor "
            . "WHERE id_professor = " . $idprofessor . " "
            . "AND id_tipus_contacte = 1;";
    $result = $db->query($sql);
    $horaris = $result->fetch();
    return $horaris['Valor'];
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
?>

