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
require_once(dirname(dirname(__FILE__)) . '/bbdd/connect_sms.php');
require_once(dirname(dirname(__FILE__)) . '/func/sms.php');

// Continguts i destinataris
$username = USERNAME_SMS;
$code = PASSWD_SMS;
$header = HEADER_SMS;

ini_set("display_errors", 1);
//$db->exec("SET NAMES utf8");
// Comprovem si s'ha d'executar
$sql2 = "SELECT cron_informe_diari_sms FROM config; ";
$result2 = $db->query($sql2);
$fila2 = $result2->fetchAll();
$executarse = $fila2[0]['cron_informe_diari_sms'];
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

            $sms_array = array();
            $nom_alumnes_array = array();

            // Una volta per cada alumne
            foreach ($result->fetchAll() as $fila) {
                //echo "<br>".$fila[1];
                // Incialitzem variables
                $absencies = 0;


                $data = array_pad(explode("/", $fila[1], 3), 3, null);
                $data_retocada = $data[2] . "-" . $data[1] . "-" . $data[0];
                if (($data[0] != "") AND ( $data[1] != "") AND ( $data[2] != "")) {
                    if (checkdate($data[1], $data[0], $data[2])) {
                        //$data_retocada = (string)$data_retocada;
                        //echo "<br>".$data_retocada." >>> ".$fila[0];
                        $date = date_create($data_retocada);
                        $interval = $date->diff(new DateTime);
                        $age = $interval->y;
                        if (( $age <= 18 ) AND ( $fila[2] == 'S' ) AND ( $fila[2] != 'F' )) {
                            // SABENT QUE L'ALUMNE COMPLEIX QUE SE LI ENVII EL SMS
                            // EXTREIEM TOTES LES DADES QUE ENS FAN FALTA
                            $idAlumne = $fila[0];

                            $sql2 = "SELECT Valor FROM contacte_alumne WHERE ";
                            $sql2 .= "id_alumne = " . $idAlumne . " AND id_tipus_contacte = 1;";
                            $result2 = $db->prepare($sql2);
                            $result2->execute();
                            $nomAlumneArr = $result2->fetch();
                            $nomAlumne = $nomAlumneArr['Valor'];


                            // AGAFEM PER SEPARAT EL D'U TUTOR I L'ALTRE PER LA PRESÈNCIA DE 
                            // REPETICIONS QUE S'ELIMINARAN EN UNA PROPERA ACTUALITZACIÓ


                            $sql = "SELECT idincidencia_alumne,id_tipus_incidencia,"
                                    . "idfranges_horaries,id_mat_uf_pla,comentari FROM incidencia_alumne "
                                    . "WHERE idalumnes = " . $idAlumne . " AND data = '" . $dataAvui . "' "
                                    . "AND id_tipus_incidencia = 1;";

                            $result2 = $db->query($sql);
                            if ($result2->rowCount() != 0) {
                                $sms_tutor = getValorTipusContacteFamilies($db, $idAlumne, TIPUS_mobil_sms);
                                if ($sms_tutor != '') {
                                    $mobil_sms = "+34." . getValorTipusContacteFamilies($db, $idAlumne, TIPUS_mobil_sms);
                                    array_push($sms_array, $mobil_sms);
                                    array_push($nom_alumnes_array, $nomAlumne);
                                }

                                $sms_tutor2 = getValorTipusContacteFamilies($db, $idAlumne, TIPUS_mobil_sms2);
                                if ($sms_tutor2 != '') {
                                    $mobil_sms2 = "+34." . getValorTipusContacteFamilies($db, $idAlumne, TIPUS_mobil_sms2);
                                    array_push($sms_array, $mobil_sms2);
                                    array_push($nom_alumnes_array, $nomAlumne);
                                }
                            }
                        }
                    }
                }
            }

            // Continguts i destinataris
            $contents = "Una absència del seu fill/a ha estat registrada";
            
            $post_string_dest  = implode (',', $sms_array);
            $post_string_nom   = implode (',', $nom_alumnes_array);
            
            $post_data['user'] = $username;
            $post_data['code'] = $code;
            $post_data['contents'] = $contents;
            $post_data['destinataris'] = $post_string_dest;
            $post_data['nom_destinataris'] = $post_string_nom;
            $post_data['header'] = $header;


            foreach ($post_data as $key => $value) {
                $post_items[] = $key . '=' . $value;
            }

            $post_string = implode('&', $post_items);
            echo $post_string;
            $curl_connection = curl_init('http://www.geisoft.cat/sms_gest/enviament_sms.php');
//            curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
//            curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
//            curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
//            curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
//            $result = curl_exec($curl_connection);
//            $error = curl_errno($curl_connection);
//            curl_close($curl_connection);
        }
    }
}

function validatePhone($string) {
    $numbersOnly = ereg_replace("[^0-9]", "", $string);
    $numberOfDigits = strlen($numbersOnly);
    if (($numberOfDigits == 9) && ((substr($numbersOnly, 0, 1) == 6) || (substr($numbersOnly, 0, 1) == 7))) {
        return true;
    } else {
        return false;
    }
}
?>

