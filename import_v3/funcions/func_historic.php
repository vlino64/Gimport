<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:funcions_saga.php
* Autor: Víctor Lino
* Descripció: Funcions relacionades amb tasques de gestió d'històric
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
function genera_historic()
	{
		
	include("../config.php");
	
	// Crea la taula d'històrics de faltes d'alumnes si no existeix
	$sql="CREATE TABLE IF NOT EXISTS `HIST_faltes_alumnes` ( `id_hist_alumnes` int(11) NOT NULL AUTO_INCREMENT, `id_alumne` int(11) NOT NULL,  `id_professor` int(11) NOT NULL, ";
	$sql.="  `dia` date NOT NULL,  `franja` varchar(20) NOT NULL,  `materia` varchar(60) NOT NULL,  `grup_curs` varchar(50) NOT NULL, ";
	$sql.="  `tipus_falta` varchar(50) NOT NULL,  `comentari` varchar(1545) NOT NULL, PRIMARY KEY (`id_hist_alumnes`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	$result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST1 . mysql_error());}	
	
	//Crea la taula que ha de contenir les dates en les que s'han fet les exportacions
	$sql="CREATE TABLE IF NOT EXISTS `HIST_dates` (`idhist_dates` int(11) NOT NULL AUTO_INCREMENT,  `Dates_historics` date NOT NULL,PRIMARY KEY (`idhist_dates`))ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	$result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST2 . mysql_error());}	
	
   // Crea la taula d'històrics de faltes d'alumnes si no existeix
   $sql="CREATE TABLE IF NOT EXISTS `HIST_CCC` ( ";
   $sql.="`idccc` int(11) NOT NULL AUTO_INCREMENT, ";
   $sql.="`alumne` int(11) DEFAULT NULL, ";
   $sql.="`grup` varchar(80) NOT NULL DEFAULT '0', ";
   $sql.="`professor` int(11) DEFAULT NULL, ";
   $sql.="`materia` varchar(80) NOT NULL, ";
   $sql.="`motiu` varchar(180) NOT NULL, ";
   $sql.="`data` date DEFAULT NULL, ";
   $sql.="`descripcio_breu` varchar(180) COLLATE utf8_bin DEFAULT NULL, ";
   $sql.="`descripcio_detallada` longtext COLLATE utf8_bin, ";
   $sql.="`data_inici_sancio` date DEFAULT NULL, ";
   $sql.="`data_fi_sancio` date DEFAULT NULL, ";
   $sql.="  PRIMARY KEY (`idccc`), ";
   $sql.="  KEY `fk_ccc_taula_principal_1_idx` (`alumne`), ";
   $sql.="  KEY `fk_ccc_taula_principal_2_idx` (`professor`) ";
   $sql.=" ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=210 ; ";
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST2 . mysql_error());}	

   $sql="CREATE TABLE IF NOT EXISTS `HIST_grups_sortides` (";
   $sql.="  `id_sortida` int(11) NOT NULL,";
   $sql.="  `id_grup` int(11) NOT NULL,";
   $sql.="  PRIMARY KEY (`id_sortida`,`id_grup`),";
   $sql.="  KEY `fk_grups_sortides_1` (`id_grup`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST3 . mysql_error());}	
   
   $sql="CREATE TABLE IF NOT EXISTS `HIST_sortides` (";
   $sql.="  `idsortides` int(11) NOT NULL ,";
   $sql.="  `data_inici` date DEFAULT NULL,";
   $sql.="  `data_fi` date DEFAULT NULL,";
   $sql.="  `hora_inici` time DEFAULT NULL,";
   $sql.="  `hora_fi` time DEFAULT NULL,";
   $sql.="  `lloc` varchar(55) COLLATE utf8_bin NOT NULL,";
   $sql.="  `descripcio` varchar(256) COLLATE utf8_bin NOT NULL,";
   $sql.="  `tancada` varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 'N',";
   $sql.="  PRIMARY KEY (`idsortides`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ; ";  
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST4 . mysql_error());}	

   $sql="CREATE TABLE IF NOT EXISTS `HIST_sortides_alumne` (";
   $sql.="  `idsortides_alumne` int(11) NOT NULL,";
   $sql.="  `id_sortida` int(11) NOT NULL,";
   $sql.="  `id_alumne` int(11) NOT NULL,";
   $sql.="  PRIMARY KEY (`idsortides_alumne`),";
   $sql.="  KEY `alumnes_sortida` (`id_alumne`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST5 . mysql_error());}	

   $sql="CREATE TABLE IF NOT EXISTS `HIST_sortides_professor` (";
   $sql.="  `idprofessorat_sortides` int(11) NOT NULL,";
   $sql.="  `id_sortida` int(11) NOT NULL,";
   $sql.="  `id_professorat` int(11) NOT NULL,";
   $sql.="  `responsable` varchar(1) COLLATE utf8_bin NOT NULL DEFAULT 'N',";
   $sql.="  PRIMARY KEY (`idprofessorat_sortides`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST6 . mysql_error());}	

   $sql="CREATE TABLE IF NOT EXISTS `HIST_incidencia_professor` (";
   $sql.="  `idincidencia_professor` int(11) NOT NULL,";
   $sql.="  `idprofessors` int(11) NOT NULL,";
   $sql.="  `grup` varchar(80) NOT NULL,";
   $sql.="  `mat_uf_pla` varchar(80) NOT NULL,";
   $sql.="  `tipus_incidencia` varchar(50) DEFAULT NULL,";
   $sql.="  `data` date DEFAULT NULL,";
   $sql.="  `comentari` varchar(1545) DEFAULT NULL,";
   $sql.="  `franges_horaries` varchar(25) DEFAULT NULL, ";
   $sql.="  PRIMARY KEY (`idincidencia_professor`)";
   $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "; 
	//echo $sql;
   $result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST7 . mysql_error());}	
   
	$sql="SELECT COUNT(*) FROM HIST_dates ";
	//echo $sql;
	$result=mysql_query($sql);	
	if (!$result) 	{die(_ERR_CREATE_TABLE_HIST2 . mysql_error());}
	if (mysql_result($result,0)=="0") {$darrera_data="";$nova_data=date("j-m-y");}
	else 
		{
		$sql="SELECT MAX(Dates_historics) FROM HIST_dates;";
		$result=mysql_query($sql);	
		if (!$result) 	{die(_ERR_CREATE_TABLE_HIST2 . mysql_error());}
		else 
			{
			$darrera_data=mysql_result($result,0);
			$darrera_data = date("d-m-y", strtotime($darrera_data));
			$nova_data=date("j-m-y");
			}
		}
	print("<font color=\"white\"><b>Genera informació histórica</b></font><br><sub>Pots modificar la data de finalització. Has de mantenir el format dd-mm-aa.</sub>");
	print("<form method=\"post\" action=\"../Historic/genera_hist_act.php\" enctype=\"multipart/form-data\" id=\"profform\">");
	if ($darrera_data=="") {print("<sub>És la primera vegada que generes històric. Generarà l'històric des de l'inici fins la data que tu marquis</sub>");}
	print("<table><tr>");
	print("<td><input type=\"text\" name=\"data_inici\" VALUE=\"".$darrera_data."\" SIZE=\"8\" READONLY>&nbsp;&nbsp;>>>&nbsp;&nbsp;");
	print("<input type=\"text\" name=\"data_fi\" VALUE=\"".$nova_data."\" SIZE=\"8\" >&nbsp;dd-mm-aa</td>");
	print("</tr><tr><td><sub><sub>La data inicial no es pot modificar. És la data en la que es va fer el darrer traspàs d'informació.<br>");
	print("<b>Si és tracta del primer traspàs apareix en blanc</b></sub></sub></td>");
	print("<tr><td><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Genera històric\"></td></tr>");
	print("</tr></table>");
	print("</form>");
	}

   
