<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes = isset($_REQUEST['idalumne_periode']) ? $_REQUEST['idalumne_periode'] : 0;

$data_just_desde = isset($_REQUEST['data_just_desde']) ? substr($_REQUEST['data_just_desde'],6,4)."-".substr($_REQUEST['data_just_desde'],3,2)."-".substr($_REQUEST['data_just_desde'],0,2) : '0000-00-00';
$data_just_finsa = isset($_REQUEST['data_just_finsa']) ? substr($_REQUEST['data_just_finsa'],6,4)."-".substr($_REQUEST['data_just_finsa'],3,2)."-".substr($_REQUEST['data_just_finsa'],0,2) : '0000-00-00';

$curs      = getCursActual($db)["idperiodes_escolars"];
$absencia  = isset($_REQUEST['absencia_periode']) ? $_REQUEST['absencia_periode'] : 0;
$retard    = isset($_REQUEST['retard_periode'])   ? $_REQUEST['retard_periode']   : 0;
$seguiment = isset($_REQUEST['seguiment_periode']) ? $_REQUEST['seguiment_periode'] : 0;
$comentari = isset($_REQUEST['comentari']) ? str_replace("'","\'",$_REQUEST['comentari']) : '';

if ($data_just_desde <= $data_just_finsa) {

	$begin = new DateTime( $data_just_desde );
	$end   = new DateTime( $data_just_finsa );
	$end   = $end->modify( '+1 day' );
	
	$interval  = new DateInterval('P1D');
	$daterange = new DatePeriod($begin, $interval ,$end);
	
	foreach($daterange as $data){
		
            $rsFranges    = getFrangesHoraries($db);
            $data_actual  = $data->format("Y-m-d");
            
            if (festiu($db,$data_actual,$curs)== 0)   
                {                
		foreach($rsFranges->fetchAll() as $row) {
			$idfranges_horaries = $row['idfranges_horaries'];
			$rsMateriaAlumne    = getMateriesDiaHoraAlumne($db,date('w'),$idfranges_horaries,$curs,$idalumnes);
                            
                            foreach($rsMateriaAlumne->fetchAll() as $row_mat) {
                                $idmateria    = $row_mat['id_mat_uf_pla'];
                                $idgrup       = $row_mat['idgrups'];
                                $rsProfMateria = getProfessorByGrupMateria($db,$row_mat['idgrups_materies']);
                                foreach($rsProfMateria->fetchAll() as $row_pm) {
                                    $idprofessors = $row_pm['idprofessors'];
                            
                                    if ($absencia) {
                                            $sql  = "DELETE FROM incidencia_alumne ";
                                            $sql .= "WHERE idalumnes=".$idalumnes." AND idfranges_horaries=".$idfranges_horaries;
                                            $sql .= " AND data='".$data_actual."' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_ABSENCIA;

                                            $result = $db->query($sql);
                                    }

                                    if ($retard) {
                                            $sql  = "DELETE FROM incidencia_alumne ";
                                            $sql .= "WHERE idalumnes=".$idalumnes." AND idfranges_horaries=".$idfranges_horaries;
                                            $sql .= " AND data='".$data_actual."' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_RETARD;

                                            $result = $db->query($sql);
                                    }

                                    if ($seguiment) {
                                            $sql  = "DELETE FROM incidencia_alumne ";
                                            $sql .= "WHERE idalumnes=".$idalumnes." AND idfranges_horaries=".$idfranges_horaries;
                                            $sql .= " AND data='".$data_actual."' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_SEGUIMENT;

                                            $result = $db->query($sql);
                                    }
                                    
                                    if ($absencia || $retard || $seguiment) {

                                        $sql  = "DELETE FROM incidencia_alumne ";
                                        $sql .= "WHERE idalumnes=".$idalumnes." AND idfranges_horaries=".$idfranges_horaries;
                                        $sql .= " AND data='".$data_actual."' AND id_tipus_incidencia=".TIPUS_FALTA_ALUMNE_JUSTIFICADA;

                                        $result = $db->query($sql);

                                        $sql  = "INSERT INTO incidencia_alumne  (idalumnes,idgrups,id_mat_uf_pla,idprofessors,id_tipus_incidencia,data,idfranges_horaries,comentari) ";
                                        $sql .= "VALUES ($idalumnes,$idgrup,$idmateria,$idprofessors,".TIPUS_FALTA_ALUMNE_JUSTIFICADA.",'$data_actual',$idfranges_horaries,'$comentari')";

                                        $result = $db->query($sql);                                       
                                    }
                                }
                            }
                    }
                }    
	}
}

echo json_encode(array('success'=>true));
//mysql_close();
?>