<?php

/*  ******************************************************************************************************** */
/*   getLiteralTipusIncident--> Nom d'un tipus d'incident
************************************************************************************************************ */
function getLiteralTipusIncident($db,$idtipus_incident) {
    $sql = "SELECT * FROM tipus_incidents WHERE idtipus_incident=".$idtipus_incident;
    $rec = $db->query($sql);
    
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
    //mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidencia --> Incidencia d'un alumne per ID
************************************************************************************************************ */
function getIncidencia($db,$idincidencia_alumne) {
     $sql  = "SELECT * ";
     $sql .= "FROM incidencia_alumne ";
     $sql .= "WHERE idincidencia_alumne=".$idincidencia_alumne;	

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     return $result;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	exitsIncidenciaAlumne --> Existeix incidència?
************************************************************************************************************ */
function exitsIncidenciaAlumne($db,$idalumnes,$data,$idfranges_horaries,$id_mat_uf_pla,$idgrups) {
	 $sql  = "SELECT * ";
	 $sql .= "FROM incidencia_alumne ";
	 //$sql .= "WHERE idalumne_agrupament=".$idalumne_agrupament." AND idprofessors=".$idprofessors." AND data='".$data."' ";	
	 $sql .= "WHERE idalumnes=".$idalumnes." AND id_mat_uf_pla=".$id_mat_uf_pla." "." AND data='".$data."' ";	
	 $sql .= "AND idfranges_horaries=".$idfranges_horaries." AND idgrups=".$idgrups." ";
	 
	 $rec = $db->query($sql);
         $count = 0;
         $result = "";
         foreach($rec->fetchAll() as $row) {
		$count++;
		$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count == 0) {
	    return 0;
	 }
	 else {
            return $result["id_tipus_incidencia"];
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciaAlumne --> Incidencia d'un alumne per grup, materia, profesor i data
	                        Per llistar les faltes d'assistència abans de la seva edició
************************************************************************************************************ */
function getIncidenciaAlumne($db,$idalumnes,$data,$idfranges_horaries) {
	 $sql  = "SELECT * ";
	 $sql .= "FROM incidencia_alumne ";
	 //$sql .= "WHERE idalumne_agrupament=".$idalumne_agrupament." AND idprofessors=".$idprofessors." AND data='".$data."' ";	
	 $sql .= "WHERE idalumnes=".$idalumnes." AND data='".$data."' ";	
	 $sql .= "AND idfranges_horaries=".$idfranges_horaries." ";

	 $rec = $db->query($sql);
         $count = 0;
         $result = "";
         foreach($rec->fetchAll() as $row) {
		$count++;
		$result = $row;
	 }
	 //mysql_free_result($rec);
	 return $result;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	existsIncidenciaAlumnebyTipus --> Incidencia d'un alumne per data, franja horària i tipus
************************************************************************************************************ */
function existsIncidenciaAlumnebyTipus($db,$idalumnes,$data,$idfranges_horaries,$id_tipus_incidencia) {
	 $sql  = "SELECT * ";
	 $sql .= "FROM incidencia_alumne ";
	 //$sql .= "WHERE idalumne_agrupament=".$idalumne_agrupament." AND idprofessors=".$idprofessors." AND data='".$data."' ";	
	 $sql .= "WHERE idalumnes=".$idalumnes." AND data='".$data."' ";	
	 $sql .= "AND idfranges_horaries=".$idfranges_horaries." AND id_tipus_incidencia=".$id_tipus_incidencia;

	 $rec = $db->query($sql);
         $count = 0;
         $result = "";
         foreach($rec->fetchAll() as $row) {
            $count++;
            $result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count == 0) {
	   return 0;
	 }
	 else {
           return 1;
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	exitsIncidenciaAlumnebyData --> Indica si existeix una incidència per un alumne i dia
	                                Per justificar faltes a futur.
************************************************************************************************************ */
function exitsIncidenciaAlumnebyData($db,$idalumnes,$data) {
     $sql  = "SELECT id_tipus_incidencia ";
     $sql .= "FROM incidencia_alumne ";
     $sql .= "WHERE idalumnes=".$idalumnes." AND data='".$data."' ";	

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
	 
     if ($count == 0) {
	   return 0;
     }
     else {
           return 1;
     }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	exitsIncidenciaAlumnebyDataFranja --> Indica si existeix una incidència per un alumne, dia i hora
	                                Per justificar faltes a futur.
************************************************************************************************************ */
function exitsIncidenciaAlumnebyDataFranja($db,$idalumnes,$data,$idfranges_horaries) {
     $sql  = "SELECT COUNT(id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ";
     $sql .= "WHERE idalumnes=".$idalumnes." AND data='".$data."' ";	
     $sql .= "AND idfranges_horaries=".$idfranges_horaries." ";
	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
	 
     if ($count == 0) {
       return 0;
     }
     else {
       return $result["total"];
     }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasAlumne --> Total Incidencies d'un alumne per tipus i entre dates
	                              Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasAlumne($db,$idalumnes,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT( ia.id_tipus_incidencia ) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idalumnes = ".$idalumnes." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasAlumneGrupMateria --> Total Incidencies d'un alumne, grup i materia per tipus i entre dates
************************************************************************************************************ */
function getTotalIncidenciasAlumneGrupMateria($db,$idalumnes,$id_tipus_incidencia,$idgrups,$id_mat_uf_pla,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT( ia.id_tipus_incidencia ) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idalumnes = ".$idalumnes." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.idgrups=".$idgrups." AND ia.id_mat_uf_pla=".$id_mat_uf_pla;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasProfessor --> Total Incidencies d'un professor  per tipus i entre dates
	                                 Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasProfessor($db,$idprofessors,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT(ia.id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idprofessors = ".$idprofessors." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasGrupMateria --> Total Incidencies d'un grup i materia per tipus i entre dates
	                                            Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasGrupMateria($db,$idgrups,$id_mat_uf_pla,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT(ia.id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idgrups=".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.id_mat_uf_pla=".$id_mat_uf_pla." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */  
  
  
  
/*  ********************************************************************************************************
	getTotalIncidenciasProfessorAlumneGrupMateria --> Total Incidencies d'un professor, alumne, grup i materia per tipus i entre dates
	                                                  Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasProfessorAlumneGrupMateria($db,$idprofessors,$idalumnes,$idgrups,$id_mat_uf_pla,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT(ia.id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idprofessors = ".$idprofessors." AND ia.idalumnes=".$idalumnes." AND ia.idgrups=".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.id_mat_uf_pla=".$id_mat_uf_pla." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasProfessorGrup --> Total Incidencies d'un professor i grup per tipus i entre dates
	                                     Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasProfessorGrup($db,$idprofessors,$idgrups,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT(ia.id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idgrups=".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
     $sql .= " AND ia.id_mat_uf_pla IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
     $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
     $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
     $sql .= "WHERE pa.idprofessors='".$idprofessors."') ";
	 	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

  /*  ********************************************************************************************************
	getTotalIncidenciasProfessorGrupMateria --> Total Incidencies d'un alumne, grup i materia per tipus i entre dates
************************************************************************************************************ */
function getTotalIncidenciasProfessorGrupMateria($db,$idprofessors,$id_tipus_incidencia,$idgrups,$id_mat_uf_pla,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT( ia.id_tipus_incidencia ) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idprofessors=".$idprofessors." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.idgrups=".$idgrups." AND ia.id_mat_uf_pla=".$id_mat_uf_pla;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */
  
/*  ********************************************************************************************************
	getTotalIncidenciasProfessorAlumne --> Total Incidencies d'un professor i alumne per tipus i entre dates
	                                       Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasProfessorAlumne($db,$idprofessors,$idalumnes,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT(ia.id_tipus_incidencia) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idalumnes=".$idalumnes." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
     $sql .= " AND ia.id_mat_uf_pla IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
     $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
     $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
     $sql .= "WHERE pa.idprofessors='".$idprofessors."') ";

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalIncidenciasGrup --> Total Incidencies d'un grup per tipus i entre dates
	                            Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalIncidenciasGrup($db,$idgrups,$id_tipus_incidencia,$data_inici,$data_fi) {
     $sql  = "SELECT ia.idalumnes, ia.id_tipus_incidencia, COUNT( ia.id_tipus_incidencia ) AS total ";
     $sql .= "FROM incidencia_alumne ia ";
     $sql .= "WHERE ia.idgrups = ".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
     $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasAlumne --> Detall Incidencies d'un alumne per tipus i entre dates
	                         Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasAlumne($db,$idalumnes,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idalumnes = ".$idalumnes." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";

	 $rec = $db->query($sql);
         return $rec;	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasPlaEstudis --> Detall Incidencies d'un alumne per tipus i entre dates per pla d'estudis
	                         		   Per l'informe global
************************************************************************************************************ */
function getIncidenciasPlaEstudis($db,$idplans_estudis,$id_tipus_incidencia,$data_inici,$data_fi) {
//	 $sql  = "SELECT ia.idalumnes,COUNT(DISTINCT ia.data) AS total ";
//	 $sql .= "FROM incidencia_alumne ia ";
//	 $sql .= "INNER JOIN moduls_materies_ufs m on ia.id_mat_uf_pla=m.id_mat_uf_pla ";
//	 $sql .= "WHERE ia.id_tipus_incidencia=".$id_tipus_incidencia." AND m.idplans_estudis=".$idplans_estudis;
//	 $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
//	 $sql .= "GROUP BY ia.idalumnes";

	 $sql  = "SELECT ia.idalumnes,COUNT(DISTINCT ia.idincidencia_alumne) AS total ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN moduls_materies_ufs m on ia.id_mat_uf_pla=m.id_mat_uf_pla ";
	 $sql .= "WHERE ia.id_tipus_incidencia=".$id_tipus_incidencia." AND m.idplans_estudis=".$idplans_estudis;
	 $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= "GROUP BY ia.idalumnes";     
    
    
	 $rec = $db->query($sql);
         return $rec;
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasAlumneGrupMateria --> Detall Incidencies d'un alumne, grup i materia per tipus i entre dates
************************************************************************************************************ */
function getIncidenciasAlumneGrupMateria($db,$idalumnes,$id_tipus_incidencia,$idgrups,$id_mat_uf_pla,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idalumnes = ".$idalumnes." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.id_mat_uf_pla=".$id_mat_uf_pla;
	 $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
         
	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasGrup --> Detall Incidencies d'un grup per tipus i entre dates
	                       Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasGrup($db,$idgrups,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idgrups = ".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
         return $rec;
	
  }
/* ********************************************************************************************************* */

  /*  ********************************************************************************************************
	getIncidenciasGrupMateriasProfessor --> Detall Incidencies d'un grup per tipus i entre dates
	                                        Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasGrupMateriasProfessor($db,$idprofessors,$idgrups,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idgrups = ".$idgrups." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
         $sql .= " AND ia.id_mat_uf_pla IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
         $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
         $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
         $sql .= "WHERE pa.idprofessors='".$idprofessors."' )";
             
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
         return $rec;
  }
/* ********************************************************************************************************* */
  
/*  ********************************************************************************************************
	getIncidenciasProfessor --> Detall Incidencies d'un professor per tipus i entre dates
	                            Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasProfessor($db,$idprofessors,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idprofessors = ".$idprofessors." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
         return $rec;
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasProfessorGrup --> Detall Incidencies d'un professor i grup per tipus i entre dates
	                                Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasProfessorGrup($db,$idprofessors,$idgrups,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idprofessors = ".$idprofessors." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
	 
         return $rec;
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasGrupMateria --> Detall Incidencies d'un grup i materia per tipus i entre dates
	                                      Per informes de faltes d'assistència
************************************************************************************************************ */
function getIncidenciasGrupMateria($db,$idgrups,$id_mat_uf_pla,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.id_mat_uf_pla=".$id_mat_uf_pla." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
	 
         return $rec;
	
  }
/* ********************************************************************************************************* */  
  
/*  ********************************************************************************************************
	getIncidenciasGrupAlumne --> Detall Incidencies d'un professor, grup i alumne per tipus i entre dates
	                                      Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasGrupAlumne($db,$idgrups,$idalumnes,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.idalumnes=".$idalumnes." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
	 
         return $rec;
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasGrupAlumneMateriasProfessor --> Detall Incidencies d'un professor, grup i alumne per tipus i entre dates
	                                      Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasGrupAlumneMateriasProfessor($db,$idprofessors,$idgrups,$idalumnes,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.idalumnes=".$idalumnes." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
         $sql .= " AND ia.id_mat_uf_pla IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
         $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
         $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
         $sql .= "WHERE pa.idprofessors='".$idprofessors."' )";
         
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
	 
         return $rec;
	
  }
/* ********************************************************************************************************* */

  
/*  ********************************************************************************************************
	getIncidenciasProfessorAlumneGrupMateria --> Detall Incidencies d'un professor, grup, materia i alumne per tipus i entre dates
	                                      Per informes de faltes d'assistència 
************************************************************************************************************ */
function getIncidenciasProfessorAlumneGrupMateria($db,$idprofessors,$idalumnes,$idgrups,$id_mat_uf_pla,$id_tipus_incidencia,$data_inici,$data_fi) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idprofessors = ".$idprofessors." AND ia.id_mat_uf_pla=".$id_mat_uf_pla." AND ia.id_tipus_incidencia=".$id_tipus_incidencia;
         $sql .= " AND ia.idgrups=".$idgrups." AND ia.idalumnes=".$idalumnes." AND ia.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " ORDER BY ia.data,fh.hora_inici,ia.id_tipus_incidencia,ia.id_tipus_incident ";
	 $rec = $db->query($sql);
	 
         return $rec;
	
  }
/* ********************************************************************************************************* */  
  
/*  ********************************************************************************************************
	getIncidenciaProfessor --> Incidencia d'un profesor 
************************************************************************************************************ */
function getIncidenciaProfessor($db,$idprofessors,$data,$idfranges_horaries) {
     $sql  = "SELECT id_tipus_incidencia ";
     $sql .= "FROM incidencia_professor ";
     $sql .= "WHERE idprofessors=".$idprofessors." AND data='".$data."' ";	
     $sql .= "AND idfranges_horaries=".$idfranges_horaries." ";

     $rec = $db->query($sql);
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
     }
     //mysql_free_result($rec);
     if ($count == 0) {
	   return 0;
     }
     else {
           return $result["id_tipus_incidencia"];
     }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getIncidenciasDataFHGrupMateria --> Detall Incidencies d'una data, franja h, grup ui matèria
	                                    Per copiar assistència de franjes anteriors
************************************************************************************************************ */
function getIncidenciasDataFHGrupMateria($db,$data,$idfranges_horaries,$idgrups,$idmateria,$idprofessors) {
	 $sql  = "SELECT ia.* ";
	 $sql .= "FROM incidencia_alumne ia ";
	 $sql .= "INNER JOIN franges_horaries fh ON ia.idfranges_horaries = fh.idfranges_horaries ";
	 $sql .= "WHERE ia.idgrups = ".$idgrups." AND ia.id_mat_uf_pla=".$idmateria;
         $sql .= " AND ia.idprofessors = ".$idprofessors;
	 $sql .= " AND ia.idfranges_horaries = ".$idfranges_horaries." AND ia.data = '".$data."'";
	 $rec = $db->query($sql);
         return $rec;	
  }
/* ********************************************************************************************************* */

?>