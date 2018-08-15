<?php
session_start();
require_once('./bbdd/connect.php');
require_once('./func/constants.php');
require_once('./func/generic.php');
require_once('./func/func_dies.php');
ini_set("display_errors", 1);

$db->exec("set names utf8");
echo "No hauries d'accedir a aquesta p&agrave;gina .....";


echo "Hola";

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S';";
	$result = $db->query($sql);
    $fila = $result->fetch();
    $idPeriode = $fila['idperiodes_escolars'];
    //echo $sql."  ".$idPeriode;

    // Extreiem dates de periode escolar
    $sql2 = "SELECT B.idplans_estudis as pla, F.nom as nom, E.nom_modul as nom2, C.nom_uf as nom3, A.data_inici as datain, A.data_fi as datafi,A.idgrups_materies as idgm ";
    $sql2 .= "FROM grups_materies A, moduls_materies_ufs B,unitats_formatives C, moduls_ufs D, moduls E, grups F "; 
    $sql2 .= "WHERE ( ";
    $sql2 .= "(A.id_mat_uf_pla = B.id_mat_uf_pla) AND ";
    $sql2 .= "(C.idunitats_formatives = B.id_mat_uf_pla) AND ";
    $sql2 .= "(C.idunitats_formatives = D.id_ufs) AND ";
    $sql2 .= "(C.idunitats_formatives = B.id_mat_uf_pla) AND ";
    $sql2 .= "(D.id_moduls = E.idmoduls) AND ";
    $sql2 .= "(A.id_grups = F.idgrups) ";
    $sql2 .= ") ORDER BY nom ASC,nom_modul ASC,nom_uf ASC ;";
    //echo $sql2;
    
    $result2=$db->query($sql2);
    foreach ($result2 -> fetchAll() as $fila2 ) 
		{
		$in = explode("-",$fila2['datain']);
        $fi = explode("-",$fila2['datafi']);
        if (checkdate($in[1],$in[2],$in[0]) && checkdate($fi[1],$fi[2],$fi[0])) 
        			
			{
			
			$sessions = classes_entre_dates($db,$fila2['datain'],$fila2['datafi'],$fila2['idgm'],$idPeriode);
			//echo ">>".$sessions;

			$sql = "SELECT COUNT(id_seguiment) as idseg FROM qp_seguiment ";
			$sql .= "WHERE id_grup_materia = '".$fila2['idgm']."' AND lectiva = 1;";
			$result=$db->query($sql);
			$fila = $result->fetch();
			$lectives = $fila['idseg'];

			$sql = "SELECT seguiment FROM qp_seguiment ";
			$sql .= "WHERE id_grup_materia = '".$fila2['idgm']."' AND seguiment LIKE '%#%';";
			//echo "<br>".$sql;
			$result= $db->query($sql);
			$fila = $result->fetch();
			$darrerSeguiment = $fila['seguiment'];
			
			if ( $darrerSeguiment != "" ){
				$ar_seguiment = explode("#",$darrerSeguiment);
				$seguiment = $ar_seguiment[1];
				$comentari = $ar_seguiment[2];
				echo "<br>".$fila2['nom'].";".$fila2['nom2'].";".$fila2['nom3'].";".$fila2['datain'].";";
				echo $fila2['datafi'].";".$sessions.";".$lectives.";".$seguiment.";".$comentari;
			}
			else {
				echo "<br>".$fila2['nom'].";".$fila2['nom2'].";".$fila2['nom3'].";".$fila2['datain'].";";
				echo $fila2['datafi'].";".$sessions.";".$lectives.";No s'ha introduït seguiment de programació";
			}
		}


     }


?>


 
