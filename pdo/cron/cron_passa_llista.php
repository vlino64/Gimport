<?php

/*
TASQUES PENDENTS
 * Està ordenat per dies però no per franges
   */



require_once(dirname(dirname(__FILE__)).'/bbdd/connect.php');

$db->exec("set names utf8");

echo "No hauries d'accedir a aquesta pàgina ...";

    // Comprovem si s'ha d'executar
    $sql2 = "SELECT cron_passa_llista FROM config; ";
    $result2 = $db->query($sql2);if (!$result2) {die(_SELECT_MAIL_PROF.mysqli_error($conn));}
	$fila2 = $result2->fetchAll();
    $executarse = $fila2[0]['cron_passa_llista'];
    echo "<br>".$executarse;

if ($executarse == 1) {
	
	$iniciSetmana = strtotime('previous Monday');
	$dataInici = date('Y-m-d', $iniciSetmana);
	$fiSetmana = strtotime("+ 4 day",$iniciSetmana);
	$dataFi = date('Y-m-d', $fiSetmana);


	$sql = "SELECT P.idprofessors, CP.Valor ";
	$sql .= "FROM professors P, contacte_professor CP ";
	$sql .= "WHERE ";
	$sql .= "P.idprofessors    = CP.id_professor AND ";
//	$sql .= "P.idprofessors < 425 AND ";
	$sql .= "CP.id_tipus_contacte = 1 AND P.activat = 'S';";

	$result = $db->query($sql); if (!$result) {die(_SELECT_PROF.mysqli_error($conn));}

	foreach($result->fetchAll() as $fila) {
		$i = 0;
		$arrprofessorat = array();
		$idProf = $fila[0];
		$nomProf = $fila[1];
		$dataNum = strtotime('previous Monday');
		
		// Extreiem el correu a enviar
		$sql2 = "SELECT Valor ";
		$sql2 .= "FROM contacte_professor ";
		$sql2 .= "WHERE ";
		$sql2 .= "id_professor = ".$idProf." AND ";
		$sql2 .= "id_tipus_contacte = 34 ;";
		//echo "<br>".$sql2."<br>";
		$result2=$db->query($sql2); if (!$result2) {die(_SELECT_MAIL_PROF.mysqli_error($conn));}
		$fila2 = $result2->fetchAll(); 
		$correuProf = $fila2[0]['Valor'];
			
		for ($dies = 1 ; $dies <=5 ; $dies++){
			$data = date('Y-m-d', $dataNum);
			// Per cada professor cada dia
			if (esLaborable($data,$db)){
	//            
				$sql = "SELECT PGM.idagrups_materies ";
				$sql .= "FROM prof_agrupament PGM  ";
				$sql .= "WHERE ";
				$sql .= "PGM.idprofessors = ".$idProf.";";
				$result2=$result = $db->query($sql); if (!$result2) {die(_SELECT_PROF_GRUP_MAT.mysqli_error($conn));}
				
				foreach($result->fetchAll() as $fila2) {   
				// Per cada grup materia de cada professor, dia a dia  les classes que té
					$grupMateria = $fila2[0];
					$sql = "SELECT B.id_dies_franges, C.iddies_setmana, C.dies_setmana, B.idfranges_horaries, D.hora_inici, E.idgrups, E.nom, F.id_mat_uf_pla ";
					$sql .= "FROM unitats_classe A, dies_franges B, dies_setmana C, franges_horaries D,  ";
					$sql .= "grups E, moduls_materies_ufs F, grups_materies G ";
					$sql .= "WHERE ";
					$sql .= "A.id_dies_franges    = B.id_dies_franges AND ";
					$sql .= "B.iddies_setmana     = C.iddies_setmana AND ";
					$sql .= "B.idfranges_horaries = D.idfranges_horaries AND ";
					$sql .= "A.idgrups_materies   = G.idgrups_materies AND ";
					$sql .= "G. id_grups	  = E.idgrups AND 		";	
					$sql .= "G.id_mat_uf_pla      = F.id_mat_uf_pla AND ";
					$sql .= "G.idgrups_materies   = ".$grupMateria." AND ";
					$sql .= "B.iddies_setmana     = ".$dies." ";	
					$sql .= "ORDER BY B.iddies_setmana DESC ,D.hora_inici DESC;";
					
					$result3=$db->query($sql); if (!$result3) {die(_SELECT_DAYS_TIMES.mysqli_error($conn));}
					
					foreach($result3->fetchAll() as $fila3){
						$idDiaFranja = $fila3[0];
						$idDia = $fila3[1];
						$dia = $fila3[2];
						$idFranja = $fila3[3];
						$franja = $fila3[4];
						$idGrup = $fila3[5];
						$grup = $fila3[6];
						$idMateria = $fila3[7];
						$nomMateria = extreuNomMateria($idMateria,$idGrup,$data,$db);        
						if ((haPassatLlista($idFranja,$grupMateria, $dataInici, $dataFi,$db)== 0) && (strcmp($nomMateria,"NO SUBJECT"))){
							$arrprofessorat[$i][0] = $data;
							$arrprofessorat[$i][1] = $dia;
							$arrprofessorat[$i][2] = $franja;
							$arrprofessorat[$i][3] = $grup;
							$arrprofessorat[$i][4] = $nomMateria;
							//echo "<br>".$nomProf." > ".$arrprofessorat[$i][0]." > ".$arrprofessorat[$i][1]." > ".$arrprofessorat[$i][2]." > ".$arrprofessorat[$i][3]." > ".$arrprofessorat[$i][4];
							$i++;
							}
						   
						}
	 
					}
							
				}            
			$dataNum = strtotime("+ 1 day",$dataNum);

			}
		
		$count = 0;
		foreach ($arrprofessorat as $professor){
		   $count++;
		}
		
		if ($count > 0 ) {
			include('./cron_passa_llista_send.php');
		}
		else {echo "<br> NO TÉ CAP INCIDÈNCIA".$count;}
		
		}    
  
}

