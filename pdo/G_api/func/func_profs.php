<?php

function profes_GET($db) {
    $result = array();
    $sql = "SELECT idprofessors,codi_professor,activat,historic FROM professors WHERE idprofessors = 417;";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        echo $sql."<br>";
        array_push($result, array(
            'idprofessors' => $row['idprofessors'],
            'codi_professor' => $row['codi_professor'],
            'activat' => $row['activat'],
            'historic' => $row['historic']));
    }
    return $result;
}

function missatgeSendToTutor($db,$idProfessor,$idAlumne,$idGrup,$loginTutor,$numTutor,$missatge) {
    $data = date("Y-m-d");
    $hora = date("H:m:s");
    $sql  = "INSERT INTO missatges_tutor(idprofessor,idalumne,idgrup,login_tutor,num_tutor,data,hora,missatge) "; 
    $sql .= "VALUES (".$idProfessor.",".$idAlumne.",".$idGrup.",'".$loginTutor."',".$numTutor.",'".$data."','".$hora."','".$missatge."');";
    //echo "<br>".$sql;
    $rs = $db->query($sql);
}