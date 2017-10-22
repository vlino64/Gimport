<?php

/*  ********************************************************************************************************
	getDireccioDiaHoraProfessor --> Hores direcció un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getDireccioDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_direccio g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraDireccionDiaProfessor --> hora de la primera guardia d'un dia i un professor
************************************************************************************************************ */
function getPrimeraDireccionDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_direccio g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);

	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraDireccionDiaProfessor --> hora de la darrera guardia d'un dia i un professor
************************************************************************************************************ */
function getDarreraDireccionDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_direccio g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */



/*  ********************************************************************************************************
	getCoordinacioDiaHoraProfessor --> Hores coordinacio un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getCoordinacioDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_coordinacions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraCoordinacioDiaProfessor --> hora de la primera guardia d'un dia i un professor
************************************************************************************************************ */
function getPrimeraCoordinacioDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_coordinacions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraCoordinacioDiaProfessor --> hora de la darrera guardia d'un dia i un professor
************************************************************************************************************ */
function getDarreraCoordinacioDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_coordinacions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */



/*  ********************************************************************************************************
	getAtencionsDiaHoraProfessor --> Hores atencio un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getAtencionsDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_atencions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraAtencionsDiaProfessor --> hora de la primera guardia d'un dia i un professor
************************************************************************************************************ */
function getPrimeraAtencionsDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_atencions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraAtencionsDiaProfessor --> hora de la darrera guardia d'un dia i un professor
************************************************************************************************************ */
function getDarreraAtencionsDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_atencions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */



/*  ********************************************************************************************************
	getPermanenciesDiaHoraProfessor --> Hores permanencia un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getPermanenciesDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_permanencies g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraPermanenciesDiaProfessor --> hora de la primera guardia d'un dia i un professor
************************************************************************************************************ */
function getPrimeraPermanenciesDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_permanencies g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraPermanenciesDiaProfessor --> hora de la darrera guardia d'un dia i un professor
************************************************************************************************************ */
function getDarreraPermanenciesDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_permanencies g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */



/*  ********************************************************************************************************
	getReunionsDiaHoraProfessor --> Hores reunio un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getReunionsDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_reunions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraReunionsDiaProfessor --> hora de la primera guardia d'un dia i un professor
************************************************************************************************************ */
function getPrimeraReunionsDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_reunions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraReunionsDiaProfessor --> hora de la darrera guardia d'un dia i un professor
************************************************************************************************************ */
function getDarreraReunionsDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_reunions g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */


/*  ********************************************************************************************************
	getAltresDiaHoraProfessor --> Hores altres un dia, una hora, curso escolar y por un determinado profesor
************************************************************************************************************ */
function getAltresDiaHoraProfessor($db,$dia,$franja,$curs,$professor) {
     $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja AND idperiode_escolar=$curs";
     $rec = $db->query($sql);
     $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 
	 if ($count==0) {
	 	$diafranja = 0;
	 }
	 else {
	 	$diafranja = $result["id_dies_franges"];
	 }
	 
	 $sql  = "SELECT g.*,ec.descripcio AS espaicentre ";
	 $sql .= "FROM prof_altres g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges    = df.id_dies_franges ";
	 $sql .= "INNER JOIN espais_centre      ec ON  g.idespais_centre    = ec.idespais_centre ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.id_dies_franges=$diafranja";

	 $rec = $db->query($sql);
	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getPrimeraAltresDiaProfessor --> hora de la primera altres d'un dia i un professor
************************************************************************************************************ */
function getPrimeraAltresDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_inici AS hora ";
	 $sql .= "FROM prof_altres g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_inici LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getDarreraAltresDiaProfessor --> hora de la darrera altres d'un dia i un professor
************************************************************************************************************ */
function getDarreraAltresDiaProfessor($db,$dia,$curs,$professor) {
	 $sql  = "SELECT fh.hora_fi AS hora ";
	 $sql .= "FROM prof_altres g ";
	 $sql .= "INNER JOIN dies_franges       df ON  g.id_dies_franges     = df.id_dies_franges ";
	 $sql .= "INNER JOIN franges_horaries   fh ON  fh.idfranges_horaries = df.idfranges_horaries ";
	 $sql .= "WHERE df.idperiode_escolar=$curs AND g.idprofessors=$professor AND df.iddies_setmana=$dia";
	 $sql .= " ORDER BY fh.hora_fi DESC LIMIT 0,1";

	 $rec = $db->query($sql);
     
	 $count = 0;
	 $result = "";
	 foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	 }
	 //mysql_free_result($rec);
	 if ($count==0) {
	 	 return '00:00';
	 }
	 else {
		 return substr($result["hora"],0,5);
	 }
  }
/* ********************************************************************************************************* */

?>