mysqli_close($conn);

function haPassatLlista($idFranja,$grupMateria, $dataInici, $dataFi,$db){

   $sql = "SELECT COUNT(*) AS valor FROM log_professors WHERE dia_franja = ".$idFranja." AND grup_materia = ".$grupMateria." ";
   $sql .= "AND data_llista >= '".$dataInici."' AND data_llista <= '".$dataFi."';  ";
   //echo "<br>".$sql;
   $result=$db->query($sql); if (!$result) {die(_SELECT_PASSA_LLISTA.mysqli_error($conn));}
   $fila = $result->fetchAll();
   if ( $fila[0]['valor'] > 0) return 1;
   else return 0 ;
       
}

function extreuNomMateria($idMateria,$idGrup,$data,$db){

    $sql = "SELECT COUNT(idmateria) AS idmateria FROM materia WHERE idmateria = ".$idMateria.";";
    $result=$db->query($sql); if (!$result) {die(_NOM_MAT.mysqli_error($conn));}
    $fila =  $result->fetchAll();
    if ($fila[0]['idmateria'] > 0 )
        {
        $sql = "SELECT nom_materia FROM materia WHERE idmateria = ".$idMateria.";";
        $result=$db->query($sql); if (!$result) {die(_NOM_MAT2.mysqli_error($conn));}
        $fila=  $result->fetchAll();
        return $fila[0]['nom_materia'];
        }
    else // SELECT COUNT(*) FROM grups_materies WHERE id_mat_uf_pla = 1169 AND id_grups = 718 AND data_inici <= 2017-08-09 AND data_fi >= 2017-08-09

        {
        $sql = "SELECT COUNT(idunitats_formatives) AS idUF FROM unitats_formatives ";
        $sql .= "WHERE idunitats_formatives = ".$idMateria.";"; 
        $result=$db->query($sql); if (!$result) {die(_NOM_MAT3.mysqli_error($conn));}
        $fila=  $result->fetchAll();
        if ($fila[0]['idUF'] > 0 )
            {
            // Comprovem que és la UF actual
            $sql = "SELECT COUNT(*) AS GrupM FROM grups_materies ";
            $sql .= "WHERE id_mat_uf_pla = ".$idMateria." AND id_grups = ".$idGrup." AND ";
            $sql .= "data_inici <= '".$data."' AND data_fi >= '".$data."';";
            $result=$db->query($sql); if (!$result) {die(_NOM_MAT4.mysqli_error($conn));}
            $fila=  $result->fetchAll();        
            if ($fila[0]['GrupM'] > 0 )
                {   
                $sql = "SELECT nom_uf FROM unitats_formatives WHERE idunitats_formatives = ".$idMateria.";";
                $result=$db->query($sql); if (!$result) {die(_NOM_MAT5.mysqli_error($conn));}
                $fila=  $result->fetchAll();
                return $fila[0]['nom_uf'];
                }
            else {return "NO SUBJECT";}    
            }
        else {return "NO SUBJECT";}    
        }
    }
   

function esLaborable($data,$db){
    
    // Mirem si està dintre del periode lectiu
    $datatime = strtotime($data);
    $sql = "SELECT data_inici, data_fi,idperiodes_escolars FROM periodes_escolars WHERE actual = 'S';";  

    $result3 = $db->query($sql); if (!$result3) {die(_SELECT_PERIODE_ESCOLAR.mysqli_error($conn));}
    $periode = $result3->fetchAll();
    if (($datatime <= strtotime($periode[0]['data_inici'])) || ($datatime >= strtotime($periode[0]['data_fi']))) {return false;}

    // Mirem si és un festiu
    $sql = "SELECT COUNT(id_festiu) AS festiu FROM periodes_escolars_festius ";
    $sql .= "WHERE festiu = '".$data."' AND id_periode = ".$periode[0]['idperiodes_escolars'].";";
    $result3=$db->query($sql); if (!$result3) {die(_SELECT_FESTIU.mysqli_error($conn));}
    $festiu = $result3->fetchAll();
    if ($festiu[0]['festiu'] == 0) {return true;}
    else {return false;}
   
}



?>
