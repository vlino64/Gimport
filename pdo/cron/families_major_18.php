<?php
// **********************************************************************
// **********************************************************************
// Eliminació accès famílies majors de 18 anys
// **********************************************************************
// **********************************************************************

require_once(dirname(dirname(__FILE__)) . '/bbdd/connect.php');
$db->exec("set names utf8");

// NOMÉS ACCEPTARÀ PETICIONS DES DE LOCALHOST.
// Si es connecta des de localhost la variable ve buida
// Si es connecta des d'una altra màquina, la variable porta contingut
if ($_SERVER['REMOTE_ADDR'] != "") {
    echo "No hauries d'accedir a aquesta p&agrave;gina .....";
} else {


// Al camp acces_families de la taula alumnes pot contenir 3 valors-
// S - Si les famílies tenen accés, N - Si les famílies no tenen accès, 
// F -Si les famĺies tenen accés tot i ser major de 18 anys

    echo "No hauries d'accedir a aquesta p&agrave;gina ...";

    $sql = "SELECT A.idalumnes, B.Valor, A.acces_familia ";
    $sql.= "FROM alumnes A, contacte_alumne B ";
    $sql.= "WHERE A.idalumnes = B.id_alumne AND ";
    $sql.= "B.id_tipus_contacte = '28' AND A.activat = 'S';";
//echo "<br>".$sql;
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
                if (( $age >= 18 ) AND ( $fila[2] == 'S' ) AND ( $fila[2] != 'F' )) {
                    $sql2 = "UPDATE `alumnes` SET `acces_familia` = 'N' WHERE `alumnes`.`idalumnes` = '" . $fila[0] . "';";
//        		echo "<br>".$sql2;
                    $result2 = $db->query($sql2);
                    if (!$result2) {
                        die(_ERR_UPDATE_ALUM1 . mysql_error());
                    }
                }
            }
        }
    }
}
//mysql_close();
?>