function hist_abs_ret_inc($databd_inici,$databd_fi,$nova_data)
{
   //*******************************************      
//Traspassem absències, retards i incidents
//*******************************************
      
      $myFile = "../uploads/historics_".$nova_data.".csv";
      $fh = fopen($myFile, 'w') or die("can't open file");
      $stringData="id_alumne,id_professor,dia,franja,materia,grup_curs,tipus_falta,comentari\n";		
      fwrite($fh, $stringData);
            
		$sql="SELECT  `idprofessors`, `id_tipus_incidencia`, `data`, `comentari`, `idfranges_horaries`, `idgrups`, `idalumnes`, `id_mat_uf_pla` ";
		$sql.="FROM `incidencia_alumne` WHERE data BETWEEN '".$databd_inici."' AND '".$databd_fi."' ;";
		//echo $sql;
		$result=mysql_query($sql);	
		if (!$result) 	{die(_ERR_SELECT_INCIDENCIES_ALUMNES . mysql_error());}
		while ($fila=mysql_fetch_row($result))
			{
			$professor=$fila[0]; //echo $professor;echo "<br>";
			// Genera tipus falta
			$sql2="SELECT tipus_falta FROM tipus_falta_alumne WHERE idtipus_falta_alumne='".$fila[1]."';";
			$result2=mysql_query($sql2);
			if (!$result) 	{die(_ERR_SELECT_TIPUS_INCIDENCIES . mysql_error());}
			$tipus_falta=mysql_result($result2,0);//echo $tipus_falta;echo "<br>";
			//
			$data=$fila[2]; //echo $data;	echo "<br>";
			$comentari=$fila[3];
			$comentari=neteja_apostrofs($comentari);
			// Genera franja
			$sql2="SELECT hora_inici,hora_fi FROM franges_horaries WHERE idfranges_horaries='".$fila[4]."';";
			$result2=mysql_query($sql2);
			if (!$result) 	{die(_ERR_SELECT_FRANGES . mysql_error());}
			$franja=mysql_fetch_row($result2);
			$franja_horaria=$franja[0]."-".$franja[1];//echo $franja_horaria;echo "<br>";			
			//
			// Genera grups
			$sql2="SELECT nom FROM grups WHERE idgrups='".$fila[5]."';";
			$result2=mysql_query($sql2);
			if (!$result) 	{die(_ERR_SELECT_GRUPS . mysql_error());}
			$grup=mysql_result($result2,0); //echo $grup;echo "<br>";
			//
			$alumne=$fila[6];// echo $alumne;echo "<br>";
			// Genera materia
			$sql2="SELECT nom_materia FROM materia WHERE idmateria='".$fila[7]."';";
			//echo $sql2;echo "<br>";
			$result2=mysql_query($sql2);
			if (!$result) 	{die(_ERR_SELECT_MATERIA . mysql_error());}
			if (mysql_num_rows($result2)==1) {$materia=mysql_result($result2,0);}
			else
				{
				$sql2="SELECT nom_uf FROM unitats_formatives WHERE idunitats_formatives='".$fila[7]."';";
				//echo $sql2;echo ">>>><br>";
				$result2=mysql_query($sql2);
				if (!$result) 	{die(_ERR_SELECT_UF . mysql_error());}
				$materia=mysql_result($result2,0); //echo $materia;echo "<br>";
				}
			//	Inserim en la taula d'històrics
			$sql2="INSERT INTO `HIST_faltes_alumnes`(`id_alumne`, `id_professor`, `dia`, `franja`, `materia`, `grup_curs`, `tipus_falta`, `comentari`) ";
			$sql2.="VALUES ('".$alumne."','".$professor."','".$data."','".$franja_horaria."','".$materia."','".$grup."','".$tipus_falta."','".$comentari."')";
			$result2=mysql_query($sql2);
			if (!$result2) 	{die(_ERR_INSERT_HIST_ALUMNE . mysql_error());}
			$stringData=$alumne.",".$professor.",".$data.",".$franja_horaria.",".$materia.",".$grup.",".$tipus_falta.",".$comentari."\n";
			fwrite($fh, $stringData);

			}
		fclose($fh);

		$sql="DELETE FROM `incidencia_alumne` WHERE data BETWEEN '".$databd_inici."' AND '".$databd_fi."' ;";
		//echo $sql;
		$result=mysql_query($sql);	
		if (!$result) 	{die(_ERR_SELECT_INCIDENCIES_ALUMNES . mysql_error());}

}   

