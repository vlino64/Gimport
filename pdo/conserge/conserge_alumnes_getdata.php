<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
require_once('../func/func_alumnes.php');

$db->exec("set names utf8");

$data   = isset($_REQUEST['data']) ? $_REQUEST['data'] : date("Y-m-d");
if ($data == '--') {
	$data = date("Y-m-d");
}

$any         = substr($data,0,4);
$mes         = substr($data,5,2);
$dia         = substr($data,8,2);
$dia_setmana = diaSemana($any,$mes,$dia);

$idgrups = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;
$sms     = isset($_REQUEST['sms'])     ? $_REQUEST['sms']     : 0 ;
$cg      = isset($_REQUEST['cg'])      ? $_REQUEST['cg']      : 0 ;

if ($cg) {
    $sql  = "SELECT DISTINCT(agm.idalumnes),ca.Valor AS alumne ";
    $sql .= "FROM alumnes_grup_materia agm ";
    $sql .= "INNER JOIN alumnes a ON agm.idalumnes=a.idalumnes ";
    $sql .= "INNER JOIN contacte_alumne ca ON agm.idalumnes=ca.id_alumne ";
    $sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";	 
    $sql .= "INNER JOIN grups             g ON gm.id_grups            = g.idgrups ";
    $sql .= "WHERE a.activat='S' AND g.idgrups=".$idgrups." AND ca.id_tipus_contacte=".TIPUS_nom_complet;
    $sql .= " ORDER BY 2 ";
}
else {
    $sql  = "SELECT agm.idalumnes, ca.Valor AS alumne,ia.id_tipus_incidencia ";
    $sql .= "FROM incidencia_alumne ia ";
    $sql .= "INNER JOIN alumnes_grup_materia  agm ON ia.idalumnes            = agm.idalumnes "; 
    $sql .= "INNER JOIN grups_materies         gm ON agm.idgrups_materies    = gm.idgrups_materies "; 
    $sql .= "INNER JOIN unitats_classe         uc ON agm.idgrups_materies    = uc.idgrups_materies ";
    $sql .= "INNER JOIN materia                 m ON gm.id_mat_uf_pla        = m.idmateria ";
    $sql .= "INNER JOIN dies_franges           df ON uc.id_dies_franges      = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries       fh ON df.idfranges_horaries   = fh.idfranges_horaries ";
    $sql .= "INNER JOIN contacte_alumne        ca ON agm.idalumnes           = ca.id_alumne ";
    $sql .= "INNER JOIN tipus_falta_alumne     tf ON ia.id_tipus_incidencia  = tf.idtipus_falta_alumne ";
    $sql .= "INNER JOIN grups                  gr ON gm.id_grups             = gr.idgrups ";
    $sql .= "WHERE ia.data='".$data."' AND ca.id_tipus_contacte=".TIPUS_nom_complet." ";
    $sql .= "AND df.iddies_setmana=".$dia_setmana." GROUP BY 1 ";

    $sql .= "UNION ";

    $sql .= "SELECT agm.idalumnes, ca.Valor AS alumne,ia.id_tipus_incidencia ";
    $sql .= "FROM incidencia_alumne ia ";
    $sql .= "INNER JOIN alumnes_grup_materia  agm ON ia.idalumnes            = agm.idalumnes "; 
    $sql .= "INNER JOIN grups_materies         gm ON agm.idgrups_materies    = gm.idgrups_materies "; 
    $sql .= "INNER JOIN unitats_classe         uc ON agm.idgrups_materies    = uc.idgrups_materies ";
    $sql .= "INNER JOIN unitats_formatives     uf ON gm.id_mat_uf_pla        = uf.idunitats_formatives ";
    $sql .= "INNER JOIN moduls_ufs             mu ON gm.id_mat_uf_pla        = mu.id_ufs ";
    $sql .= "INNER JOIN moduls                  m ON mu.id_moduls            = m.idmoduls ";
    $sql .= "INNER JOIN dies_franges           df ON uc.id_dies_franges      = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries       fh ON df.idfranges_horaries   = fh.idfranges_horaries ";
    $sql .= "INNER JOIN contacte_alumne        ca ON agm.idalumnes           = ca.id_alumne ";
    $sql .= "INNER JOIN tipus_falta_alumne     tf ON ia.id_tipus_incidencia  = tf.idtipus_falta_alumne ";
    $sql .= "INNER JOIN grups                  gr ON gm.id_grups             = gr.idgrups ";
    $sql .= "WHERE ia.data='".$data."' AND ca.id_tipus_contacte=".TIPUS_nom_complet." ";
    $sql .= "AND df.iddies_setmana=".$dia_setmana." GROUP BY 1 ";
    $sql .= " ORDER BY 2 ASC ";
}

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
    if ($sms) {
        //$dada = $row["id_alumne"];
        // Indica si és major d'edat
        if (getMajorEdat($db,$row["idalumnes"])) {
            $row["alumne"] = $row["alumne"]." (>=18)";
        }
        // Indica si ja s'ha enviat un sms
        $sql2 = "SELECT COUNT(*) FROM sms_tmp WHERE idalumne = ".$row["idalumnes"]." AND data = '".$data."';";

        $result2=$db->query($sql2);
        foreach($result2->fetchAll() as $fila2) {
            $enviat = $fila2[0];
            if ($enviat > 0) { 
                $row["alumne"] = $row["alumne"]." (Sms enviat)"; }
        }
    }
    array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>