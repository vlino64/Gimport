<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:funcions_generals.php
* Autor: Víctor Lino
* Descripció: Funcions generals vinculades al proccés de càrrega
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/


//**********************************************************************
//		 Realitza la neteja de les taules prèvis a la càrrega per eliminar valors anteriors
//**********************************************************************


function neteja($taula)

	{
	$result = mysql_query("SHOW TABLES LIKE '".$taula."'");
        $tableExists = mysql_num_rows($result) > 0;
        if ($tableExists)
            {
            $sql="DELETE FROM ".$taula.";";
            $result=mysql_query($sql);
            if (!$result) 
		{
		die(_ERR_DELETE_TABLE." > ".$taula." > ".mysql_error());
		}
            //echo "Esborrada taula ".$taula."<br>";;
            }
        }


////**********************************************************************
////		 Realitza la neteja de les taules prèvis a la càrrega per eliminar valors anteriors
////**********************************************************************


function buidatge($modalitat)

	{
	switch ($modalitat)
		{
		case "desdediesfrangesespais":
			neteja('guardies');
			neteja('unitats_classe');
			neteja('dies_franges');
			neteja('franges_horaries');
                        neteja('franges_tmp');
			neteja('dies_setmana');
			neteja('espais_centre');
			break;

		case "desdecreahoraris":
			neteja('grups_materies');
			neteja('unitats_classe');
			neteja('guardies');
			break;		
		
		case "desdeassignacioalumnes":
			neteja('alumnes_grup_materia');
			break;		
	
		case "desdemateries":
			neteja('prof_agrupament');
			neteja('alumnes_grup_materia');
			neteja('unitats_classe');
			neteja('grups_materies');
			neteja('materia');
			neteja('moduls_ufs');
			neteja('moduls');
			neteja('unitats_formatives');
			neteja('moduls_materies_ufs');
			//neteja('plans_estudis');
			break;
	
		case "desdegrups":
			neteja('prof_agrupament');
			neteja('alumnes_grup_materia');
			neteja('guardies');
			neteja('unitats_classe');
			neteja('grups_materies');
			neteja('materia');
			neteja('moduls_ufs');
			neteja('moduls');
			neteja('unitats_formatives');
			neteja('moduls_materies_ufs');
			//neteja('plans_estudis');
			neteja('grups');
			break;
		
		case "total":
			neteja('professor_carrec');
			neteja('prof_agrupament');
			neteja('guardies');
			neteja('unitats_classe');
			neteja('dies_franges');
			neteja('dies_setmana');
			neteja('franges_horaries');
                        neteja('franges_tmp');
			neteja('espais_centre');	
			neteja('materia');
                        neteja('moduls_ufs');
                        neteja('moduls');
                        neteja('unitats_formatives');
                        neteja('alumnes_grup_materia');
			neteja('grups_materies');
			neteja('moduls_materies_ufs');
			neteja('grups');
			neteja('contacte_alumne');
			neteja('alumnes_families');
			neteja('contacte_families');
			neteja('families');
			neteja('alumnes');
			neteja('contacte_professor');
			neteja('professors');
			neteja('equivalencies');
                        neteja('plans_estudis');
			break;
			
		}
	
	}


////**********************************************************************
////  	Extreu l'id-PK d'una taula
////**********************************************************************
//
//// $taula -> Taula a la que fer la consulta
//// $camps -> Camp on fer la cerca
//// $id -> Camp a extreure	
//// $codi -> Valor qeu s'ha de cerca al camp per fer la cerca

function extreu_id($taula,$camp,$id,$codi)
	{
	$sql="SELECT ".$id." FROM ".$taula." WHERE ".$camp."='".$codi."';";
	//echo "extreu ".$sql."<br>";
	$result=mysql_query($sql);
	if (!$result) 
		{
		die(_ERR_SELECT_ID . mysql_error());
		}
	$fila=  mysql_fetch_row($result);
	return $fila[0];
	}

////**********************************************************************
////				Carrega tipus de dades peersonals
////				Assigna a una rray associatiu el nom del camp d'informació 
////				amb el seu codi per facilitar consultes
////**********************************************************************

function recuperacampdedades(&$camps)

	{
//	include("../config.php");
	
	$sql ="SELECT Nom_info_contacte,idtipus_contacte FROM `tipus_contacte`;";
	$result = mysql_query($sql);
	if (!$result) 
     	{
		die(_ERR_SELECT_DATA_TYPE. mysql_error());
		} 
	while ($fila=mysql_fetch_row($result))
		{
		$camps[$fila[0]]=$fila[1];
		//echo $fila[0].$fila[1]."---".$camps[$fila[0]]."<br>";
		}
	}
	

function munta_el_config()
	{
	include("../../bbdd/connect.php");
   	
	$file = fopen("../config.php","w")or die("can't open file");
	fclose($file);
	
	$file = fopen("../config.php","w")or die("can't open file");
	fwrite($file,"<?php\n");
	fwrite($file,"// Aquest fitxer s'ha generat automàticament\n");
	fwrite($file,"// Els canvis que puguis introduir, es perdran en regenerar-se\n");
	fwrite($file,"// ==========================================================\n");
	fwrite($file,"          \n");
   fwrite($file,"          \n");
   fwrite($file,"\$connexio = @mysql_connect('localhost','".DB_USER."','".DB_PASS."');\n");
   fwrite($file,"if (!\$connexio) {\n");
	fwrite($file,"die('Could not connect: ' . mysql_error());}\n");
   fwrite($file,"mysql_select_db('".DB_NAME."', \$connexio);\n");
   fwrite($file,"mysql_set_charset(\"utf8\");\n");
   fwrite($file,"?>");
	fclose($file);
	}

