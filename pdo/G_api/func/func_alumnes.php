<?php

function nomCompletAlumne($db, $idfamilies) {
    $result = array();
    $sql = "SELECT af.idalumnes AS idAlumne,ca.Valor AS nomCompletAlumne FROM alumnes_families af 
        INNER JOIN contacte_alumne ca ON af.idalumnes=ca.id_alumne 
        WHERE af.idfamilies='$idfamilies' AND ca.id_tipus_contacte = 1 ORDER BY 2";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result, array(
            'idAlumne' => $row['idAlumne'],
            'nomCompletAlumne' => $row['nomCompletAlumne']));
    }
    return $result;
}

function horariAlumneDia($db, $idalumne, $dia_setmana, $curs_actual){
    $result = array();
    $sql = "SELECT uc.*,CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS dia_hora,m.nom_materia AS nom_materia,ec.descripcio AS descripcio,
            g.nom as grup,fh.idfranges_horaries AS franges_horaries  
    FROM unitats_classe uc
    INNER JOIN alumnes_grup_materia   agm ON uc.idgrups_materies   = agm.idgrups_materies 
    INNER JOIN dies_franges     df ON uc.id_dies_franges    = df.id_dies_franges 
    INNER JOIN dies_setmana     ds ON df.iddies_setmana     = ds.iddies_setmana 
    INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries 
    INNER JOIN espais_centre    ec ON uc.idespais_centre    = ec.idespais_centre 
    INNER JOIN grups_materies   gm ON uc.idgrups_materies   = gm.idgrups_materies 
    INNER JOIN grups             g ON gm.id_grups          = g.idgrups 
    INNER JOIN materia           m ON gm.id_mat_uf_pla      = m.idmateria 
    WHERE fh.esbarjo<>'S' AND ds.iddies_setmana=".$dia_setmana." AND df.idperiode_escolar=".$curs_actual." AND agm.idalumnes=".$idalumne." 

    UNION 

    SELECT uc.*,CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS dia_hora,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia,ec.descripcio,g.nom as grup,fh.idfranges_horaries 
    FROM unitats_classe uc 
    INNER JOIN alumnes_grup_materia   agm ON uc.idgrups_materies   = agm.idgrups_materies 
    INNER JOIN dies_franges     df ON uc.id_dies_franges    = df.id_dies_franges 
    INNER JOIN dies_setmana     ds ON df.iddies_setmana     = ds.iddies_setmana 
    INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries 
    INNER JOIN espais_centre    ec ON uc.idespais_centre    = ec.idespais_centre 
    INNER JOIN grups_materies   gm ON uc.idgrups_materies   = gm.idgrups_materies 
    INNER JOIN grups             g ON gm.id_grups          = g.idgrups 
    INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives 
    INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs 
    INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls 
    WHERE fh.esbarjo<>'S' AND ds.iddies_setmana=".$dia_setmana." AND df.idperiode_escolar=".$curs_actual." AND agm.idalumnes=".$idalumne." 
    AND gm.data_inici<='".date("y-m-d")."' AND gm.data_fi>='".date("y-m-d")."'

    ORDER BY 5"; 
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result, array(
            'dia_hora' => $row['dia_hora'],
            'nom_materia' => $row['nom_materia'],
            'descripcio' => $row['descripcio'],
            'grup' => $row['grup'],            
            'franges_horaries' => $row['franges_horaries']));
    }
    return $result;
}

function getIdFamilia($db, $user) {
    $result = array();
    $sql = "SELECT id_families FROM contacte_families WHERE "
            . "(id_tipus_contacte = 31 AND Valor = '" . $user . "') OR "
            . "(id_tipus_contacte = 35 AND Valor = '" . $user . "')";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result, array(
            'id_families' => $row['id_families']));
    }
    return $result;
}

function getInfoAlumneFamilia($db, $idAlumne, $tipusDada) {
    $result = array();
    $idTipus = getIdTipusContacte($db, $tipusDada);
    $sql = "SELECT cf.Valor as Valor FROM alumnes_families af, contacte_families cf 
        WHERE af.idalumnes = " . $idAlumne . " AND af.idfamilies = cf.id_families AND "
            . "cf.id_tipus_contacte = " . $idTipus . ";";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result, array(
            'dadaSollicitada' => $row['Valor']));
    }
    return $result;
}

function getIdTipusContacte($db, $Tipus) {
    $result = array();
    $sql = "SELECT idtipus_contacte FROM tipus_contacte WHERE Nom_info_contacte = '" . $Tipus . "';";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        $idTipusContacte = $row['idtipus_contacte'];
    }
    return $idTipusContacte;
}

function comprovaLogin($db, $user, $pass) {
    $result = array();
    $idTipusUserTutor1 = getIdTipusContacte($db, 'login');
    $idTipusUserTutor2 = getIdTipusContacte($db, 'login2');
    $idTipusPassTutor1 = getIdTipusContacte($db, 'contrasenya');
    $idTipusPassTutor2 = getIdTipusContacte($db, 'contrasenya2');

    $sql = "SELECT id_families "
            . "FROM contacte_families "
            . "WHERE id_tipus_contacte = '" . $idTipusUserTutor1 . "' AND Valor = '" . $user . "';";
    $rs = $db->query($sql);
    if ($rs->rowCount() == 1) {
        $row = $rs->fetch();
        $idFamilia = $row['id_families'];
        $sql = "SELECT id_families "
                . "FROM contacte_families "
                . "WHERE id_tipus_contacte = '" . $idTipusPassTutor1 . "' AND Valor = '" . md5($pass) . "'"
                . "AND id_families = " . $idFamilia . ";";
        $rs = $db->query($sql);
        if ($rs->rowCount() == 1) return $idFamilia;
    } else {
        $sql = "SELECT id_families "
                . "FROM contacte_families "
                . "WHERE id_tipus_contacte = '" . $idTipusUserTutor2 . "' AND Valor = '" . $user . "';";
        $rs = $db->query($sql);
        if ($rs->rowCount() == 1) {
            $row = $rs->fetch();
            $idFamilia = $row['id_families'];
            $sql = "SELECT id_families "
                    . "FROM contacte_families "
                    . "WHERE id_tipus_contacte = '" . $idTipusPassTutor2 . "' AND Valor = '" . md5($pass) . "'"
                    . "AND id_families = " . $idFamilia . ";";
            $rs = $db->query($sql);
            if ($rs->rowCount() == 1) return $idFamilia;
        }
    }
    return -1;
}
