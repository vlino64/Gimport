<?php

require_once('./bbdd/connect.php');
ini_set("display_errors", 1);

$db->exec("set names utf8");
echo "No hauries d'accedir a aquesta p&agrave;gina .....";

// Extreiem el nom del grup
$sql = "SELECT idalumnes ";
$sql .= "FROM alumnes WHERE activat = 'S' ; ";
//echo "<br>".$sql;

$result = $db->query($sql);

foreach ($result->fetchAll() as $fila) {
    $alumne = $fila['idalumnes'];
    $nouCorreu = "";
    $nouTelefon = "";
    $sql2 = "SELECT idfamilies ";
    $sql2 .= "FROM alumnes_families WHERE idalumnes = " . $alumne . " ; ";
    $result2 = $db->query($sql2);
    $familiaArr = $result2->fetch();
    $familia = $familiaArr['idfamilies'];

// Extreiem el correu tutor1
    if ($familia != "") {
        
        // ***************** GESTIO DADES TUTOR 1 *********************
        
        $sql3 = "SELECT idcontacte_families, Valor FROM contacte_families WHERE id_tipus_contacte = 24 AND id_families = " . $familia . " ;";
        $result3 = $db->query($sql3);
        $nouCorreu = "";

        foreach ($result3->fetchAll() AS $dadesArr) {
            $telefonTutor = eliminaprimerCaracter($dadesArr['Valor']);
            if (((substr($telefonTutor, 0, 1) != '6') AND ( substr($telefonTutor, 0, 1) != '7')
                    AND ( strpos($telefonTutor, '@') !== false)) OR ( strlen($telefonTutor) == 0)) {
                if (strlen($telefonTutor) > 0)
                    $nouCorreu = $telefonTutor;
                $sql2 = "DELETE FROM contacte_families WHERE idcontacte_families = " . $dadesArr['idcontacte_families'] . " ;";
                //echo "<br>".$sql2;
                $result2 = $db->query($sql2);
            }
        }
        $sql3 = "SELECT idcontacte_families, Valor FROM contacte_families WHERE id_tipus_contacte = 19 AND id_families = " . $familia . " ;";
        $result3 = $db->query($sql3);
        $nouTelefon = "";

        foreach ($result3->fetchAll() AS $dadesArr) {
            $mailTutor = eliminaprimerCaracter($dadesArr['Valor']);
            if (((strlen($mailTutor) > 0) AND ( strpos($mailTutor, '@') === false)) OR ( strlen($mailTutor) == 0)) {
                if (strlen($mailTutor) > 0)
                    $nouTelefon = $mailTutor;
                $sql2 = "DELETE FROM contacte_families WHERE idcontacte_families = " . $dadesArr['idcontacte_families'] . " ;";
                //echo "<br>".$sql2;
                $result2 = $db->query($sql2);
            }
        }

        if ($nouCorreu != "") {
            $sql2 = "INSERT INTO contacte_families(id_families,id_tipus_contacte,Valor) "
                    . "VALUES (" . $familia . ",19,'" . $nouCorreu . "');";
            $result2 = $db->query($sql2);
        }
        if ($nouTelefon != "") {
            $sql2 = "INSERT INTO contacte_families(id_families,id_tipus_contacte,Valor) "
                    . "VALUES (" . $familia . ",24,'" . $nouTelefon . "');";
            $result2 = $db->query($sql2);
        }
        
        // ***************** GESTIO DADES TUTOR 2 *********************
        
        $sql3 = "SELECT idcontacte_families, Valor FROM contacte_families WHERE id_tipus_contacte = 30 AND id_families = " . $familia . " ;";
        $result3 = $db->query($sql3);
        $nouCorreu = "";

        foreach ($result3->fetchAll() AS $dadesArr) {
            $telefonTutor = eliminaprimerCaracter($dadesArr['Valor']);
            if (((substr($telefonTutor, 0, 1) != '6') AND ( substr($telefonTutor, 0, 1) != '7')
                    AND ( strpos($telefonTutor, '@') !== false)) OR ( strlen($telefonTutor) == 0)) {
                if (strlen($telefonTutor) > 0)
                    $nouCorreu = $telefonTutor;
                $sql2 = "DELETE FROM contacte_families WHERE idcontacte_families = " . $dadesArr['idcontacte_families'] . " ;";
                //echo "<br>".$sql2;
                $result2 = $db->query($sql2);
            }
        }
        $sql3 = "SELECT idcontacte_families, Valor FROM contacte_families WHERE id_tipus_contacte = 29 AND id_families = " . $familia . " ;";
        $result3 = $db->query($sql3);
        $nouTelefon = "";

        foreach ($result3->fetchAll() AS $dadesArr) {
            $mailTutor = eliminaprimerCaracter($dadesArr['Valor']);
            if (((strlen($mailTutor) > 0) AND ( strpos($mailTutor, '@') === false)) OR ( strlen($mailTutor) == 0)) {
                if (strlen($mailTutor) > 0)
                    $nouTelefon = $mailTutor;
                $sql2 = "DELETE FROM contacte_families WHERE idcontacte_families = " . $dadesArr['idcontacte_families'] . " ;";
                //echo "<br>".$sql2;
                $result2 = $db->query($sql2);
            }
        }

        if ($nouCorreu != "") {
            $sql2 = "INSERT INTO contacte_families(id_families,id_tipus_contacte,Valor) "
                    . "VALUES (" . $familia . ",29,'" . $nouCorreu . "');";
//            echo "<br>".$sql2;
            $result2 = $db->query($sql2);
        }
        if ($nouTelefon != "") {
            $sql2 = "INSERT INTO contacte_families(id_families,id_tipus_contacte,Valor) "
                    . "VALUES (" . $familia . ",30,'" . $nouTelefon . "');";
//          echo "<br>".$sql2;
            $result2 = $db->query($sql2);
        }        
        
    }
}

function eliminaprimerCaracter($cadena) {
    if (substr($cadena, 0, 1) == " ") {
        $cadena = str_replace(" ", "", $cadena);
    }
    return $cadena;
}
?>