function copia_bd($user,$pass,$basedd)
	{
	$filename = "bk_".$basedd."_".date("d-m-Y_H-i-s").".sql";
	$mime = "application/x-gzip";

	header( "Content-Type: " . $mime );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	$cmd = "mysqldump -u $user --password=$pass $basedd";   

	passthru( $cmd );
	return;

	}


function data_correcta($data_fi)
	
	{
	$data=explode("-",$data_fi);
	if (checkdate($data[1],$data[2],$data[0])) {return 1;}
	else {return 0;}
	
	}

function neteja_apostrofs(&$cadena)

	{
	$elements = array("`", "'");
	$cadena = str_replace($elements, " ", $cadena);
	return $cadena;
	}
//
//
//// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2
////				 FUNCIONS DESTINADES AL GESTIÓ DE LES FASES DEL PROCÈS
//// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function extreu_fase($camp)
	{
		
	$sql00="SELECT estat FROM fases WHERE fase='".$camp."';"; 
	$result00=mysql_query($sql00);
        if (!$result00) {die(_ERR_EXTRO_FASE . mysql_error());}
	$estat=  mysql_fetch_row($result00);
        return $estat[0];
        
	}

function introduir_fase($camp,$valor)
	{
	
	$sql01="UPDATE fases SET estat='".$valor."' WHERE fase='".$camp."';"; 
	//echo "<br>".$sql01;
	$result01=mysql_query($sql01);
	if (!$result01) {die(_ERR_INTRO_FASE . mysql_error());}
	}


function nova_taula_fases()
	{
	
	
	$sql="DROP TABLE IF EXISTS fases";
	$result=mysql_query($sql);
	
   $sql="CREATE TABLE IF NOT EXISTS `fases` (";
   $sql.="`id` int(11) NOT NULL AUTO_INCREMENT,";
   $sql.="`fase` varchar(50) NOT NULL,";
   $sql.="`estat` int(11) NOT NULL,";
   $sql.="`comentaris` varchar(100) NOT NULL,";
   $sql.="PRIMARY KEY (`id`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$result=mysql_query($sql);
	
	$sql="INSERT INTO `fases` (`id`, `fase`, `estat`,`comentaris`) VALUES ";
	$sql.="(1, 'geisoft', 0,'Utilitzem o no geisoft'),";
	$sql.="(2, 'sincro', 0,'Està sincronitzada la gestió centralitzada i gassist '),";
	$sql.="(3, 'carrega', 0,'0:primera absoluta;1:primera relativa o successives;2:continuar prèvia;3- actualització'),";
	$sql.="(4,'aprofitar_saga',0,'Aprofites el fitxer de saga'),";
	$sql.="(5,'aprofitar_horaris',0,'Aprofites el fitxer d horaris ho'),";
	$sql.="(6,'segona_carrega',0,'És una segona càrrega o posterior'),";
	$sql.="(7,'modalitat_fitxer',0,'0:ESO/BAT/CAS; 1:CCFF; 2:DUAL'),";
	$sql.="(8,'app_horaris',0,'0: gpuntis,1:ghc peñalara;2:Kronowin;3:HorW (Sevilla);4: cap aplicacio'),";
	$sql.="(9, 'professorat', 0,'professorat'),";
	$sql.="(10, 'alumnat', 0,'alumnat'),";
	$sql.="(11, 'grups', 0,'grups'),";
	$sql.="(12, 'alumne_grups', 0,''),";
	$sql.="(13, 'grups_sense_amteries', 0,'Grups sense matèries. Càrrega només SAGA'),";
	$sql.="(14, 'materies', 0,'materies'),";
	$sql.="(16, 'dies_espais_franges', 0,'dies, espais i franges'),";
	$sql.="(17, 'dies_setmana', 0,'Dies de la setmana'),";
	$sql.="(18, 'franges', 0,'Franges horàries'),";
	$sql.="(19, 'espais', 0,'espais de centre'),";
	$sql.="(20, 'lessons', 0,'Horaris'),";
	$sql.="(21, 'historic', 0,'Gestió de l històric'),";
	$sql.="(22, 'assig_alumnes',0,'Assignació d alumnes a grups/matèries');";
	
	//echo $sql;
	$result=mysql_query($sql);
	}

function nova_taula_equivalencies()
	{
		
	$sql="DROP TABLE IF EXISTS equivalencies";
	$result=mysql_query($sql);
	
	$sql="DROP TABLE IF EXISTS grups_tmp";
	$result=mysql_query($sql);
	
	$sql="CREATE TABLE IF NOT EXISTS `equivalencies` (  `id` int(11) NOT NULL AUTO_INCREMENT, ";
	$sql.="`grup_gp` varchar(100) DEFAULT NULL,";
	$sql.="`grup_ga` varchar(60) DEFAULT NULL,";
	$sql.="`grup_saga` varchar(60) DEFAULT NULL,";
	$sql.="`prof_gp` varchar(60) DEFAULT NULL,";
	$sql.="`prof_ga` varchar(60) DEFAULT NULL,";
	$sql.="`nom_prof_gp` varchar(60) DEFAULT NULL,";
	$sql.="`codi_prof_gp` varchar(60) DEFAULT NULL,";
	$sql.="`pla_saga` varchar(60) DEFAULT NULL,";
	$sql.="`materia_saga` varchar(60) DEFAULT NULL,";
	$sql.="`materia_gp` varchar(60) DEFAULT NULL,";
	$sql.="`altres` varchar(60) DEFAULT NULL,";
	$sql.="PRIMARY KEY (`id`)";
	$sql.=") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$result=mysql_query($sql);
	if (!$result) {die(_ERR_NEW_TABLE_EQUIV . mysql_error());}
	
	}   
   
   
   
   
   ?>


