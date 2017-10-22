<?php

/*  ********************************************************************************************************
	getIncidenciapProfessorbyID --> Incidencia d'un professor per ID
************************************************************************************************************ */
function getIncidenciapProfessorbyID($db,$idincidencia_professor) {
	 $sql  = "SELECT * ";
	 $sql .= "FROM incidencia_professor ";
	 $sql .= "WHERE idincidencia_professor=".$idincidencia_professor;	

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
	exitsIncidenciapProfessor --> Existeix incidència?
************************************************************************************************************ */
function exitsIncidenciapProfessor($db,$idprofessors,$data,$idfranges_horaries) {
	 $sql  = "SELECT * ";
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
	getIncidenciapProfessor --> Incidencia d'un professor per grup, materia i data
************************************************************************************************************ */
function getIncidenciapProfessor($db,$idprofessors,$data,$idfranges_horaries) {
	 $sql  = "SELECT * ";
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
	 return $result;
  }
/* ********************************************************************************************************* */

?>