function hist_ccc($databd_inici,$databd_fi,$nova_data)
{
//*******************************************
//Traspassem CCC
//*******************************************
//      
      // Comprovem si s'utilitza el módul de CCC
      if (file_exists('../../tutoria/ccc'))
         {
         //print('El módul de ccc SI s\'utilitza');
         $myFile = "../uploads/CCC_".$nova_data.".csv";
         $fh = fopen($myFile, 'w') or die("can't open file");
         $stringData="id_alumne,id_professor,dia,franja,materia,grup_curs,tipus_falta,comentari\n";		
         fwrite($fh, $stringData);

         $sql="SELECT * ";
         $sql.="FROM `ccc_taula_principal` WHERE data BETWEEN '".$databd_inici."' AND '".$databd_fi."' ;";
         //echo $sql;
         $result=mysql_query($sql);	
         if (!$result) 	{die(_ERR_SELECT_INCIDENCIES_ALUMNES . mysql_error());}
         while ($fila=mysql_fetch_row($result))
            {
            $professor=$fila[3]; //echo $professor;echo "<br>";
            // Genera tipus falta
            $sql2="SELECT nom_motiu FROM ccc_motius WHERE idccc_motius='".$fila[8]."';";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_TIPUS_INCIDENCIES . mysql_error());}
            $motiu=mysql_result($result2,0);//echo $tipus_falta;echo "<br>";
            $motiu=neteja_apostrofs($motiu);

            $data=$fila[9]; //echo $data;	echo "<br>";
            $inici_sancio=$fila[15]; //echo $inici_sancio;	echo "<br>";
            if ($inici_sancio == '0000-00-00') {$inici_sancio="1970-01-01";}
            $fi_sancio=$fila[16]; //echo $fi_sancio;	echo "<br>";
            if ($fi_sancio == '0000-00-00') {$fi_sancio="1970-01-01";}

            $comentari=$fila[11];
            $comentari=neteja_apostrofs($comentari);

            $comentari_llarg=$fila[12];
            $comentari_llarg=neteja_apostrofs($comentari_llarg);

            // Genera grups
            $sql2="SELECT nom FROM grups WHERE idgrups='".$fila[2]."';";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_GRUPS . mysql_error());}
            $grup=mysql_result($result2,0); //echo $grup;echo "<br>";
            //
            $alumne=$fila[1];// echo $alumne;echo "<br>";
            // Genera materia
            $sql2="SELECT nom_materia FROM materia WHERE idmateria='".$fila[4]."';";
            //echo $sql2;echo "<br>";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_MATERIA . mysql_error());}
            if (mysql_num_rows($result2)==1) {$materia=mysql_result($result2,0);}
            else
               {
               $sql2="SELECT nom_uf FROM unitats_formatives WHERE idunitats_formatives='".$fila[7]."';";
               //echo $sql2;echo ">>>><br>";
               $result2=mysql_query($sql2);
               if (!$result) 	{die(_ERR_SELECT_UF . mysql_error());}
               $materia=mysql_result($result2,0); //echo $materia;echo "<br>";
               }
            //	Inserim en la taula d'històrics
            $sql2="INSERT INTO `HIST_CCC`(`alumne`,`grup`, `professor`, `data`, `materia`, `data_inici_sancio`, `data_fi_sancio`, `motiu`, `descripcio_breu`, `descripcio_detallada`) ";
            $sql2.="VALUES ('".$alumne."','".$grup."','".$professor."','".$data."','".$materia."','".$inici_sancio."','".$fi_sancio."','".$motiu."','".$comentari."','".$comentari_llarg."')";
            //echo "<br>".$sql2;
            //echo "<br>".$alumne."','".$grup."','".$professor."','".$data."','".$materia."','".$inici_sancio."','".$fi_sancio."','".$motiu."','".$comentari."','".$comentari_llarg;
            $result2=mysql_query($sql2);
            if (!$result2) 	{die(_ERR_INSERT_HIST_ALUMNE_CCC . mysql_error());}
            $stringData=$alumne."','".$grup."','".$professor."','".$data."','".$materia."','".$inici_sancio."','".$fi_sancio."','".$motiu."','".$comentari."','".$comentari_llarg."\n";
            fwrite($fh, $stringData);

            }
         fclose($fh);

         $sql="DELETE FROM `ccc_taula_principal` WHERE data BETWEEN '".$databd_inici."' AND '".$databd_fi."' ;";
         //echo $sql;
         $result=mysql_query($sql);	
         if (!$result) 	{die(_ERR_DELETE_CCC_ALUMNES . mysql_error());}
   
}

}

