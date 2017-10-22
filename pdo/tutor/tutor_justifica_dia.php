<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups   = $_REQUEST['grup'];
$idalumnes = $_REQUEST['idalumne'];
$data      = isset($_REQUEST['data_just']) ? substr($_REQUEST['data_just'],6,4)."-".substr($_REQUEST['data_just'],3,2)."-".substr($_REQUEST['data_just'],0,2) : '0000-00-00';
$curs      = getCursActual($db)["idperiodes_escolars"];
$absencia  = isset($_REQUEST['absencia']) ? $_REQUEST['absencia'] : 0;
$retard    = isset($_REQUEST['retard'])   ? $_REQUEST['retard']   : 0;
$seguiment = isset($_REQUEST['seguiment']) ? $_REQUEST['seguiment'] : 0;
$comentari = isset($_REQUEST['comentari']) ? str_replace("'","\'",$_REQUEST['comentari']) : '';

if (festiu($db,$data,$curs)== 0)   
    {
    if (! exitsIncidenciaAlumnebyData($db,$idalumnes,$data)) {
            // Justifiquem a futur, futures absències, retards ...
        $rsFranges    = getFrangesHoraries($db);
        foreach($rsFranges->fetchAll() as $row) {
            $idfranges_horaries = $row['idfranges_horaries'];
            $rsMateriaAlumne    = getMateriesDiaHoraAlumne($db,date('w',strtotime($data)),$idfranges_horaries,$curs,$idalumnes);

            foreach($rsMateriaAlumne->fetchAll() as $row_mat) {
                $idmateria    = $row_mat['id_mat_uf_pla'];
                $idgrup       = $row_mat['idgrups'];
                $rsProfMateria = getProfessorByGrupMateria($db,$row_mat['idgrups_materies']);
                foreach($rsProfMateria->fetchAll() as $row_pm) {
                    $idprofessors = $row_pm['idprofessors'];
                
                    $sql  = "INSERT INTO incidencia_alumne  (idalumnes,idgrups,id_mat_uf_pla,idprofessors,id_tipus_incidencia,data,idfranges_horaries,comentari) ";
                    $sql .= "VALUES ($idalumnes,$idgrup,$idmateria,$idprofessors,".TIPUS_FALTA_ALUMNE_JUSTIFICADA.",'$data',$idfranges_horaries,'$comentari')";

                    $result = $db->query($sql);
                }
            }
        }

    }

    else {

            if ($idalumnes == 0) {

                    if ($absencia) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_ABSENCIA;
                            $result = $db->query($sql);
                    }

                    if ($retard) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_RETARD;
                            $result = $db->query($sql);
                    }

                    if ($seguiment) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_SEGUIMENT;
                            $result = $db->query($sql);
                    }

            }

            else {

                    if ($absencia) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE idalumnes='$idalumnes' AND data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_ABSENCIA;
                            $result = $db->query($sql);
                    }

                    if ($retard) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE idalumnes='$idalumnes' AND data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_RETARD;
                            $result = $db->query($sql);
                    }

                    if ($seguiment) {
                            $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE idalumnes='$idalumnes' AND data='$data' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_SEGUIMENT;
                            $result = $db->query($sql);
                    }

                    //Emplenen les franges horàries que no tenen absències, retards, ...
                    $rsFranges    = getFrangesHoraries($db);				
                    foreach($rsFranges->fetchAll() as $row) {
                            $idfranges_horaries = $row['idfranges_horaries'];
                            if (! exitsIncidenciaAlumnebyDataFranja($db,$idalumnes,$data,$idfranges_horaries)) {

                                    $rsMateriaAlumne = getMateriesDiaHoraAlumne($db,date('w',strtotime($data)),$idfranges_horaries,$curs,$idalumnes);
                                    foreach($rsMateriaAlumne->fetchAll() as $row_mat) {
                                            $idmateria    = $row_mat['id_mat_uf_pla'];
                                            $idgrup       = $row_mat['idgrups'];
                                            $rsProfMateria = getProfessorByGrupMateria($db,$row_mat['idgrups_materies']);
                                            foreach($rsProfMateria->fetchAll() as $row_pm) {
                                                $idprofessors = $row_pm['idprofessors'];
                                            
                                                $sql  = "INSERT INTO incidencia_alumne  (idalumnes,idgrups,id_mat_uf_pla,idprofessors,id_tipus_incidencia,data,idfranges_horaries,comentari) ";
                                                $sql .= "VALUES ($idalumnes,$idgrup,$idmateria,$idprofessors,".TIPUS_FALTA_ALUMNE_JUSTIFICADA.",'$data',$idfranges_horaries,'$comentari')";

                                                $result = $db->query($sql);
                                            }
                                    }

                            }

                    }


            }

        }
    echo json_encode(array('success'=>true));
    }

if (isset($rsFranges)){
	//mysql_free_result($rsFranges);
	//mysql_free_result($rsMateriaAlumne);
}
?>