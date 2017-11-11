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


function neteja($taula,$db)

	{
	$result = $db->query("SHOW TABLES LIKE '".$taula."'");
        $tableExists = $result->rowCount() > 0;
        if ($tableExists)
            {
            $sql="DELETE FROM ".$taula.";";
            $result=$db->query($sql);
            //echo "Esborrada taula ".$taula."<br>";;
            }
        }


////**********************************************************************
////		 Realitza la neteja de les taules prèvis a la càrrega per eliminar valors anteriors
////**********************************************************************


function buidatge($modalitat,$db)

	{
	switch ($modalitat)
		{
		case "desdediesfrangesespais":
			neteja('guardies',$db);
			neteja('unitats_classe',$db);
			neteja('dies_franges',$db);
			neteja('franges_horaries',$db);
                        neteja('franges_tmp',$db);
			neteja('dies_setmana',$db);
			neteja('espais_centre',$db);
			break;

		case "desdecreahoraris":
			neteja('grups_materies',$db);
			neteja('unitats_classe',$db);
			neteja('guardies',$db);
			break;		
		
		case "desdeassignacioalumnes":
			neteja('alumnes_grup_materia',$db);
			break;		
	
		case "desdemateries":
			neteja('prof_agrupament',$db);
			neteja('alumnes_grup_materia',$db);
			neteja('unitats_classe',$db);
			neteja('grups_materies',$db);
			neteja('materia',$db);
			neteja('moduls_ufs',$db);
			neteja('moduls',$db);
			neteja('unitats_formatives',$db);
			neteja('moduls_materies_ufs',$db);
			//neteja('plans_estudis');
			break;
	
		case "desdegrups":
			neteja('prof_agrupament',$db);
			neteja('alumnes_grup_materia',$db);
			neteja('guardies',$db);
			neteja('unitats_classe',$db);
			neteja('grups_materies',$db);
			neteja('grups',$db);
			break;

		case "materies_ufs":
                        neteja('materia',$db);
			neteja('moduls_ufs',$db);
			neteja('moduls',$db);
			neteja('unitats_formatives',$db);
			neteja('moduls_materies_ufs',$db);

			break;                    

                case "ufs_mantenint_materies":
			neteja('moduls_ufs',$db);
			neteja('moduls',$db);
                        
			$sql="SELECT idunitats_formatives FROM unitats_formatives;";
                        $result=$db->query($sql);
			foreach ($result->fetchAll() as $fila){
                            $sql="SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE id_mat_uf_pla = ".$fila[0].";";
                            $result=$db->query($sql);
                            $present = $result->rowCount();
                            if ($present > 0){
                                $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_mat_uf_pla = ".$fila[0].";";
                                $result2=$db->query($sql2);
                                foreach ($result2->fetchAll() as $fila2){
                                    $sql3 = "DELETE FROM alumnes_grup_materia WHERE idgrups_materies = $fila2[0];"; 
                                    $result3=$db->query($sql3);
                                    
                                    $sql3 = "DELETE FROM prof_agrupament WHERE idagrups_materies = $fila2[0];"; 
                                    $result3=$db->query($sql3);

                                    $sql3 = "DELETE FROM grups_materies WHERE idgrups_materies = $fila2[0];"; 
                                    $result3=$db->query($sql3);
                                    
                                }                                
                            }
                            $sql3 = "DELETE FROM moduls_materies_ufs WHERE id_mat_uf_pla = $fila[0];"; 
                            $result3=$db->query($sql3);
                            
                        }                    
                        neteja('unitats_formatives',$db);
                        break;
                    
                    
                case "materies":
			$sql="SELECT idmateria FROM materia;";
                        $result=$db->query($sql);
			foreach ($result->fetchAll() as $fila){
                            $sql2 = "DELETE FROM moduls_materies_ufs WHERE id_mat_uf_pla = ".$fila[0].";";
                            $result2=$db->query($sql2);
                            $sql2 = "DELETE FROM materia WHERE idmateria = ".$fila[0].";";
                            $result2=$db->query($sql2);
                        }
                        break;
                        
		case "total":
			neteja('professor_carrec',$db);
			neteja('prof_agrupament',$db);
			neteja('guardies',$db);
			neteja('unitats_classe',$db);
			neteja('dies_franges',$db);
			neteja('dies_setmana',$db);
			neteja('franges_horaries',$db);
                        neteja('franges_tmp',$db);
			neteja('espais_centre',$db);	
			neteja('materia',$db);
                        neteja('moduls_ufs',$db);
                        neteja('moduls',$db);
                        neteja('unitats_formatives',$db);
                        neteja('alumnes_grup_materia',$db);
			neteja('grups_materies',$db);
			neteja('moduls_materies_ufs',$db);
			neteja('grups',$db);
			neteja('contacte_alumne',$db);
			neteja('alumnes_families',$db);
			neteja('contacte_families',$db);
			neteja('families',$db);
                        neteja('ccc_taula_principal',$db);
			neteja('alumnes',$db);
			neteja('contacte_professor',$db);
			neteja('professors',$db);
			neteja('equivalencies',$db);
                        neteja('plans_estudis',$db);
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

function _extreu_id($taula,$camp,$id,$codi)
	{
	$sql="SELECT ".$id." FROM ".$taula." WHERE ".$camp."='".$codi."';";
//	echo "extreu ".$sql."<br>";
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

function recuperacampdedades($camps,$db)

	{
	$sql ="SELECT Nom_info_contacte,idtipus_contacte FROM `tipus_contacte`;";
	$result = $db->query($sql);
        if (!$result) {die(_ERR_SELECT_DATA_TYPE. mysql_error());} 
	foreach($result->fetchAll() as $fila) {
		
		$camps[$fila['Nom_info_contacte']]=$fila['idtipus_contacte'];
		//echo $fila[0].$fila[1]."---".$camps[$fila[0]]."<br>";
		}
	
        return $camps;        
        }
	

function _copia_bd($user,$pass,$basedd)
	{
	$filename = "bk_".$basedd."_".date("d-m-Y_H-i-s").".sql";
	$mime = "application/x-gzip";

	header( "Content-Type: " . $mime );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	$cmd = "mysqldump -u $user --password=$pass $basedd";   

	passthru( $cmd );
	return;

	}


function _data_correcta($data_fi)
	
	{
	$data=explode("-",$data_fi);
	if (checkdate($data[1],$data[2],$data[0])) {return 1;}
	else {return 0;}
	
	}

function _neteja_apostrofs(&$cadena)

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

function extreu_fase($camp,$db)
	{
		
	$sql00="SELECT estat FROM fases WHERE fase='".$camp."';"; 
	$result00=$db->query($sql00);
        foreach ($result00->fetchAll() as $fila) {$estat=  $fila['estat'];}
        return $estat;
        
	}

function introduir_fase($camp,$valor,$db)
	{
	$sql01="UPDATE fases SET estat='".$valor."' WHERE fase='".$camp."';"; 
	//echo "<br>".$sql01;
	$result01 = $db->query($sql01);
	}


function nova_taula_fases($db)
	{
	
	
	$sql="DROP TABLE IF EXISTS fases";
	$result = $db->query($sql);
	
        $sql="CREATE TABLE IF NOT EXISTS `fases` (";
        $sql.="`id` int(11) NOT NULL AUTO_INCREMENT,";
        $sql.="`fase` varchar(50) NOT NULL,";
        $sql.="`estat` int(11) NOT NULL,";
        $sql.="`comentaris` varchar(100) NOT NULL,";
        $sql.="PRIMARY KEY (`id`)";
        $sql.=") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$result = $db->query($sql);
	
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
	$result = $db->query($sql);
	}

function nova_taula_equivalencies($db)
	{
		
	$sql="DROP TABLE IF EXISTS equivalencies";
	$result = $db->query($sql);
	
	$sql="DROP TABLE IF EXISTS grups_tmp";
	$result = $db->query($sql);
	
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
	$result = $db->query($sql);
	
	}   
   
   
   
   
   ?>


