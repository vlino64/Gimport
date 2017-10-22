<?php

    // Comprova hora entrada altres
	// Veure si abans hi ha una hora de guàrdia
	$hora_entrada_altres = getPrimeraGuardiaDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	// Veure si abans hi ha una hora de direcció
	$hora_entrada_altres = getPrimeraDireccionDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	// Veure si abans hi ha una hora de coordinació
	$hora_entrada_altres = getPrimeraCoordinacioDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	// Veure si abans hi ha una hora d'atencions
	$hora_entrada_altres = getPrimeraAtencionsDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	// Veure si abans hi ha una hora de permanència
	$hora_entrada_altres = getPrimeraPermanenciesDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	// Veure si abans hi ha una hora de reunió
	$hora_entrada_altres = getPrimeraReunionsDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
        
        // Veure si abans hi ha una hora de reunió d'altres
	$hora_entrada_altres = getPrimeraAltresDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_entrada_altres != '00:00') && ($hora_entrada == '00:00'))  {
		$hora_entrada = $hora_entrada_altres;
	}
	else if (($hora_entrada_altres != '00:00') && ($hora_entrada != '00:00') && ($hora_entrada_altres < $hora_entrada))  {
		$hora_entrada = $hora_entrada_altres;
	}
	
	
		
	// Comprova hora sortida altres
	// Veure si després hi ha una hora de guàrdia
	$hora_sortida_altres = getDarreraGuardiaDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
	
	// Veure si després hi ha una hora de direcció
	$hora_sortida_altres = getDarreraDireccionDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
	
	// Veure si després hi ha una hora de coordinació
	$hora_sortida_altres = getDarreraCoordinacioDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
	
	// Veure si després hi ha una hora d'atencions
	$hora_sortida_altres = getDarreraAtencionsDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
	
	// Veure si després hi ha una hora de permanència
	$hora_sortida_altres = getDarreraPermanenciesDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
	
	// Veure si després hi ha una hora de reunió
	$hora_sortida_altres = getDarreraReunionsDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
        
        // Veure si després hi ha una hora d'e reunió d'altres
	$hora_sortida_altres = getDarreraAltresDiaProfessor($db,$dia_setmana,$curs_escolar,$row["idprofessors"]);
	if (($hora_sortida_altres != '00:00') && ($hora_sortida == '00:00'))  {
		$hora_sortida = $hora_sortida_altres;
	}
	else if (($hora_sortida_altres != '00:00') && ($hora_sortida != '00:00') && ($hora_sortida_altres > $hora_sortida))  {
		$hora_sortida = $hora_sortida_altres;
	}
?>