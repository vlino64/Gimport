<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : date("Y-m-d");
if ($data_inici=='--') {
    $data_inici = date("Y-m-d");
}
    
$data_fi    = isset($_REQUEST['data_fi']) ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2) : date("Y-m-d");
if ($data_fi=='--') {
    $data_fi = date("Y-m-d");
}

$idprofessor = isset($_REQUEST['idprofessor']) ? intval($_REQUEST['idprofessor']) : 0;   
$curs        = getCursActual($db)["idperiodes_escolars"];

// Recorrem entre dates
$startdate   = strtotime($data_inici);
$enddate     = strtotime($data_fi);

$dates_array = array();
$count_dates = 0;

if ($idprofessor != 0) {

 while($startdate <= $enddate){
    
    $data_fi     = date_format(date_create($data_fi), 'd-m-Y');
    $data_fi_c   = date_format(date_create($data_fi), 'Y-m-d');
    $dia_setmana = date_format(date_create($data_fi), 'w');
    
    //
    // Agafar l'SQL del fitxer ./abs_prof/com_abs_prof_getdata.php
    //
    
    $sql  = "SELECT uc.*,pa.idagrups_materies,g.idgrups, m.idmateria, m.nom_materia AS materia,ec.descripcio AS espaicentre,g.nom as grup, ";
    $sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,fh.idfranges_horaries,pa.idprofessors ";
    $sql .= "FROM prof_agrupament pa ";
    $sql .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
    $sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
    $sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
    $sql .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
    $sql .= "INNER JOIN materia             m ON gm.id_mat_uf_pla      = m.idmateria ";
    $sql .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
    $sql .= "WHERE df.iddies_setmana=$dia_setmana AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs AND pa.idprofessors=$idprofessor ";

    $sql .= " UNION ";

    $sql .= "SELECT uc.*,pa.idagrups_materies,g.idgrups, uf.idunitats_formatives, CONCAT(m.nom_modul,'-',uf.nom_uf) AS materia, ";
    $sql .= "ec.descripcio AS espaicentre,g.nom as grup,CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,fh.idfranges_horaries,pa.idprofessors ";
    $sql .= "FROM prof_agrupament pa ";
    $sql .= "INNER JOIN unitats_classe     uc ON pa.idagrups_materies  = uc.idgrups_materies ";
    $sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges    = df.id_dies_franges ";
    $sql .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries ";
    $sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre    = ec.idespais_centre ";
    $sql .= "INNER JOIN grups_materies     gm ON uc.idgrups_materies   = gm.idgrups_materies ";
    $sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla      = uf.idunitats_formatives ";
    $sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla      = mu.id_ufs ";
    $sql .= "INNER JOIN moduls              m ON mu.id_moduls          = m.idmoduls ";
    $sql .= "INNER JOIN grups               g ON gm.id_grups           = g.idgrups ";
    $sql .= "WHERE df.iddies_setmana=$dia_setmana AND fh.esbarjo<>'S' AND df.idperiode_escolar=$curs AND pa.idprofessors=$idprofessor ";
    $sql .= "AND gm.data_inici<='".$data_fi_c."' AND gm.data_fi>='".$data_fi_c."'";
    $sql .= " ORDER BY 11";
    
    $rec = $db->query($sql);

    $classes_dia_array  = array();
    foreach($rec->fetchAll() as $row) {
	   $result = $row;
	   $id_dies_franges    = $result["id_dies_franges"];
	   $idfranges_horaries = $result["idfranges_horaries"];
	   $franja_horaria     = getLiteralFranjaHoraria($db,$idfranges_horaries);
           $grup_materia       = $result["idgrups_materies"];
           
           $id_professor_guardia = 0;
           $nom_prof_guardia = "";
           
           if (existLogProfessorDataFranjaGrupMateria($db,$idprofessor,TIPUS_ACCIO_PASALLISTA,$data_fi_c,$idfranges_horaries,$grup_materia)) {
                $passa_llista = 'S';
           }
           else if (existLogDataFranjaGrupMateria($db,TIPUS_ACCIO_PASALLISTAGUARDIA,$data_fi_c,$idfranges_horaries,$grup_materia)) {
               // Per aquest cas, no cal comprovar que el professor sigui el conectat, només que existeixi 
               // una guàrdia en una hora d'aquest professor on s'ha passat llista 
               $passa_llista = 'G';
               $id_professor_guardia = getProfessorsLogById($db,existLogDataFranjaGrupMateria($db,TIPUS_ACCIO_PASALLISTAGUARDIA,$data_fi_c,$idfranges_horaries,$grup_materia))["id_professor"];
               $nom_prof_guardia = getProfessor($db,$id_professor_guardia,TIPUS_nom_complet);
           }
           else {
                $passa_llista = 'N';
           }
           
           array_push( $classes_dia_array,array(
                "id"	       => "",
                "data"	       => "$franja_horaria",
                "hora"	       => $result["hora"],
                "grup"         => $result["grup"],
                "materia"      => $result["materia"],
                "profe_guardia" => $nom_prof_guardia,
                "passa_llista" => "$passa_llista")); 
    }
    
    if (!festiu($db,$data_fi_c,$curs)) {
        array_push( $dates_array,array(
                "id"		  => "$count_dates",
		"data"		  => "$data_fi",
                "children"        => $classes_dia_array) );
    }
    
   	  /*if (!festiu($db,$data_fi,$periode)) {
		$diasetmana         = date_format(date_create($data_fi), 'w');
		$literaldiasetmana  = date_format(date_create($data_fi), 'l');
		$datasortida        = date_format(date_create($data_fi), 'd-m-Y');
			
		$sql="SELECT id_dies_franges,idfranges_horaries FROM dies_franges WHERE iddies_setmana=".$diasetmana." ORDER BY id_dies_franges DESC";
		$rec = $db->query($sql);

		
		while($row = mysql_fetch_object($rec)) {
		   $result = $row;
		   $id_dies_franges    = $result->id_dies_franges;
		   $idfranges_horaries = $result["idfranges_horaries"];
		   $franja_horaria     = getLiteralFranjaHoraria($db,$idfranges_horaries);
		   
		   $sql_uc  = "SELECT * FROM unitats_classe WHERE idgrups_materies='".$idgrups_materies."' AND ";
                   $sql_uc .= "id_dies_franges= ".$id_dies_franges ;
		   $rec_uc = $db->query($sql_uc);

		   while($row_uc = mysql_fetch_object($rec_uc)) {
			   $result_uc = $row_uc;
			   
			   $sql_qp  = "SELECT * FROM qp_seguiment WHERE data='".$data_fi."' ";
                           $sql_qp .= "AND id_grup_materia='".$idgrups_materies."' AND id_dia_franja='".$id_dies_franges."' ORDER BY data DESC; ";
			   $rec_qp = $db->query($sql_qp);
			   
			   $id_seguiment    = 0;
			   $lectiva         = 0;
			   $seguiment       = '';
			   
			   while($row_qp = mysql_fetch_object($rec_qp)) {
					$result_qp       = $row_qp;
					$id_seguiment    = $row_qp->id_seguiment;
					$lectiva         = $row_qp->lectiva;
					$seguiment       = $row_qp->seguiment;
			   }
			   
			   $count_dates++;
			   array_push( $dates,array(
			   				"id_seguiment"    => $id_seguiment,
							"data"			  => "$datasortida",
							"id_dia_franja"   => $id_dies_franges,
							"id_grup_materia" => $idgrups_materies,
			   				"dia"             => daysCatalan($literaldiasetmana),
							"franja_horaria"  => $franja_horaria,
							"lectiva" 		  => $lectiva,
							"seguiment" 	  => $seguiment));
		   }
		   	   
		}	
	  }*/
            
	  $data_fi = date("Y-m-d", strtotime("$data_fi -1 day"));
	  $startdate += 86400;
          $count_dates ++;
   
 }
}
   
if (isset($rec)) {
    //mysql_free_result($rec);
}

/*if (isset($rec_uc)) {
    //mysql_free_result($rec_uc);
}
if (isset($rec_gp)) {
    //mysql_free_result($rec_gp);
}*/

echo json_encode($dates_array);

//mysql_close();  
?>