function hist_sortides($databd_inici,$databd_fi,$nova_data)
{
   //*******************************************
//Traspassem sortides
//*******************************************
//       
         // Comprovem si s'utilitza el módul d assegurament del servei
         if (file_exists('../../tutoria/sortides'))
            {

            // Clonem grups_sortides

            $sql="SELECT * FROM `grups_sortides`;";
            $result=mysql_query($sql);	
            if (!$result) 	{die(_ERR_SELECT_GRUPS_SORTIDES . mysql_error());}
            while ($fila=mysql_fetch_row($result))
               {
               $sql2="INSERT INTO HIST_grups_sortides(id_sortida,id_grup) ";
               $sql2.="SELECT ".$fila[0].",".$fila[1];
               $sql2.=" FROM dual ";
               $sql2.="WHERE NOT EXISTS (SELECT id_sortida,id_grup FROM HIST_grups_sortides WHERE id_sortida='".$fila[0]."' LIMIT 1) ;";
               $result2=mysql_query($sql2);	
               if (!$result2) 	{die(_ERR_SELECT_GRUPS_SORTIDES2 . mysql_error());}
               }        


            // Clonem sortides_alumnes

            $sql="SELECT * FROM `sortides_alumne`;";
            //echo $sql."<br>",
            $result=mysql_query($sql);	
            if (!$result) 	{die(_ERR_SELECT_SORTIDES_ALUMNES . mysql_error());}
            while ($fila=mysql_fetch_row($result))
               {
               $sql2="INSERT INTO HIST_sortides_alumne(idsortides_alumne,id_sortida,id_alumne) ";
               $sql2.="SELECT ".$fila[0].",".$fila[1].",".$fila[2];
               $sql2.=" FROM dual ";
               $sql2.="WHERE NOT EXISTS (SELECT idsortides_alumne,id_sortida,id_alumne FROM HIST_sortides_alumne ";
               $sql2.="WHERE idsortides_alumne='".$fila[0]."' LIMIT 1) ;";
               //echo $sql2."<br>";
               $result2=mysql_query($sql2);	
               if (!$result2) 	{die(_ERR_SELECT_SORTIDES_ALUMNES2 . mysql_error());}
               }        

            // Clonem sortides_professor

            $sql="SELECT * FROM `sortides_professor`;";
            //echo $sql."<br>",
            $result=mysql_query($sql);	
            if (!$result) 	{die(_ERR_SELECT_SORTIDES_PROFESSORS . mysql_error());}
            while ($fila=mysql_fetch_row($result))
               {
               $sql2="INSERT INTO HIST_sortides_professor(idprofessorat_sortides,id_sortida,id_professorat) ";
               $sql2.="SELECT ".$fila[0].",".$fila[1].",".$fila[2];
               $sql2.=" FROM dual ";
               $sql2.="WHERE NOT EXISTS (SELECT idprofessorat_sortides,id_sortida,id_professorat FROM HIST_sortides_professor ";
               $sql2.="WHERE idprofessorat_sortides='".$fila[0]."' LIMIT 1) ;";
               //echo $sql2."<br>";
               $result2=mysql_query($sql2);	
               if (!$result2) 	{die(_ERR_SELECT_SORTIDES_PROFESSOR2 . mysql_error());}
               }                    

             // Clonem sortides

            $sql="SELECT * FROM `sortides`;";
            //echo $sql."<br>",
            $result=mysql_query($sql);	
            if (!$result) 	{die(_ERR_SELECT_SORTIDES.mysql_error());}
            while ($fila=mysql_fetch_row($result))
               {
               $fila[5]=  neteja_apostrofs($fila[5]);
               $fila[6]=  neteja_apostrofs($fila[6]);
               $sql2="INSERT INTO HIST_sortides ";
               $sql2.="SELECT ".$fila[0].",'".$fila[1]."','".$fila[2]."','".$fila[3]."','".$fila[4]."','".$fila[5]."','".$fila[6]."','".$fila[7]."'";
               $sql2.=" FROM dual ";
               $sql2.="WHERE NOT EXISTS (SELECT * FROM HIST_sortides ";
               $sql2.="WHERE idsortides='".$fila[0]."' LIMIT 1) ;";
               //echo $sql2."<br>";
               $result2=mysql_query($sql2);	
               if (!$result2) 	{die(_ERR_SELECT_SORTIDES_PROFESSOR2 . mysql_error());}
               }            
           }
      //}
}  


function hist_abs_prof($databd_inici,$databd_fi,$nova_data)
{
 // Clonem absències _professorat
          // Comprovem si s'utilitza el módul d assegurament del servei
      if (file_exists('../../tutoria/abs_prof'))
         {        
         $sql="SELECT * FROM `incidencia_professor` WHERE data BETWEEN '".$databd_inici."' AND '".$databd_fi."';";
         //echo $sql."<br>",
         $result=mysql_query($sql);	
         if (!$result) 	{die(_ERR_SELECT_SORTIDES.mysql_error());}
         while ($fila=mysql_fetch_row($result))
            {
            // Genera franja
            $sql2="SELECT hora_inici,hora_fi FROM franges_horaries WHERE idfranges_horaries='".$fila[7]."';";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_FRANGES . mysql_error());}
            $franja=mysql_fetch_row($result2);
            $franja_horaria=$franja[0]."-".$franja[1];//echo $franja_horaria;echo "<br>";			
            // Genera grups
            $sql2="SELECT nom FROM grups WHERE idgrups='".$fila[2]."';";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_GRUPS . mysql_error());}
            $grup=mysql_result($result2,0); //echo $grup;echo "<br>";
            $grup=  neteja_apostrofs($grup);
            // Genera materia
            $sql2="SELECT nom_materia FROM materia WHERE idmateria='".$fila[3]."';";
            //echo $sql2;echo "<br>";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_MATERIA . mysql_error());}
            if (mysql_num_rows($result2)==1) {$materia=mysql_result($result2,0);}
            else
               {
               $sql2="SELECT nom_uf FROM unitats_formatives WHERE idunitats_formatives='".$fila[7]."';";
               //echo $sql2;echo ">>>><br>";
               $result2=mysql_query($sql2);
               if (!$result) 	{die(_ERR_SELECT_UF . mysql_error());}
               $materia=mysql_result($result2,0); //echo $materia;echo "<br>";
               }
            $materia=  neteja_apostrofs($materia);
            // Genera tipus falta
            $sql2="SELECT tipus_falta FROM tipus_falta_professor WHERE idtipus_falta_professor='".$fila[4]."';";
            $result2=mysql_query($sql2);
            if (!$result) 	{die(_ERR_SELECT_TIPUS_INCIDENCIES . mysql_error());}
            $tipus_falta=mysql_result($result2,0);//echo $tipus_falta;echo "<br>";
            $tipus_falta=  neteja_apostrofs($tipus_falta);
            
            
            $sql2="INSERT INTO HIST_incidencia_professor ";
            $sql2.="SELECT ".$fila[0].",'".$fila[1]."','".$grup."','".$materia."','".$tipus_falta."','".$fila[5]."','".$fila[6]."','".$franja_horaria."'";
            $sql2.=" FROM dual ";
            $sql2.="WHERE NOT EXISTS (SELECT * FROM HIST_incidencia_professor ";
            $sql2.="WHERE idincidencia_professor='".$fila[0]."' LIMIT 1) ;";
            //echo $sql2."<br>";
            $result2=mysql_query($sql2);	
            if (!$result2) 	{die(_ERR_SELECT_SORTIDES_PROFESSOR2 . mysql_error());}
            }            

         }
      }


   ?>

