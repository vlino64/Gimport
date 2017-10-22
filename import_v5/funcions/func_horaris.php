<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:funcions_saga.php
* Autor: Víctor Lino
* Descripció: Funcions relacionades amb tasques d'importació de dades de SAGA
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2
// 					CREACIÓ D'HORARIS
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function crea_horaris_ASC_mixt()
    {

    include("../config.php");

    introduir_fase('lessons',0);    
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];


    if (!extreu_fase('segona_carrega'))
        {buidatge('desdecreahoraris');echo "<br>Tot Netejat";}

    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);
    
    $sql="SELECT idperiodes_escolars FROM periodes_escolars WHERE actual='S'; ";
    $result=mysql_query($sql);
    $periode=mysql_result($result,0);
    

    //$resultatconsulta=simplexml_load_file($exportsagaxml);
    
    $csvFile = $_SESSION['upload_horaris'];
    
    $data= array();
    $data = netejaCsv($csvFile);
    foreach ($data as $fila)
        {
        $idMateria = "";
        $idModul ="";
        $idGrup = "";
        $idgrup_materia="";
        //echo "<br>>".$fila;
        $array_fila=explode(";",$fila);

        // Exdtreiem el codi de la materia/unitats formatives
        $codiMateria = $array_fila[0];
        $materia = $array_fila[1];
        //$nom_materia=neteja_apostrofs($nom_materia);
        $codiGrup = $array_fila[2];
        $codiGrup = neteja_apostrofs($codiGrup);
        $codiMateriaUnits = $codiMateria."-".$materia;
        $codiMateriaUnits=neteja_apostrofs($codiMateriaUnits);
        //echo "<br>".$codiMateriaUnits;
        $idMateria = extreu_id('materia','codi_materia','idmateria',$codiMateriaUnits);
        
        $idProfessor = extreu_id("equivalencies","nom_prof_gp","prof_ga",$array_fila[3]);
        //echo "<br>>>>".$idMateria." >>".$codiMateria." >> ".$materia." >> ".$codiGrup." >> ".$array_fila[3]." >> ".$array_fila[4];
        if ($idMateria == "")
            {
            $idModul = extreu_id('equivalencies','materia_gp','materia_saga',$codiMateriaUnits);
            if ($idModul != "") {$idPla = extreu_id('equivalencies','materia_gp','pla_saga',$codiMateriaUnits);}
            }
        //echo "<br>".$idMateria." >> ".$idModul;   
        // Extreiem les sessions    
        $sessions = $array_fila[4];    
            
        // Extreiem l'identificador del grup
        $idGrup = extreu_id('grups','nom','idgrups',$codiGrup);
        //echo "<br>".$codiGrup." >> ".$idGrup;
        // Gestionem el grup-matèria

        if (($idMateria != "") AND ($idGrup != ""))
            {
            //echo "<br>Entra materia";
            // Comprovem que el grup materia no existeix
            $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$idMateria."';";
            
            $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA(1).mysql_error());}
            $present=mysql_num_rows($result);
            $fila2 = mysql_fetch_row($result);$idgrup_materia = $fila2[0];
            if ($present==1) 
                {
                $es_nou_grup_materia = 0;
                $idgrup_materia = creadesdoblament($idgrup_materia,$materia,$idProfessor);
                }
            if ($present==0)
               {
               $es_nou_grup_materia = 1;
               $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('".$idGrup."','".$idMateria."');";
               //echo "<br>".$sql;
               $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA(2).mysql_error());}
               $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$idMateria."';";
               $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
               $idgrup_materia=mysql_result($result,0); 
               }
            // Gestionem el professor... o professors ja que en poden haver 2 omés
            gestionaProfessorESO($array_fila[3],$idgrup_materia,$es_nou_grup_materia);    
            creaSessionsEso($sessions, $idgrup_materia,$codi_noroom,$periode);
            }
        else if (($idModul != "") AND ($idGrup != ""))
            {
            //echo "<br>Entra mòdul";
            $arrayUfs = array();
            for ($j=0;$j<count($arrayUfs);$j++)
                {
                $arrayUfs[$j][0]="";$arrayUfs[$j][1]="";
                }
            
            // Extreiem les unitats formatives dels móduls eliminnat els "DESD" per no fer massa crides
            //$sql = "SELECT id_ufs FROM moduls_ufs WHERE id_moduls =  '".$idModul."'; ";
            $sql = "SELECT A.id_ufs FROM moduls_ufs A,unitats_formatives B WHERE A.id_moduls =  '".$idModul."' AND ";
            $sql.= "A.id_ufs = B.idunitats_formatives AND B.nom_uf NOT LIKE '%DESD%' ";
            //echo "<br>".$sql;
            $resultat=mysql_query($sql);
            if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
            // Repetir+a el bucle per cada Uf d'aquest módul
            $i = 0;
            while ($fila2 = mysql_fetch_row($resultat))
                {
                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$fila2[0]."';";
                //echo "<br>".$sql;
                $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                $present=mysql_num_rows($result);
                $fila3 = mysql_fetch_row($result);$idgrup_materia = $fila3[0];
                if ($present==1) 
                    {
                    $es_nou_grup_materia = 0;
                    $idgrup_materia = creadesdoblament($idgrup_materia,$idModul,$idProfessor);
                    
                    }
                if ($present==0)
                   {
                   $es_nou_grup_materia = 1;
                    // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                   // La posteriors comencen d'aquesta data fins a final de curs.
                   if (primera_uf($fila2[0])==1) 
                      { 
                      $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                      $sql.="VALUES ('".$idGrup."','".$fila2[0]."','".$data_inici."','".$data_tmp2."');";
                       
                      }
                   else
                      {
                      $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                      $sql.="VALUES ('".$idGrup."','".$fila2[0]."','".$data_tmp2."','".$data_fi."');";
                      } 
                      }
                   //echo "<br>".$sql;  
                   $result=mysql_query($sql);if (!$result) {die(_INSERINT_GRUP_MATERIA(3).mysql_error());}
                   $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$fila2[0]."';";
//                   echo "<br>".$sql;
                   $result=mysql_query($sql);if (!$result) {die(_CERCANT_GRUP_MATERIA.mysql_error());}
                   $idgrup_materia=mysql_result($result,0);
//                   echo "<br>".$idgrup_materia;
//                   echo "<br>>>>".$i;
                   
                $arrayUfs[$i][0] = $idgrup_materia;
                $arrayUfs[$i][1] = $es_nou_grup_materia;
                $i++;
//                 echo "<br>>>>".$i;  
                 } 
                 
//            for ($j=0;$j<count($arrayUfs);$j++)
//                {
//                echo "<br>=== ".$arrayUfs[$j][0]." >> ".$arrayUfs[$j][1];
//                }
            gestionaProfessorCCFF($array_fila[3],$arrayUfs);
            creaSessionsCCFF($sessions,$arrayUfs,$codi_noroom,$periode);
            }
        }
        introduir_fase('lessons',1);
        $page = "./menu.php";
        $sec="0";
        header("Refresh: $sec; url=$page");		
        
    }


function crea_horaris_gp_mixt($exportsagaxml,$exporthorarixml) 
    {

    include("../config.php");

    introduir_fase('lessons',0);    
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];


    if (!extreu_fase('segona_carrega'))
        {buidatge('desdecreahoraris');echo "<br>Tot Netejat";}

    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);

    $resultatconsulta=simplexml_load_file($exportsagaxml);
    $resultatconsulta2=simplexml_load_file($exporthorarixml);

    if  ( !$resultatconsulta ) {echo "Carrega fallida Saga >> ".$exportsagaxml;}
    else if ( !$resultatconsulta2 ) {echo "Carrega fallida Horaris >> ".$exporthorarixml;}
    else
        {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->lessons->lesson as $classe)
            {
            $professor=$classe->lesson_teacher[id];
            $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);

            $materia=$classe->lesson_subject[id];
            $materia=neteja_apostrofs($materia);
            $grup = $classe->lesson_classes[id];
            $id_materia=extreu_id('equivalencies','materia_gp','materia_saga',$materia);
            //echo "<br>".$materia." >>> ".$id_materia;;
             // Si és un modul de CCFF LOE
            if ($id_materia!="")
                {
                //echo "Ha entrat, és un módul del pla ";
                // ********************************************************
                // **********  SI ÉS UN MÒDUL DE CCFF  *****************
                // ******************************************************** 
                $grup=$classe->lesson_classes[id];
                //Primer hem d'extreure el pla d'estudis en funció de la classe de la taula d'equivalències
                $id_pla=extreu_id('equivalencies','grup_gp','pla_saga',$grup);
                //echo $id_pla;
                $sql="SELECT materia_saga FROM equivalencies WHERE materia_gp='".$materia."' AND pla_saga='".$id_pla."';";
                //echo "<br>".$sql;
                $resultat=mysql_query($sql);
                if (!$resultat) {die(SELECT_MATERIA.mysql_error());}
                $fila=  mysql_fetch_row($resultat);
                $id_materia=$fila[0];

                //echo "<br>".$classe[id]." ---> ".$professor." >> ".$id_professor." >> ".$materia." >> ".$id_materia." >> ".$grup;

                if (($grup!='') AND ($id_materia!=''))
                   {
                   // Extreiem les unitats formatives dels móduls
                       $sql = "SELECT A.id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '".$id_materia."' ";
                       $sql.= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                   //echo "<br>".$sql;
                   $resultat=mysql_query($sql);

                   if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                   // Repetir+a el bucle per cada Uf d'aquest módul
                   while ($fila=mysql_fetch_row($resultat))
                      {
                      // Cerquem en la taula grups
                      $sql="SELECT idgrups FROM grups WHERE codi_grup='".$grup."';";
                      $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS.mysql_error());}
                      $idgrup=mysql_result($result,0);
                      if ($idgrup=='')
                            {
                            // Cerquem en la taula equivalencies
                            $sql="SELECT grup_ga FROM equivalencies WHERE grup_gp='".$grup."';";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
                            $idgrup=mysql_result($result,0);	
                            }
                      if ($idgrup!='')
                            {
                            // Afegit per fe comprovacions
                            $sql="SELECT nom_uf,codi_uf FROM unitats_formatives WHERE idunitats_formatives='".$fila[0]."';";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                            $fila2=  mysql_fetch_row($result);
                            //print("<br> dades uf: ".$fila2[0]."  -  ".$fila2[1]);

                            // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                            $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$fila[0]."';";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                            $present=mysql_num_rows($result);
                            if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                            if ($present==0)
                               {
                               $es_nou_grup_materia = 1;
                                // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                               // La posteriors comencen d'aquesta data fins a final de curs.
                               if (primera_uf($fila[0])==1) 
                                  { 
                                  $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                  $sql.="VALUES ('".$idgrup."','".$fila[0]."','".$data_inici."','".$data_tmp2."');";
                                  //echo "<br>      ".$sql;
                                  }
                               else
                                  {
                                  $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                  $sql.="VALUES ('".$idgrup."','".$fila[0]."','".$data_tmp2."','".$data_fi."');";
                                  }

                               $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
                               $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$fila[0]."';";
                               $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                               $idgrup_materia=mysql_result($result,0);
                               }
                            // Assignem el profe al grup materia si no existeix ja
                            if ($id_professor!='')
                               {
                                if ($es_nou_grup_materia)
                                    {
                                    $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                    //echo "<br>>>>>>>".$sql;
                                    $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                    }
                                else 
                                    {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                                    if ( $comprovacio == 1 ) 
                                        {
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                        //echo "<br>>>>>>>".$sql;
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        }
                                     else
                                        {
                                        $idgrup_materia = $comprovacio;
                                        }                                        
                                    }
                               }		
                            foreach ($classe->times->time as $franges)
                               {
                               //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
                               // Extreiem el codi de la franja/dia
                               $dia=$franges->assigned_day;
                               $franja=$franges->assigned_period;
                               $horainici=$franges->assigned_starttime;
                               $horafi=$franges->assigned_endtime;

                               if(extreu_fase('segona_carrega'))
                                  {
                                  $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                                  $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                                  $franja=mysql_result($result,0);
                                  }

                               if (($horainici!="") && ($horafi!=""))
                                  {
                                  $horainici=$horainici*100;
                                  $horainici=arregla_hora_gpuntis($horainici);
                                  $horafi=$horafi*100;
                                  $horafi=arregla_hora_gpuntis($horafi);

                                  $id_torn=extreu_id('grups','idgrups','idtorn',$idgrup);

                                  $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                                  $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                                  $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                                  //echo "<br>".$sql;
                                  $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                  $codi_dia_franja=mysql_result($result,0);
                                  }
                               else 
                                  {
                                  //Per si hi ha torn superposats....
                                  $codi_dia_franja=extreu_codi_franja($dia,$franja,$idgrup);

                                  //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                  }


                               // Extreiem l'id de l'espai
                               $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                               $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                               $codi_espai=mysql_result($result,0);
                               if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                               // Inserim la unitat classe
                               $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
                               $result=mysql_query($sql);
                               }
                            }
                         }			
                      }
                    }
                else 
                    {
                    // Comprovem si és una materia
                    $id_materia=extreu_id('materia','codi_materia','idmateria',$materia);
                    // ********************************************************
                    // **********  SI ÉS UNA MATEIA DE L'ESO  *****************
                    // ********************************************************
                   
                    if ($id_materia!="")
                       {
                       $grup=$classe->lesson_classes[id];
                       if (($grup!='')	AND ($id_materia!=''))
                          {
                          // Cerquem en la taula grups
                          $sql="SELECT idgrups FROM grups WHERE codi_grup='".$grup."';";
                          $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS.mysql_error());}
                          $idgrup=mysql_result($result,0);
                          if ($idgrup=='')
                                {
                                // Cerquem en la taula equivalencies
                                $sql="SELECT grup_ga FROM equivalencies WHERE grup_gp='".$grup."';";
                                //echo "<br>".$sql;
                                $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
                                $idgrup=mysql_result($result,0);	
                                }
                          if ($idgrup!='')
                                {
                                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                                $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$id_materia."';";
                                $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                                $present=mysql_num_rows($result);
                                if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                                if ($present==0)
                                   {
                                   $es_nou_grup_materia = 1;
                                   $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('".$idgrup."','".$id_materia."');";
                                   //echo $sql."<br>";
                                   $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
                                   $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$id_materia."';";
                                   $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                                   $idgrup_materia=mysql_result($result,0);
                                   }
                                // Assignem el profe al grup materia si no existeix ja
                                if ($id_professor!='')
                                   {
                                if ($es_nou_grup_materia)
                                    {
                                    $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                    //echo "<br>>>>>>>".$sql;
                                    $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                    }
                                else 
                                    {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                                    if ( $comprovacio == 1 ) 
                                        {
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                        //echo "<br>>>>>>>".$sql;
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        }
                                     else
                                        {
                                        $idgrup_materia = $comprovacio;
                                        }                                        
                                    }
                                   }		
                                foreach ($classe->times->time as $franges)
                                   {
                                   //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
                                   // Extreiem el codi de la franja/dia
                                   $dia=$franges->assigned_day;
                                   $franja=$franges->assigned_period;
                                   $horainici=$franges->assigned_starttime;
                                   $horafi=$franges->assigned_endtime;

                                   if(extreu_fase('segona_carrega'))
                                      {
                                      $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                                      $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                                      $franja=mysql_result($result,0);
                                      }

                                   if (($horainici!="") && ($horafi!=""))
                                      {
                                      $horainici=$horainici*100;
                                      $horainici=arregla_hora_gpuntis($horainici);
                                      $horafi=$horafi*100;
                                      $horafi=arregla_hora_gpuntis($horafi);

                                      $id_torn=extreu_id('grups','idgrups','idtorn',$idgrup);

                                      $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                                      $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                                      $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                                      //echo "<br>   ".$sql;
                                      $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                      $codi_dia_franja=mysql_result($result,0);
                                      }
                                   else 
                                      {
                                      //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                      //Per si hi ha torn superposats....

                                      $codi_dia_franja=extreu_codi_franja($dia,$franja,$idgrup);                              
                                      }
                                   //$result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                   //$codi_dia_franja=mysql_result($result,0);
                                   // Extreiem l'id de l'espai
                                   $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                                   $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                                   $codi_espai=mysql_result($result,0);
                                   if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                                   // Inserim la unitat classe
                                   $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
                                   //echo "<br>".$sql;
                                   $result=mysql_query($sql);if (!$result) {die(INSERT_UNITATS_CLASSE2.mysql_error());}

                                   }
                                }
                             }			
                          }

                       }
                    }

                }

            foreach ($resultatconsulta2->lessons->lesson as $classe)
               {
               // Valorem si es tracta d'una guardia o una tutoria
               // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
               $professor=$classe->lesson_teacher[id];
               $id_professor=extreu_id('equivalencies','prof_gp','prof_ga',$professor);
               $grup=$classe->lesson_classes[id];
               $id_grup=extreu_id('grups','codi_grup','idgrups',$grup);
               if ($id_grup=='')
                  {$id_grup=extreu_id('equivalencies','grup_gp','grup_ga',$grup);}
               $materia=$classe->lesson_subject[id];
               $materia=neteja_apostrofs($materia);
               if (($id_professor!='') AND ($id_grup!='') AND ($materia=='SU_TUT'))
                  {
                  // Comprovem si no existeix ja
                  $sql="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$id_professor."' AND idcarrecs='1' AND idgrups='".$id_grup."';";
                  $result=mysql_query($sql);	
                  if (!$result) {die(_ERR_SELECT_TUTOR . mysql_error());}
                  if(mysql_num_rows($result)<1)
                     {
                     $sql="INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('".$id_professor."','1','".$id_grup."');";
                     $result=mysql_query($sql);	
                     if (!$result) {die(_ERR_ASSIGN_TUTOR . mysql_error());}						
                     }
                  }
               else if (($id_professor!='') AND ($materia=='SU_GU'))
                  {
                  foreach ($classe->times->time as $franges)
                     {
                     $dia=$franges->assigned_day;
                     $franja=$franges->assigned_period;
                     $codi_dia_franja=extreu_codi_franja_guardies($dia,$franja);
          //               $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$franges->assigned_day."' AND idfranges_horaries='".$franges->assigned_period."' ";
          //               $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
          //               $codi_dia_franja=mysql_result($result,0);
                     // Extreiem l'id de l'espai
                     $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                     $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                     $codi_espai=mysql_result($result,0);
                     if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                     // Inserim la unitat classe
                     $sql="INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('".$id_professor."','".$codi_dia_franja."','".$codi_noroom."')";
                     //echo "<br>".$sql;
                     $result=mysql_query($sql);if (!$result) {die(ERROR_INTRO_GUARDIES.mysql_error());}	
                     }
                  }		

               }
            introduir_fase('lessons',1);
            $page = "./menu.php";
            $sec="0";
            header("Refresh: $sec; url=$page");		
        }

function extreu_grup_HW($exporthorarixml,$grupHW)
    {
    $resultatconsulta4=simplexml_load_file($exporthorarixml);
    if  ( !$resultatconsulta4 ) {echo "Carrega fallida Saga >> ".$exporthorarixml;}
    $abreviatura = 0;
    foreach ($resultatconsulta4->DATOS->GRUPOS -> GRUPO as $grup)
        {
        //echo "<br>".$grup[num_int_gr]." >> ".$grupHW;
        if (!strcmp($grup[num_int_gr],$grupHW)) 
            {
            $abreviatura = $grup[abreviatura];
//            echo "<br>Ha entrat >>> ".$abreviatura;
            }
        }
    return $abreviatura;
    }

// Modificat per adequar-lo a aSc ioder incorporar desdoblaments en els que coincideixin tant professors com materies
// Seria el cas d'un professor que fa una hora amb tot el grup, una hora amb mig grup i una altra  hora amb
// l'altre mig grup. per tant s'haurien de crear tres arupaments diferents per la mateixa materìa i professor. 
// Per tant tres grups materia    
    
function comprova_desdoblament($id_professor,$idgrup_materia)
    {
    include("../config.php");    
    // Comprova si aquest grup materia està assignat i si ho està , si és al professor que ens ha arribat
//    echo "<br>".$id_professor." >>".$idgrup_materia;
    // Separo el grup i matèria
    $sql="SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='".$idgrup_materia."';";
    
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT.mysql_error());}
    $fila = mysql_fetch_row($result);
//    echo "<br>grup".$fila[0]." >> materia: ".$fila[1];
    // Comprovem amb la variable taula si es materia o unitat formativa
    // Extreim el codi de la materia
    $sql2 = "SELECT codi_uf,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '".$fila[1]."';";
    $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $present = mysql_num_rows($result2); 
    if ($present) {$taula = 1;}
    if ($present == 0)
        {
        $sql2 = "SELECT codi_materia,nom_materia,idmateria FROM materia WHERE idmateria = '".$fila[1]."';";
        $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_SELECT_GRUPS_MAT3.mysql_error());}
        $present = mysql_num_rows($result2);        
        if ($present) {$taula = 2;}
        }
    
    $fila2 = mysql_fetch_row($result2); 
    //echo "<br> La taula és...".$taula;
    if ($taula == 1)
        {
        $sql = "SELECT A.idunitats_formatives, A.codi_uf FROM unitats_formatives A, grups_materies B, grups C ";
        $sql.= "WHERE codi_uf LIKE '%".$fila2[0]."%' AND B.id_grups = C.idgrups ";
        $sql.= "AND B.id_mat_uf_pla=A.idunitats_formatives AND C.idgrups='".$fila[0]."' ORDER BY idunitats_formatives ;";
        }
    if ($taula == 2)
        {
        // Treu totles les materies que tenen el patró
        //$sql = "SELECT idmateria FROM materia WHERE codi_materia LIKE '%".$fila2[0]."%' ORDER BY idmateria ;";
        
        $sql = "SELECT A.idmateria FROM materia A, grups_materies B, grups C ";
        $sql.= "WHERE codi_materia LIKE '%".$fila2[0]."%' AND B.id_grups = C.idgrups ";
        $sql.= "AND B.id_mat_uf_pla=A.idmateria AND C.idgrups='".$fila[0]."' ORDER BY idmateria ;";
        
        
        }
//    echo "<br>.. Comprova ...".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $assignat = 0;
    while ($fila3 = mysql_fetch_row($result))
        {
        // Per cada unitat formativa exteriem el grup materia forçant que ertany al grup
        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE ";
        $sql2.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
        $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_EXTRACT_MAT_GRUP1.mysql_error());}
        while ($fila4=  mysql_fetch_row($result2))
            {
            $sql3="SELECT idprofessors FROM prof_agrupament WHERE idagrups_materies='".$fila4[0]."';";
            $result3=mysql_query($sql3);if (!$result3) {die(_ERROR_SELECT_PROF_GRUPS_MAT.mysql_error());}
            $fila5 = mysql_fetch_row($result3);
            if (($fila5[0] == $id_professor)) {$idGrupMateria= $fila4[0];$assignat = 1;}
            
            }
        }
    if ($assignat == 0 ) {return 1;}
    else {return $idGrupMateria;}
    }        
      
function comprova_desdoblament_tmp($id_professor,$idgrup_materia)
    {
    include("../config.php");    
    // Comprova si aquest grup materia està assignat i si ho està , si és al professor que ens ha arribat
    
    // Separo el grup i matèria
    $sql="SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='".$idgrup_materia."';";
    
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT.mysql_error());}
    $fila = mysql_fetch_row($result);
    
    // Comprovem amb la variable taula si es materia o unitat formativa
    // Extreim el codi de la materia
    $sql2 = "SELECT codi_uf,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '".$fila[1]."';";
    $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $present = mysql_num_rows($result2); 
    if ($present) {$taula = 1;}
    if ($present == 0)
        {
        $sql2 = "SELECT codi_materia,nom_materia,idmateria FROM materia WHERE idmateria = '".$fila[1]."';";
        $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_SELECT_GRUPS_MAT3.mysql_error());}
        $present = mysql_num_rows($result2);        
        if ($present) {$taula = 2;}
        }
    
    $fila2 = mysql_fetch_row($result2); 
    //echo "<br> La taula és...".$taula;
    if ($taula == 1)
        {
        $sql = "SELECT A.idunitats_formatives, A.codi_uf FROM unitats_formatives A, grups_materies B, grups C ";
        $sql.= "WHERE codi_uf LIKE '%".$fila2[0]."%' AND B.id_grups = C.idgrups ";
        $sql.= "AND B.id_mat_uf_pla=A.idunitats_formatives AND C.idgrups='".$fila[0]."' ORDER BY idunitats_formatives ;";
        }
    if ($taula == 2)
        {
        // Treu totles les materies que tenen el patró
        //$sql = "SELECT idmateria FROM materia WHERE codi_materia LIKE '%".$fila2[0]."%' ORDER BY idmateria ;";
        
        $sql = "SELECT A.idmateria FROM materia A, grups_materies B, grups C ";
        $sql.= "WHERE codi_materia LIKE '%".$fila2[0]."%' AND B.id_grups = C.idgrups ";
        $sql.= "AND B.id_mat_uf_pla=A.idmateria AND C.idgrups='".$fila[0]."' ORDER BY idmateria ;";
        
        
        }
    //echo "<br>.. Comprova ...".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $assignat = 0;
    while ($fila3 = mysql_fetch_row($result))
        {
        // Per cada unitat formativa exteriem el grup materia forçant que ertany al grup
        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE ";
        $sql2.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
        $result2=mysql_query($sql2);if (!$result2) {die(_ERROR_EXTRACT_MAT_GRUP1.mysql_error());}
        while ($fila4=  mysql_fetch_row($result2))
            {
            $sql3="SELECT idprofessors FROM prof_agrupament WHERE idagrups_materies='".$fila4[0]."';";
            $result3=mysql_query($sql3);if (!$result3) {die(_ERROR_SELECT_PROF_GRUPS_MAT.mysql_error());}
            $fila5 = mysql_fetch_row($result3);
            if (($fila5[0] == $id_professor)) {$idGrupMateria= $fila4[0];$assignat = 1;}
            
            }
        }
    if ($assignat == 0 ) {return 1;}
    else {return $idGrupMateria;}
    }    
    
    
function creadesdoblament($idgrup_materia,$modul,$idProfessor)
// rep el el grup materia del darrer desdoblament del grup afectat
        
    {
    include("../config.php");    
    
    // Separo el grup i matèria
    $sql="SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='".$idgrup_materia."';";
    //echo "<br>".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT.mysql_error());}
    $fila = mysql_fetch_row($result);
    
    // Comprovem amb la variable taula si es materia o unitat formativa
    $sql = "SELECT nom_uf,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '".$fila[1]."';";
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $present = mysql_num_rows($result); 
    if ($present) {$taula = 1;}
    if ($present == 0)
        {
        $sql = "SELECT codi_materia,nom_materia,idmateria FROM materia WHERE idmateria = '".$fila[1]."';";
        $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT3.mysql_error());}
        $present = mysql_num_rows($result);        
        if ($present) {$taula = 2;}
        }
    
    
    $fila2 = mysql_fetch_row($result);     
    
    if ( $taula == 1)
        {
        $sql = "SELECT A.idunitats_formatives,A.codi_uf,A.nom_uf FROM unitats_formatives A,grups_materies B, grups C ";
        $sql.= "WHERE A.nom_uf LIKE '%".$fila2[1]."%'AND A.idunitats_formatives = B.id_mat_uf_pla ";
        $sql.= "AND B.id_grups = C.idgrups AND C.idgrups= '".$fila[0]."' " ;
        $sql.= "ORDER BY idunitats_formatives DESC LIMIT 1;";
//        echo "<br>".$sql;
        }
    if ( $taula == 2)
        {
        $sql = "SELECT A.idmateria,A.codi_materia,A.nom_materia FROM materia A,grups_materies B, grups C ";
        $sql.= "WHERE codi_materia LIKE '%".$fila2[0]."%'AND A.idmateria = B.id_mat_uf_pla ";
        $sql.= "AND B.id_grups = C.idgrups AND C.idgrups= '".$fila[0]."' " ;
        $sql.= "ORDER BY idmateria DESC LIMIT 1;";
        }
       
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_UFSS.mysql_error());}         
    $fila3 = mysql_fetch_row($result);
    //echo "<br>".$sql;
    // Comprovem si el nou nom ja existeix degut a que s'ha creat per un altre grup 
    $id_materia = $fila3[0];
    //echo "<br>".$fila3[0]." >>> ".$fila3[1]." >>> ".$fila3[2]; 
    $nou_nom = genera_nom_desdoblament($fila3);
    $nou_nom[1] = substr($nou_nom[1],0,99);
//    echo "<br>".$nou_nom[0]." >>> ".$nou_nom[1]." >>> ".$nou_nom[2]; 
    $sql = "SELECT COUNT(codi_materia) FROM moduls_materies_ufs WHERE codi_materia = '".$nou_nom[1]."';";
    //echo "<br>".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_CHECK_MAT_DESDD.mysql_error());}
    $fila4 = mysql_fetch_row($result);
    if ($fila4[0] == 0)
        {   
        $id_pla = extreu_id('moduls_materies_ufs','id_mat_uf_pla','idplans_estudis',$fila2[2]);

        // Situem a la taula moduls_materies_ufs
        $sql = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
        $sql.= "VALUES ('".$id_pla."','".$nou_nom[1]."','S');";
        //echo "<br>".$sql;
        $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_DESDD.mysql_error());}
        $id_materia = extreu_id('moduls_materies_ufs','codi_materia','id_mat_uf_pla',$nou_nom[1]);


        if ($taula == 1)
            {
            $sql = "INSERT INTO unitats_formatives(idunitats_formatives,codi_uf,nom_uf,data_inici,data_fi) ";
            $sql.= "VALUES ('".$id_materia."','".$nou_nom[1]."','".$nou_nom[2]."','".$fila[2]."','".$fila[3]."');";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_DESD2.mysql_error());}

            $sql = "INSERT INTO moduls_ufs(id_moduls,id_ufs) VALUES ('".$modul."','".$id_materia."');";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_DESD3.mysql_error());}
            
            }    
        if ( $taula == 2)
            {

            $sql = "INSERT INTO materia(idmateria,codi_materia,nom_materia) ";
            $sql.= "VALUES ('".$id_materia."','".$nou_nom[1]."','".$nou_nom[2]."');" ;
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_DESD5.mysql_error());}

            }
        }    
    else
        {
        //Com que existeix, trec l'id i l'assino al grup geneant un nou grup materia
        $sql = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$nou_nom[1]."';";
        //echo "<br>".$sql;
        $result=mysql_query($sql);if (!$result) {die(_ERROR_CHECK_MAT_DESDD.mysql_error());}
        $fila4 = mysql_fetch_row($result);$id_materia=$fila4[0];
        
        }
    if ($taula ==1) 
        {
        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql.= "VALUES ('".$fila[0]."','".$id_materia."','".$fila[2]."','".$fila[3]."');";
        }
    else 
        {
        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla) ";
        $sql.= "VALUES ('".$fila[0]."','".$id_materia."');";
        }
    
    //echo "<br>".$sql;        
    $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_GRUP_MAT11.mysql_error());}

    $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
    $sql.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$id_materia."');";
    //echo "<br>".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_EXTRACT_MAT_GRUP2.mysql_error());}
    $grup_materiav=  mysql_fetch_row($result);
    
    $idgrup_materia = $grup_materiav[0];
    //echo "<br> id grup materia".$idgrup_materia;
    return $idgrup_materia;
    }
    
// treu el darrer desdoblament del grup afectat
//function treu_darrer_desdoblament($idgrup_materia)
//    {
//    include("../config.php");    
//    
//    // Separo el grup i matèria
//    $sql="SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='".$idgrup_materia."';";
////    echo "<br>".$sql;
//    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT.mysql_error());}
//    $fila = mysql_fetch_row($result);
//    
//    // Comprovem amb la variable taula si es materia o unitat formativa
//    $sql = "SELECT codi_uf,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '".$fila[1]."';";
////    echo "<br>".$sql;
//    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
//    $present = mysql_num_rows($result); 
//    if ($present) {$taula = 1;}
//    if ($present == 0)
//        {
//        $sql = "SELECT codi_materia,nom_materia,idmateria FROM materia WHERE idmateria = '".$fila[1]."';";
////        echo "<br>".$sql;
//        $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT3.mysql_error());}
//        $present = mysql_num_rows($result);        
//        if ($present) {$taula = 2;}
//        }
//    
//    $fila2 = mysql_fetch_row($result); 
//    
//    if ( $taula == 1)
//        {
//        //echo "<br>Ha de crear el nou grup matèria";    
//        $sql = "SELECT idunitats_formatives FROM unitats_formatives WHERE codi_uf LIKE '%".$fila2[0]."%' ";
//        $sql.= "ORDER BY idunitats_formatives DESC LIMIT 1;";
////        echo "<br>".$sql;
//        }
//    if ( $taula == 2)
//        {
//        $sql = "SELECT idmateria FROM materia  WHERE nom_materia LIKE '%".$fila2[1]."%' ";
//        $sql.= "ORDER BY idmateria DESC LIMIT 1;";
//        echo "<br>".$sql;
//        }    
//    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_UFS.mysql_error());}         
//    $fila3 = mysql_fetch_row($result);
//    
//    $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
//    $sql.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
////    echo "<br>".$sql;
//    $result=mysql_query($sql);if (!$result) {die(_ERROR_EXTRACT_MAT_GRUP3.mysql_error());}
//    if (mysql_num_rows($result) == 0)
//        {
//        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
//        $sql.= "VALUES('".$fila[0]."','".$fila3[0]."','".$fila[2]."','".$fila[3]."')";
////        echo "<br>".$sql;
//        $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_GRUP4.mysql_error());}
//
//        $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
//        $sql.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
//        $result=mysql_query($sql);if (!$result) {die(_ERROR_EXTRACT_MAT_GRUP5.mysql_error());}        
//        
//        }
//        
//    $grup_materiav=  mysql_fetch_row($result);
//    return $grup_materiav[0];
//    
//    
//    }
function treu_darrer_desdoblament($idgrup_materia)
    {
    include("../config.php");    
    
    // Separo el grup i matèria
    $sql="SELECT id_grups,id_mat_uf_pla,data_inici,data_fi FROM grups_materies WHERE idgrups_materies='".$idgrup_materia."';";
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT.mysql_error());}
    $fila = mysql_fetch_row($result);
    
    // Comprovem amb la variable taula si es materia o unitat formativa
    $sql = "SELECT codi_uf,nom_uf,idunitats_formatives FROM unitats_formatives WHERE idunitats_formatives = '".$fila[1]."';";
//    echo "<br>".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT2.mysql_error());}
    $present = mysql_num_rows($result); 
    if ($present) {$taula = 1;}
    if ($present == 0)
        {
        $sql = "SELECT codi_materia,nom_materia,idmateria FROM materia WHERE idmateria = '".$fila[1]."';";
//        echo "<br>".$sql;
        $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_GRUPS_MAT3.mysql_error());}
        $present = mysql_num_rows($result);        
        if ($present) {$taula = 2;}
        }
    
    $fila2 = mysql_fetch_row($result); 
    
    if ( $taula == 1)
        {
        $sql = "SELECT A.idgrups_materies, C.codi_uf ";
        $sql .= "FROM grups_materies A, grups B, unitats_formatives C ";
        $sql .= "WHERE C.codi_uf LIKE '%".$fila2[0]."%' AND B.idgrups = $fila[0] AND ";
        $sql .= " B.idgrups = A.id_grups AND C.idunitats_formatives = A.id_mat_uf_pla";
        $sql .= " ORDER BY idunitats_formatives DESC LIMIT 1;";
        }
    if ( $taula == 2)
        {
        $sql = "SELECT A.idgrups_materies, C.nom_materia ";
        $sql .= "FROM grups_materies A, grups B, materia C ";
        $sql .= "WHERE C. nom_materia LIKE '%".$fila2[1]."%' AND B.idgrups = $fila[0] AND ";
        $sql .= " B.idgrups = A.id_grups AND C.idmateria = A.id_mat_uf_pla";
        $sql .= " ORDER BY idmateria DESC LIMIT 1;";
        }    
    //echo "<br> treu...".$sql;
    $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_UFS.mysql_error());}         
    $fila3 = mysql_fetch_row($result);
    
//    $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
//    $sql.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
////    echo "<br>".$sql;
//    $result=mysql_query($sql);if (!$result) {die(_ERROR_EXTRACT_MAT_GRUP3.mysql_error());}
//    if (mysql_num_rows($result) == 0)
//        {
//        $sql = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
//        $sql.= "VALUES('".$fila[0]."','".$fila3[0]."','".$fila[2]."','".$fila[3]."')";
//        echo "<br>".$sql;
//        $result=mysql_query($sql);if (!$result) {die(_ERROR_INSERT_MAT_GRUP4.mysql_error());}
//
//        $sql = "SELECT idgrups_materies FROM grups_materies WHERE ";
//        $sql.= "(id_grups = '".$fila[0]."' AND id_mat_uf_pla = '".$fila3[0]."');";
//        $result=mysql_query($sql);if (!$result) {die(_ERROR_EXTRACT_MAT_GRUP5.mysql_error());}        
//        
//        }
//        
//    $grup_materiav=  mysql_fetch_row($result);
//    return $grup_materiav[0];
    
    return $fila3[0];
    }

function genera_nom_desdoblament($vector)    
    {
//    echo "<br>vector que arriba".$vector[0]." ".$vector[1]." ".$vector[2];
    if (substr($vector[1],0,4) != "DESD")
        {
        //Si és el primer desdoblament
        $vector[1] = "DESD_".$vector[1];
        $vector[2] = "DESD_".$vector[2];
        //echo "<br>".$vector[1]." >> ".$vector[2];
        }     
    else 
        {
        //Si és el segon desdoblament
        $arrel = explode("_",$vector[1]);
        $arrel2 = explode("_",$vector[2]);
        $index = strlen($arrel[0]);
        if ($index == 4)
            {
            $arrel[0] = "DESD1";
            $arrel2[0] = "DESD1";
            }
        else 
            {
            if ($index == 5) // Si és del primers deu desdoblament
                {
                $repeticio = intval(substr($arrel[0],4,1));
                }
            else  // Si hi ha més de deu desdoblaments. Axò passa quan s'obliden una reunió o quelcom semblant
                {
                $repeticio = intval(substr($arrel[0],4,2));
                }
            $repeticio ++;
            $arrel[0]= "DESD".$repeticio;
            $arrel2[0]= "DESD".$repeticio;
            }
        $vector[1] = implode("_",$arrel);    
        $vector[2] = implode("_",$arrel2);    
        }
    return $vector;
     
    
    
    
    }    
        
function treuDatesUnitatsFormatives()
    {
    $intervalDies= "150 days";
    $dates = array();
    include("../config.php");
   
    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S' ;";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
    $fila = mysql_fetch_row($result);$periode = $fila[0];     
        
    // Extreiem data inici i data fi dels periodes escolars
    $sql="SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S';";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DATES.mysql_error());}
    $fila=mysql_fetch_row($result);
    $dates[0]=$fila[0];
    $data_tmp=date_create($data_inici);
    date_add($data_tmp,date_interval_create_from_date_string($intervalDies));
    $dates[1]=date_format($data_tmp,"Y-m-d");
    $dates[2]=$fila[1];
    
    return $dates;
    
    }
        
        
function crea_horaris_KW_mixt($exportsagaxml,$exporthorarixml) 
    {

    include("../config.php");

    introduir_fase('lessons',0);
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];

    if (!extreu_fase('segona_carrega'))
        {buidatge('desdecreahoraris');
        //echo "<br>Tot Netejat";
        }

    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);
    
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    $resultatconsulta2=simplexml_load_file($exporthorarixml);
    $resultatconsulta3=simplexml_load_file($exporthorarixml);

    if  ( !$resultatconsulta ) {echo "Carrega fallida Saga >> ".$exportsagaxml;}
    else if ( !$resultatconsulta2 ) {echo "Carrega fallida Horaris >> ".$exporthorarixml;}
    else
        {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->SOLUCT -> SOLUCF as $unitatClasse)
            {
            $idProfessor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$unitatClasse[PROF]);
            $idGrup = extreu_id('equivalencies','grup_gp','grup_ga',$unitatClasse[CODGRUPO]);
            $id_espai = extreu_id('espais_centre','codi_espai','idespais_centre',$unitatClasse[AULA]);
            if ($id_espai == "") {$id_espai = $codi_noroom;}
            
            // Exdtreiem el codi de la materia/unitats formatives
            $codiMateria = $unitatClasse[ASIG];
            $idMateria = extreu_id('materia','codi_materia','idmateria',$codiMateria);
            if ($idMateria == "")
                {
                $idModul = extreu_id('equivalencies','materia_gp','materia_saga',$codiMateria);
                if ($idModul != "") {$idPla = extreu_id('equivalencies','materia_gp','pla_saga',$codiMateria);}
                }            
            
            $sql="SELECT id_dies_franges,idperiode_escolar FROM dies_franges WHERE iddies_setmana='".$unitatClasse[DIA]."' AND idfranges_horaries='".$unitatClasse[HORA]."';";
            $result=mysql_query($sql);	
            if (!$result) {die(_ERR_SELECT_DIA_FRANJA . mysql_error());}
            $fila = mysql_fetch_row($result);
            $idDiaFranja = $fila[0];
            $periode = $fila[1];
            
            // Comprovem dades
//            echo "<br> id espai".$id_espai;

            // Gestionem el grup-matèria
            if (($idMateria != "") AND ($idGrup != ""))
                {
                // Comprovem que el grup materia no existeix
                $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$idMateria."';";
                //echo "<br>".$sql;
                $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA(1).mysql_error());}
                $present=mysql_num_rows($result);
                $fila2 = mysql_fetch_row($result);$idgrup_materia = $fila2[0];
                if ($present==1) 
                    {
                    $es_nou_grup_materia = 0;
                    //$idgrup_materia = creadesdoblament($idgrup_materia,$materia,$idProfessor);
                    }
                if ($present==0)
                   {
                   $es_nou_grup_materia = 1;
                   $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('".$idGrup."','".$idMateria."');";
                   //echo "<br>".$sql;
                   $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA(2).mysql_error());}
                   $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$idMateria."';";
                   $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                   $idgrup_materia=mysql_result($result,0); 
                   }

                   
                // Gestionem el professor... o professors ja que en poden haver 2 omés
                if ($es_nou_grup_materia)
                    {
                    $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$idgrup_materia."');";
//                    echo "<br>>>>>>>".$sql;
                    $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}            
                    }
                else 
                    {
                    // Hem de comprovar que la relació no estigui ja establerta
                    $sql="SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '".$idProfessor."' AND idagrups_materies ='".$idgrup_materia."';";
                    //echo "<br>>>>>>>".$sql;
                    $result=mysql_query($sql);if (!$result) {	die(_SELECT_PROF_GRUP_MATERIA.mysql_error());}
                    if (mysql_num_rows($result) == 0)
                        {
                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$idgrup_materia."');";
                        //echo "<br>>>>>>>".$sql;
                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA(2).mysql_error());}                            
                        }
                    }                   
                   
                
                // Generem la unitat classe  
                $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                $sql.="('".$idDiaFranja."','".$id_espai."','".$idgrup_materia."')";
                //echo "<br>".$sql;
                $result=mysql_query($sql);    
                
                }
            else if (($idModul != "") AND ($idGrup != ""))
                {
//                echo "<br>Hem entrat";
                $arrayUfs = array();
                for ($i=0;$i<count($arrayUfs);$i++)
                    {
                    $arrayUfs[$i][0]="";$arrayUfs[$i][1]="";
                    }
                $i = 0;
                // Extreiem les unitats formatives dels móduls eliminnat els "DESD" per no fer massa crides
                //$sql = "SELECT id_ufs FROM moduls_ufs WHERE id_moduls =  '".$idModul."'; ";
                $sql = "SELECT A.id_ufs FROM moduls_ufs A,unitats_formatives B WHERE A.id_moduls =  '".$idModul."' AND ";
                $sql.= "A.id_ufs = B.idunitats_formatives AND B.nom_uf NOT LIKE '%DESD%' ";
                //echo "<br>".$sql;
                $resultat=mysql_query($sql);
                if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                // Repetir+a el bucle per cada Uf d'aquest módul
                while ($fila2 = mysql_fetch_row($resultat))
                    {
                    // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                    $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$fila2[0]."';";
                    //echo "<br>".$sql;
                    $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                    $present=mysql_num_rows($result);
                    $fila3 = mysql_fetch_row($result);$idgrup_materia = $fila3[0];
                    if ($present==1) 
                        {
                        //if ($codiMateria == "VEH001") {echo "<br>Va a desdoblament********************";}
                        $es_nou_grup_materia = 0;
                        //$idgrup_materia = creadesdoblament($idgrup_materia,$idModul,$idProfessor);

                        }
                    if ($present==0)
                       {
                       $es_nou_grup_materia = 1;
                        // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                       // La posteriors comencen d'aquesta data fins a final de curs.
                       if (primera_uf($fila2[0])==1) 
                          { 
                          $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                          $sql.="VALUES ('".$idGrup."','".$fila2[0]."','".$data_inici."','".$data_tmp2."');";
                          //if ($codiMateria == "VEH001") {echo "<br>".$sql;} 
                          }
                       else
                          {
                          $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                          $sql.="VALUES ('".$idGrup."','".$fila2[0]."','".$data_tmp2."','".$data_fi."');";
                          //if ($codiMateria == "VEH001") {echo "<br>>>>>".$sql;} 
                          }

                       $result=mysql_query($sql);if (!$result) {die(_INSERINT_GRUP_MATERIA(3).mysql_error());}
                       $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idGrup."' AND id_mat_uf_pla='".$fila2[0]."';";
                       $result=mysql_query($sql);if (!$result) {die(_CERCANT_GRUP_MATERIA.mysql_error());}
                       $idgrup_materia=mysql_result($result,0);            
                       } 
                    $arrayUfs[$i][0] = $idgrup_materia;
                    $arrayUfs[$i][1] = $es_nou_grup_materia;
                    $i++;

                    }   
                for ($i=0;$i<count($arrayUfs);$i++)
                    {
                    $idgrup_materia = $arrayUfs[$i][0];
                    // Gestionem el professor... o professors ja que en poden haver 2 omés
                    if ($es_nou_grup_materia)
                        {
                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$idgrup_materia."');";
                        //echo "<br>>>>>>>".$sql;
                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}            
                        }
                    else 
                        {
                        // Hem de comprovar que la relació no estigui ja establerta
                        $sql="SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '".$idProfessor."' AND idagrups_materies ='".$idgrup_materia."';";
                        //echo "<br>>>>>>>".$sql;
                        $result=mysql_query($sql);if (!$result) {	die(_SELECT_PROF_GRUP_MATERIA.mysql_error());}
                        if (mysql_num_rows($result) == 0)
                            {
                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$idgrup_materia."');";
                            //echo "<br>>>>>>>".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA(2).mysql_error());}                            
                            }
                        }                   
                    // Generem la unitat classe  
                    $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                    $sql.="('".$idDiaFranja."','".$id_espai."','".$idgrup_materia."')";
//                    echo "<br>".$sql;
                    $result=mysql_query($sql);                     
                    }
                }
            }


    introduir_fase('lessons',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");		
    }}


function crea_horaris_HW_mixt($exportsagaxml,$exporthorarixml) 
    {
    include("../config.php");

    introduir_fase('lessons',0);    
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];


    if (!extreu_fase('segona_carrega'))
        {buidatge('desdecreahoraris');
        //echo "<br>Tot Netejat";
        }

    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);
    
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    $resultatconsulta2=simplexml_load_file($exporthorarixml);
    $resultatconsulta3=simplexml_load_file($exporthorarixml);

    if  ( !$resultatconsulta ) {echo "Carrega fallida Saga >> ".$exportsagaxml;}
    else if ( !$resultatconsulta2 ) {echo "Carrega fallida Horaris >> ".$exporthorarixml;}
    else
        {
        echo "Carregues correctes";

        foreach ($resultatconsulta2->HORARIOS->HORARIOS_PROFESORES -> HORARIO_PROF as $professor)
            {
            //echo "<br> Nombre professor: ".$professor[hor_num_int_pr];
            $prof = $professor[hor_num_int_pr];
            $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$prof);
            //$id_grup = extreu_id('equivalencies','grup_gp','grup_ga',$grup);
            
            foreach ($professor -> ACTIVIDAD as $uniclasse )
                {
                //echo "<br> >>>> Nombre activitat: ".$uniclasse[num_act];
                $materia=$uniclasse[asignatura];
                //$materia=neteja_apostrofs($materia);
                $id_materia=extreu_id('equivalencies','materia_gp','materia_saga',$materia);
                if ($id_materia!="")
                    {$esloe = 1;}
                else
                    {$esloe = 0;
                    $id_materia=extreu_id('materia','codi_materia','idmateria',$materia);
                    }
                foreach ($uniclasse -> GRUPOS_ACTIVIDAD as $grups)
                    {
                    if ($grups[tot_gr_act] ==0)
                        {
                        break;
                        }
                    else if ($grups[tot_gr_act] == 1)
                        {
                        $nom_curt_grup=extreu_grup_HW($exporthorarixml,$grups['grupo_1']);
                        }
                    else
                        {
                        $nombre_grups = $grups[tot_gr_act];
                        
                        $codi_agrupament = "";
                        for ( $i=1; $i<=$nombre_grups ;$i++)
                            {
                            $grup= $grups['grupo_'.$i];
                            if ($grup=="") {break;}
                            $nom_curt_grup=extreu_grup_HW($exporthorarixml,$grup);
                            if ($i == 1) {$codi_agrupament=$nom_curt_grup;}
                            else {$codi_agrupament=$codi_agrupament."_".$nom_curt_grup;}
                            }
                        $nom_curt_grup = $codi_agrupament;
                            
                        }
//                    
                    $id_grup=extreu_id('grups','codi_grup','idgrups',$nom_curt_grup);
                    }
//
                if ($esloe)
                    {
                    //echo "Ha entrat, és un módul del pla ";
                    // ********************************************************
                    // **********  SI ÉS UN MÒDUL DE CCFF  *****************
                    // ******************************************************** 
                    $id_pla=extreu_id('equivalencies','materia_gp','pla_saga',$materia);
                    //echo $id_pla;
                    $sql="SELECT materia_saga FROM equivalencies WHERE materia_gp='".$materia."' AND pla_saga='".$id_pla."';";
                    
                    $resultat=mysql_query($sql);
                    if (!$resultat) {die(SELECT_MATERIA.mysql_error());}
                    $fila=  mysql_fetch_row($resultat);
                    // Treu l'id del módul a gassist
                    $id_materia=$fila[0];

                    //echo "<br>".$classe[id]." ---> ".$professor." >> ".$id_professor." >> ".$materia." >> ".$id_materia." >> ".$grup;

                    if (($id_grup!='') AND ($id_materia!=''))
                       {
                       // Extreiem les unitats formatives dels móduls sense comptar els dedoblaments
                       
                       $sql = "SELECT A.id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '".$id_materia."' ";
                       $sql.= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                       //echo "<br>".$sql;
                       $resultat=mysql_query($sql);

                       if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                       // Repetir+a el bucle per cada Uf d'aquest módul
                       while ($fila=mysql_fetch_row($resultat))
                            {
                            // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                            $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$fila[0]."';";
                            //echo "<br> comprovem si ja existeix".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                            $present=mysql_num_rows($result);
                            if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                            if ($present==0)
                               {
                               $es_nou_grup_materia = 1;
                                // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                               // La posteriors comencen d'aquesta data fins a final de curs.
                               if (primera_uf($fila[0])==1) 
                                  { 
                                  $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                  $sql.="VALUES ('".$id_grup."','".$fila[0]."','".$data_inici."','".$data_tmp2."');";
                                  //echo "<br>      ".$sql;
                                  }
                               else
                                  {
                                  $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                  $sql.="VALUES ('".$id_grup."','".$fila[0]."','".$data_tmp2."','".$data_fi."');";
                                  }

                               $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
                               $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$fila[0]."';";
                               $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                               $idgrup_materia=mysql_result($result,0);
                               //echo "<br>Id grup materia molt abans: ".$idgrup_materia;
                               }
                            // Assignem el profe al grup materia si no existeix ja
//                            echo "<br>Professor: ".$id_professor; 
//                             echo "<br> es nou grup materia?".$es_nou_grup_materia;

//                            echo "<br>======";
//                            echo "<br>materia: ".$id_materia." grup: ".$id_grup." grupmateria: ".$idgrup_materia." professor: ".$id_professor;
                            if ($id_professor!='')
                               {
                                if ($es_nou_grup_materia)
                                    {
                                    $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                    //echo "<br> es nou grup materia i inserim professor".$sql;
                                    $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                    }
                                else 
                                    {
                                    // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
//                                    echo "<br> Si no és nou comencem a comprovar el desdoblament";
                                    $idgrup_materia_original = $idgrup_materia;
                                    $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
//                                    echo "<br>comporvació: ".$comprovacio;
                                    if ( $comprovacio == 1 ) 
                                        { 
                                        //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                        $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                        //echo "<br>Id grup materia després: ".$idgrup_materia;
                                        $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                        //echo "<br>Inserim el nou desdoblament".$sql;
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        }
                                     else
                                        {
                                        $idgrup_materia = $comprovacio;
                                        }
                                    }
                               }		
//                            echo "<br>materia: ".$id_materia." grup: ".$id_grup." grupmateria: ".$idgrup_materia." professor: ".$id_professor;
                            foreach ($resultatconsulta3->DATOS->TRAMOS_HORARIOS->TRAMO as $franges)
                                {
                                if (!strcmp($uniclasse[tramo],$franges[num_tr]))
                                    {
                                    $dia=$franges[numero_dia];
                                    $horainici=$franges[hora_inicio];
                                    $horafi=$franges[hora_final];                                       
                                    $horainici=$horainici.":00";
                                    if(strlen($horainici)== 7) {str_pad($horainici, 8, "0", STR_PAD_LEFT);}
                                    $horafi=$horafi.":00";
                                    if(strlen($horafi)== 7) {str_pad($horafi, 8, "0", STR_PAD_LEFT);}

                                   if(extreu_fase('segona_carrega'))
                                      {
                                      $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                                      $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                                      $franja=mysql_result($result,0);
                                      }                                        

                                   if (($horainici!="") && ($horafi!=""))
                                      {
                                      $id_torn=extreu_id('grups','idgrups','idtorn',$id_grup);

                                      $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                                      $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                                      $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                                      //echo "<br>".$sql;
                                      $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                      $codi_dia_franja=mysql_result($result,0);
                                      }
                                   else 
                                      {
                                      //Per si hi ha torn superposats....
                                      $codi_dia_franja=extreu_codi_franja($dia,$franja,$id_grup);

                                      //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                      }                                       

                                    // Extreiem l'id de l'espai
                                    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$uniclasse[aula]."'; ";
                                    $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                                    $codi_espai=mysql_result($result,0);
                                    if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                                    // Inserim la unitat classe
                                    $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
//                                    echo "<br>".$sql;
                                    $result=mysql_query($sql);                                          

                                    }
                                }
                            }
                        }			
                    }
                        
                else 
                    {
                // Comprovem si és una materia

                    // ********************************************************
                    // **********  SI ÉS UNA MATERIA DE L'ESO  *****************
                    // ********************************************************
                    if ($id_materia!="")
                       {
                       //echo "<br>".$id_grup." >> ".$nom_curt_grup." >> ".$id_materia;
                       if (($nom_curt_grup!='')	AND ($id_materia!=''))
                          {
//                          // Cerquem en la taula grups
//                          $sql="SELECT idgrups FROM grups WHERE codi_grup='".$nom_curt_grup."';";
//                          echo "<br>".$sql;
//                          $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS.mysql_error());}
//                          $idgrup=mysql_result($result,0);
//                          if ($idgrup=='')
//                                {
//                                // Cerquem en la taula equivalencies
//                                $sql="SELECT grup_ga FROM equivalencies WHERE grup_gp='".$grup."';";
//                                
//                                $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
//                                $idgrup=mysql_result($result,0);	
//                                }
                          if ($id_grup!='')
                                {
                                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                                $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$id_materia."';";
                                $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                                $present=mysql_num_rows($result);
                                if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                                if ($present==0)
                                   {
                                   $es_nou_grup_materia = 1;
                                   $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('".$id_grup."','".$id_materia."');";
                                   $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
                                   $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$id_materia."';";
                                   $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                                   $idgrup_materia=mysql_result($result,0);
                                   }
                                // Assignem el profe al grup materia si no existeix ja
                                if ($id_professor!='')
                                   {
                                    if ($es_nou_grup_materia)
                                        {
                                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                        //echo "<br>>>>>>>".$sql;
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        }
                                    else 
                                        {
                                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                        $idgrup_materia_original = $idgrup_materia;
                                        $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                                        if ( $comprovacio == 1 ) 
                                            {
                                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                            $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                                            $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                            //echo "<br>>>>>>>".$sql;
                                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                            }
                                        else
                                           {
                                           $idgrup_materia = $comprovacio;
                                           }                                            
                                        }
//                                   $sql="SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors='".$id_professor."' AND idagrups_materies='".$idgrup_materia."';";
//                                   $result=mysql_query($sql);if (!$result) {die(_ERROR_SELECT_PROF_GRUPS_MAT.mysql_error());}
//                                   if (mysql_num_rows($result)==0)
//                                      {
//                                      $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
//                                      $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
//                                      }
                                   }		
                            foreach ($resultatconsulta3->DATOS->TRAMOS_HORARIOS->TRAMO as $franges)
                                {
                                if (!strcmp($uniclasse[tramo],$franges[num_tr]))
                                    {
                                    $dia=$franges[numero_dia];
                                    $horainici=$franges[hora_inicio];
                                    $horafi=$franges[hora_final];                                       
                                    $horainici=$horainici.":00";
                                    if(strlen($horainici)== 7) {str_pad($horainici, 8, "0", STR_PAD_LEFT);}
                                    $horafi=$horafi.":00";
                                    if(strlen($horafi)== 7) {str_pad($horafi, 8, "0", STR_PAD_LEFT);}

                                    if(extreu_fase('segona_carrega'))
                                          {
                                          $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                                          $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                                          $franja=mysql_result($result,0);
                                          }                                        

                                    if (($horainici!="") && ($horafi!=""))
                                          {
                                          $id_torn=extreu_id('grups','idgrups','idtorn',$id_grup);

                                          $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                                          $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                                          $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                                          //echo "<br>".$sql;
                                          $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                                          $codi_dia_franja=mysql_result($result,0);
                                          }
                                    else 
                                          {
                                          //Per si hi ha torn superposats....
                                          $codi_dia_franja=extreu_codi_franja($dia,$franja,$id_grup);

                                          //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                                          }                                       

                                    // Extreiem l'id de l'espai
                                    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$uniclasse[aula]."'; ";
                                    $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                                    $codi_espai=mysql_result($result,0);
                                    if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                                    // Inserim la unitat classe
                                    $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
                                    //echo "<br>".$sql;
                                    $result=mysql_query($sql);                                          

                                    }
                                }
                                }
                             }			
                          }

                    }
                }
             // Si és un modul de CCFF LOE
            }

        }
//
            foreach ($resultatconsulta2->DATOS -> GRUPOS -> GRUPO as $grupxml)
                {
                // Valorem si es tracta d'una guardia o una tutoria
                // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
                $professor = $grup_xml['num_pr_tutor_principal'];
                $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
               
                $grup_tutor = $grup_xml['num_int_gr']; 
                $id_grup=extreu_id('equivalencies','grup_gp','grup_ga',$grup);
                if (($id_grup != '') AND ($id_professor != ''))
                    {
                    $sql="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$id_professor."' AND idcarrecs='1' AND idgrups='".$id_grup."';";
                    echo "<br>".$sql;
                    $result=mysql_query($sql);	
                    if (!$result) {die(_ERR_SELECT_TUTOR . mysql_error());}
                    if(mysql_num_rows($result)<1)
                        {
                        $sql="INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('".$id_professor."','1','".$id_grup."');";
                        echo "<br>".$sql;
                        $result=mysql_query($sql);	
                        if (!$result) {die(_ERR_ASSIGN_TUTOR . mysql_error());}						
                        }
                    }
                }
    introduir_fase('lessons',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");		
    }
    

function crea_horaris_GP_eso($exportsagaxml,$exporthorarixml) 
	{
	
	include("../config.php");
	
	introduir_fase('lessons',0);
	
	if (!extreu_fase('segona_carrega'))
            {buidatge('desdecreahoraris');}
		
	$sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
	$result=mysql_query($sql);
	$codi_noroom=mysql_result($result,0);
	$resultatconsulta=simplexml_load_file($exportsagaxml);
	$resultatconsulta2=simplexml_load_file($exporthorarixml);
	
	if  ( !$resultatconsulta ) {echo "Carrega fallida Saga >> ".$exportsagaxml;}
	else if ( !$resultatconsulta2 ) {echo "Carrega fallida Horaris >> ".$exporthorarixml;}
	else
		{
		echo "Carregues correctes";
		
		foreach ($resultatconsulta2->lessons->lesson as $classe)
			{
			$professor=$classe->lesson_teacher[id];
			$id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
                        //echo "<br>".$professor." >> ".$id_professor;
		

			$materia=$classe->lesson_subject[id];
			$materia=neteja_apostrofs($materia);
			$id_materia=extreu_id('materia','codi_materia','idmateria',$materia);
			$grup=$classe->lesson_classes[id];
			//echo "<br>Ha entrat".$grup.">>>".$id_materia;
			if (($grup != "") AND ($id_materia != ""))
				{
                                //echo "<br>>>>>>>Ha entrat".$grup.">>>".$materia;
				// Cerquem en la taula grups
				$sql="SELECT idgrups FROM grups WHERE codi_grup='".$grup."';";
				$result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS.mysql_error());}
				$idgrup=mysql_result($result,0);
				if ($idgrup=='')
						{
						
                                                // Cerquem en la taula equivalencies
						$sql="SELECT grup_ga FROM equivalencies WHERE grup_gp='".$grup."';";
						//echo "<br>".$sql;
						$result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
						$idgrup=mysql_result($result,0);	
						}
				if ($idgrup!='')
						{
                                                						
                                                // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
						$sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$id_materia."';";
						$result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
						$present=mysql_num_rows($result);
						if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
						if ($present==0)
							{
							$es_nou_grup_materia = 1;
                                                        $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla) VALUES ('".$idgrup."','".$id_materia."');";
							//echo $sql."<br>";
							$result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
							$sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$id_materia."';";
							$result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
							$idgrup_materia=mysql_result($result,0);
							}
						// Assignem el profe al grup materia si no existeix ja
						if ($id_professor!='')
							{
                                                        if ($es_nou_grup_materia)
                                                            {
                                                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                                            //echo "<br>>>>>>>".$sql;
                                                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                                            }
                                                        else 
                                                            {
                                                            // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                                            $idgrup_materia_original = $idgrup_materia;
                                                            $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                                                            if ($comprovacio == 1 ) 
                                                                {
                                                                //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                                                $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                                                //echo "<br>Id grup materia després: ".$idgrup_materia;
                                                                $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                                                $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                                                //echo "<br>>>>>>>".$sql;
                                                                $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                                                }
                                                            else
                                                               {
                                                               $idgrup_materia = $comprovacio;
                                                               }                                                                
                                                            }
							}		
						foreach ($classe->times->time as $franges)
							{
							//echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
							// Extreiem el codi de la franja/dia
                     $dia=$franges->assigned_day;
                     $franja=$franges->assigned_period;
                     $horainici=$franges->assigned_starttime;
                     $horafi=$franges->assigned_endtime;
                     
                     if(extreu_fase('segona_carrega'))
                        {
                        $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                        $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                        $franja=mysql_result($result,0);
                        }
                      
                     if (($horainici!="") && ($horafi!=""))
                        {
                        $horainici=$horainici*100;
                        $horainici=arregla_hora_gpuntis($horainici);
                        $horafi=$horafi*100;
                        $horafi=arregla_hora_gpuntis($horafi);

                        $id_torn=extreu_id('grups','idgrups','idtorn',$idgrup);

                        $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                        $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                        $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                        //echo "<br>".$sql;
                        $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                        $codi_dia_franja=mysql_result($result,0);
                        }
                     else 
                        {
                        //Per si hi ha torn superposats....
                        $codi_dia_franja=extreu_codi_franja($dia,$franja,$idgrup);
                        //echo "<br>".$dia." >> ".$franja." >> ".$idgrup;
                        //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                        }
                     // Extreiem l'id de l'espai
                    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                    $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                    $codi_espai=mysql_result($result,0);
                    if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                    // Inserim la unitat classe
                    $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
                     //echo "<br>>>>".$sql;
                     $result=mysql_query($sql);if (!$result) {die(INSERT_UNITATS_CLASSE.mysql_error());}
                     
							}
						}
					}			
				}
			foreach ($resultatconsulta2->lessons->lesson as $classe)
				{
				// Valorem si es tracta d'una guardia o una tutoria
				// Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
				$professor=$classe->lesson_teacher[id];
				$id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
				$grup=$classe->lesson_classes[id];
				$id_grup=extreu_id('grups','codi_grup','idgrups',$grup);
				if ($id_grup=='')
					{$id_grup=extreu_id('equivalencies','grup_gp','grup_ga',$grup);}
				$materia=$classe->lesson_subject[id];
				$materia=neteja_apostrofs($materia);
				if (($id_professor!='') AND ($id_grup!='') AND ($materia=='SU_TUT'))
					{
					// Comprovem si no existeix ja
					$sql="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$id_professor."' AND idcarrecs='1' AND idgrups='".$id_grup."';";
					$result=mysql_query($sql);	
					if (!$result) {die(_ERR_SELECT_TUTOR . mysql_error());}
					if(mysql_num_rows($result)<1)
						{
						$sql="INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('".$id_professor."','1','".$id_grup."');";
						$result=mysql_query($sql);	
						if (!$result) {die(_ERR_ASSIGN_TUTOR . mysql_error());}						
						}
					}
				else if (($id_professor!='') AND ($materia=='SU_GU'))
					{
					foreach ($classe->times->time as $franges)
						{
						$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$franges->assigned_day."' AND idfranges_horaries='".$franges->assigned_period."' ";
						$result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
						$codi_dia_franja=mysql_result($result,0);
						// Extreiem l'id de l'espai
						$sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
						$result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
						$codi_espai=mysql_result($result,0);
						if ($codi_espai=="") {$codi_espai=$codi_noroom;}
						// Inserim la unitat classe
						$sql="INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('".$id_professor."','".$codi_dia_franja."','".$codi_noroom."')";
						//echo "<br>".$sql;
						$result=mysql_query($sql);if (!$result) {die(ERROR_INTRO_GUARDIES.mysql_error());}	
						}
					}		

				}
			}
			introduir_fase('lessons',1);
			$page = "./menu.php";
			$sec="0";
			header("Refresh: $sec; url=$page");		
		}
		


function crea_horaris_PN_eso($exportsagaxml,$exporthorarixml) 
    {
	
    include("../config.php");

    introduir_fase('lessons',0);

    // Extreiem data inici i data fi dels periodes escolars
    $sql="SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S';";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DATES.mysql_error());}
    $fila=mysql_fetch_row($result);
    $data_inici=$fila[0];
    $data_fi=$fila[1];

    if (!extreu_fase('segona_carrega'))
        {buidatge('desdecreahoraris');}

    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);
    // S'han de fer dos recorregut paral.lels per això es crida dues vegades
    $resultatconsulta=simplexml_load_file($exporthorarixml);
    $resultatconsulta2=simplexml_load_file($exporthorarixml);
    if ( !$resultatconsulta ) {echo "Carrega fallida";}
    else 
        {
        
        foreach ($resultatconsulta->sesionesLectivas->sesion as $franja)
            {
            //echo "<br>===============================";
            $sessio=$franja[id];
            $materia=$franja->materia;
            $id_materia=extreu_id('materia','codi_materia','idmateria',$materia);

            //Peñalara 2008
            //$grup=neteja_item_grup_materia($franja->grupo);
            //$grup=$franja->grupo;
            // peñalara posterior 2008
            $grup=neteja_item_grup_materia($franja->grupoMateria);
            $id_grup=extreu_id('grups','codi_grup','idgrups',$grup);

            //Hem de mirar el grup a equivalencies i sinó a grups
            if ($id_grup=='')
                {$id_grup=extreu_id('equivalencies','grup_gp','grup_ga',$grup);}

                        //Si tots dos existeixen, seguim endavant
            //echo "<br>Materia - grup :".$id_materia." - ".$materia." - ".$id_grup." - ".$grup;
            if (($id_materia!='') AND ($id_grup!=''))
                {
                // Hem de comprovar si aquest grup-materia ja existeix.
                $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$id_materia."';";
                //echo "<br> Nova".$sql;
                $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA.mysql_error());}
                $present=mysql_num_rows($result);
                if ($present==1) {$id_grup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                if ($present==0)
                   {
                    $es_nou_grup_materia = 1;
                    $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                    $sql.="VALUES ('".$id_grup."','".$id_materia."','".$data_inici."','".$data_fi."');";
                    
                    $result=mysql_query($sql);if (!$result) {die(INSERT_GRUP_MATERIA.mysql_error());}

                    $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$id_materia."';";
                    $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA.mysql_error());}
                    $id_grup_materia=mysql_result($result,0);
                    //echo "<br> NoNova".$sql;
                    //echo "<br> id grup materia".$id_grup_materia;
                    }
                foreach ($resultatconsulta2->horario->tramo as $horari)
                    {
                    // A Peñalara els dies i franges comencen per 0, però l'aplicació comença a pintar els horaris dels diferents dies per 1
                    // es fa la modificació per tal que em doni la dada correctament
                    $dia=$horari[dia]+1;
                    $franja=$horari[indice]+1;
                    //echo "<br>>>>>>".$horari[dia]." >> ".$horari[indice];    
                    if(extreu_fase('segona_carrega'))
                    {
                    $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                    $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                    $franja=mysql_result($result,0);
                    }

                    $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                    $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                    $codi_dia_franja=mysql_result($result,0);
                    foreach($horari->aula as $horari2)
                        {
                        $sessio2=$horari2->sesion;
                        //echo "<br>Segona lectura de sessio".$sessio2." >> ".$sessio;
                        if (!strcmp($sessio2,$sessio))
                            {
                            $espai=$horari2[id];
                            $professor=$horari2->profesor;
                            // Assignem al professor al seu grup matèria
                            //Treurem l'id del professor de la taual d'equivalències
                            $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
                            //echo "<br> Professor:".$professor." >> ".$id_professor;
                            // Inserim el registre que relaciona professor i grup materia
                            if ($id_professor!='')
                                    {
                                    if ($es_nou_grup_materia)
                                        {
                                        // Comprovem que aquest emparellament no existeix ja
                                        $sql = "SELECT COUNT(*) FROM prof_agrupament WHERE idprofessors = '".$id_professor."' AND ";
                                        $sql .= "idagrups_materies = '".$id_grup_materia."';";
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        $fila4 = mysql_fetch_row($result);
                                        if ($fila4[0] == 0)
                                            {
                                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$id_grup_materia."');";
                                            //echo "<br>> NOU >>>>>".$sql;
                                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA2.mysql_error());}
                                            }
                                        }
                                    else 
                                        {
                                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                        $id_grup_materia_original = $id_grup_materia;
                                        $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                                        if ($comprovacio == 1 ) 
                                            {
                                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                            $id_grup_materia = treu_darrer_desdoblament($id_grup_materia_original);
                                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                                            $id_grup_materia = creadesdoblament($id_grup_materia,$id_materia);
                                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$id_grup_materia."');";
                                            //echo "<br>> DESDOBLAMENT >>>>>".$sql;
                                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                            }
                                        else
                                           {
                                           $idgrup_materia = $comprovacio;
                                           }                                            
                                        }
                                    }		
                            //echo "<br>>>".$codi_dia_franja." >> ".$dia." >>>>".$hora."<br>";		
                            // Extreiem l'id de l'espai
                            $sql="SELECT idespais_centre FROM espais_centre WHERE descripcio='".$espai."'; ";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                            $codi_espai=mysql_result($result,0);
                            if ($codi_espai=="") {$codi_espai=$codi_noroom;}

                            $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$id_grup_materia."')";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {die(INSERT_UNITAT_CLASSE.mysql_error());}	

                            }
                        }
                    }
                }
            }

        }
     	// Crearem les guardies
	$resultatconsulta2=simplexml_load_file($exporthorarixml);
        foreach ($resultatconsulta2->horario->tramo as $horari)
            {
            $dia=$horari[dia]+1;
            $franja=$horari[indice]+1;
            $franja_tmp=$franja;
            //echo "<br>".$dia.">>>".$franja;

            if(extreu_fase('segona_carrega'))
                {
                $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;

                $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                $franja=mysql_result($result,0);
                if ($franja=="") {$franja=$franja_tmp;}
                //echo "<br>franja -->".$franja;
                }			
            $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."'; ";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
            $codi_dia_franja=mysql_result($result,0);
            //echo "<br>".$codi_dia_franja;
            if ($codi_dia_franja!="")
                {
                foreach($horari-> guardia as $guardia)
                   {
                   $professor=$guardia->profesor;
                   //echo "<br>".$professor." >>> ".$accio;
                   //Treurem l'id del professor de la taual d'equivalències
                   $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
                   if ($id_professor!='')
                      {
                      $sql="INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('".$id_professor."','".$codi_dia_franja."','".$codi_noroom."')";
                      //echo "<br>".$sql;
                      $result=mysql_query($sql);if (!$result) {die(ERROR_INTRO_GUARDIES.mysql_error());}
                      }
                   }
                }   
		
            }
    introduir_fase('lessons',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");		
    }
	

function crea_horaris_gp_ccff($exportsagaxml,$exporthorarixml) 
    {
    include("../config.php");

    introduir_fase('lessons',0);    
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];
   
    if (!extreu_fase('segona_carrega'))
      {buidatge('desdecreahoraris');}
	
	
    $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
    $result=mysql_query($sql);
    $codi_noroom=mysql_result($result,0);
	
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    $resultatconsulta2=simplexml_load_file($exporthorarixml);
	
    if  ( !$resultatconsulta ) {echo "Carrega fallida Saga >> ".$exportsagaxml;}
    else if ( !$resultatconsulta2 ) {echo "Carrega fallida Horaris >> ".$exporthorarixml;}
    else
	{
	echo "Carregues correctes";
		
	foreach ($resultatconsulta2->lessons->lesson as $classe)
            {
            $professor=$classe->lesson_teacher[id];
            $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);

            $materia=$classe->lesson_subject[id];
            $materia=neteja_apostrofs($materia);
            $grup=$classe->lesson_classes[id];
            // Si consultes a equivalencies però hi ha diferents M01 que pertanyen a diferents plans d'estudis
            // pot provocar que no es carregui el módul corresponent
            // S'ha de vincular l'id materia al pla d'estudis
            //$id_materia=extreu_id('equivalencies','materia_gp','materia_saga',$materia);
            //Primer hem d'extreure el pla d'estudis en funció de la classe de la taula d'equivalències
            $id_pla=extreu_id('equivalencies','grup_gp','pla_saga',$grup);
         
            $sql="SELECT materia_saga FROM equivalencies WHERE materia_gp='".$materia."' AND pla_saga='".$id_pla."';";
            //$sql="SELECT materia_saga,pla_saga FROM equivalencies WHERE materia_gp='". $materia."';";
            //echo "<br>".$sql;
            $resultat=mysql_query($sql);
            if (!$resultat) {die(SELECT_MATERIA.mysql_error());}
            $fila=  mysql_fetch_row($resultat);
            $id_materia=$fila[0];

            //echo "<br>".$classe[id]." ---> ".$professor." >> ".$id_professor." >> ".$materia." >> ".$id_materia." >> ".$grup;
         
	if (($grup!='') AND ($id_materia!=''))
            {
            // Extreiem les unitats formatives dels móduls
//            $sql="SELECT id_ufs FROM moduls_ufs WHERE id_moduls='".$id_materia."';";
           $sql = "SELECT A.id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '".$id_materia."' ";
           $sql.= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
            //echo "<br>".$sql;
            $resultat=mysql_query($sql);
         
            if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
            // Repetir+a el bucle per cada Uf d'aquest módul
            while ($fila=mysql_fetch_row($resultat))
               {
               // Cerquem en la taula grups
               $sql="SELECT idgrups FROM grups WHERE codi_grup='".$grup."';";
               $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS.mysql_error());}
               $idgrup=mysql_result($result,0);
               if ($idgrup=='')
                     {
                     // Cerquem en la taula equivalencies
                     $sql="SELECT grup_ga FROM equivalencies WHERE grup_gp='".$grup."';";
                     //echo "<br>".$sql;
                     $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
                     $idgrup=mysql_result($result,0);	
                     }
               if ($idgrup!='')
                     {
                     // Afegit per fe comprovacions
                     $sql="SELECT nom_uf,codi_uf FROM unitats_formatives WHERE idunitats_formatives='".$fila[0]."';";
                     //echo "<br>".$sql;
                     $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                     $fila2=  mysql_fetch_row($result);
                     //print("<br> dades uf: ".$fila2[0]."  -  ".$fila2[1]);
    
                     // Comprovem si el grup matèria ja està donat d'alta i si ho està extreiem l'id. Si no hi és, l'introduim
                     $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$fila[0]."';";
                     //echo "<br>".$sql;
                     $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                     $present=mysql_num_rows($result);
                     if ($present==1) {$idgrup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                     if ($present==0)
                        {
                        $es_nou_grup_materia = 1; 
                        // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                        // La posteriors comencen d'aquesta data fins a final de curs.
                        if (primera_uf($fila[0])==1) 
                           { 
                           $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                           $sql.="VALUES ('".$idgrup."','".$fila[0]."','".$data_inici."','".$data_tmp2."');";
                           //echo "<br>      ".$sql;
                           }
                        else
                           {
                           $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                           $sql.="VALUES ('".$idgrup."','".$fila[0]."','".$data_tmp2."','".$data_fi."');";
                           }
                        
                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_GRUP_MATERIA.mysql_error());}
                        $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$idgrup."' AND id_mat_uf_pla='".$fila[0]."';";
                        $result=mysql_query($sql);if (!$result) {	die(_CERCANT_GRUP_MATERIA.mysql_error());}
                        $idgrup_materia=mysql_result($result,0);
                        }
                     // Assignem el profe al grup materia si no existeix ja
                     if ($id_professor!='')
                        {
                        if ($es_nou_grup_materia)
                            {
                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                            //echo "<br>>>>>>>".$sql;
                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                            }
                        else 
                            {
                            // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                            $idgrup_materia_original = $idgrup_materia;
                            $comprovacio = comprova_desdoblament($id_professor,$idgrup_materia);
                            if ($comprovacio == 1 ) 
                                {
                                //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                $idgrup_materia = treu_darrer_desdoblament($idgrup_materia_original);
                                //echo "<br>Id grup materia després: ".$idgrup_materia;
                                $idgrup_materia = creadesdoblament($idgrup_materia,$id_materia);
                                $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$idgrup_materia."');";
                                //echo "<br>>>>>>>".$sql;
                                $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                }
                             else
                                {
                                $idgrup_materia = $comprovacio;
                                }                                
                            }
                }		
                     foreach ($classe->times->time as $franges)
                        {
                        //echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
                        // Extreiem el codi de la franja/dia
                        $dia=$franges->assigned_day;
                        $franja=$franges->assigned_period;
                        $franja_xml=$franges->assigned_period;
                        $horainici=$franges->assigned_starttime;
                        $horafi=$franges->assigned_endtime;

                        if(extreu_fase('segona_carrega'))
                           {
                           $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
                           $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                           $franja_xml=mysql_result($result,0);
                           }

                     if (($horainici!="") && ($horafi!=""))
                        {
                        $horainici=$horainici*100;
                        $horainici=arregla_hora_gpuntis($horainici);
                        $horafi=$horafi*100;
                        $horafi=arregla_hora_gpuntis($horafi);

                        $id_torn=extreu_id('grups','idgrups','idtorn',$idgrup);

                        $sql="SELECT A.id_dies_franges FROM dies_franges A, franges_horaries B WHERE ";
                        $sql.="A.iddies_setmana='".$dia."' AND B.hora_inici='".$horainici."' AND B.hora_fi='".$horafi."' ";
                        $sql.="AND A.idfranges_horaries=B.idfranges_horaries AND B.idtorn='".$id_torn."'; ";
                        //echo "<br>".$sql;
                        $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                        $codi_dia_franja=mysql_result($result,0);
                        }
                     else 
                        {
                        //Per si hi ha torn superposats....
                        // echo $dia." >> ".$franja." >> ".$grup;
                        $codi_dia_franja=extreu_codi_franja($dia,$franja,$idgrup);

                        //$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                        }
                        // Extreiem l'id de l'espai
                        $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                        $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                        $codi_espai=mysql_result($result,0);
                        if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                        // Inserim la unitat classe
                        $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idgrup_materia."')";
                        $result=mysql_query($sql);
                        //echo "<br>".$sql;
                        }
                     }
                  }			
               }
            }
	foreach ($resultatconsulta2->lessons->lesson as $classe)
            {
            // Valorem si es tracta d'una guardia o una tutoria
            // Si fossin guardia  d'aula(SU_GU) o Tutoria de grup (SU_TUT) s'introduirien en les taules corresponents
            $professor=$classe->lesson_teacher[id];
            $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
            $grup=$classe->lesson_classes[id];
            $id_grup=extreu_id('grups','codi_grup','idgrups',$grup);
            if ($id_grup=='')
                    {$id_grup=extreu_id('equivalencies','grup_gp','grup_ga',$grup);}
            $materia=$classe->lesson_subject[id];
            $materia=neteja_apostrofs($materia);
            if (($id_professor!='') AND ($id_grup!='') AND ($materia=='SU_TUT'))
                    {
                    // Comprovem si no existeix ja
                    $sql="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$id_professor."' AND idcarrecs='1' AND idgrups='".$id_grup."';";
                    $result=mysql_query($sql);	
                    if (!$result) {die(_ERR_SELECT_TUTOR . mysql_error());}
                    if(mysql_num_rows($result)<1)
                            {
                            $sql="INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups) VALUES ('".$id_professor."','1','".$id_grup."');";
                            $result=mysql_query($sql);	
                            if (!$result) {die(_ERR_ASSIGN_TUTOR . mysql_error());}						
                            }
                    }
            else if (($id_professor!='') AND ($materia=='SU_GU'))
                    {
                    foreach ($classe->times->time as $franges)
                            {
                            $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$franges->assigned_day."' AND idfranges_horaries='".$franges->assigned_period."' ";
                            $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                            $codi_dia_franja=mysql_result($result,0);
                            // Extreiem l'id de l'espai
                            $sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='".$franges->assigned_room[id]."'; ";
                            $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                            $codi_espai=mysql_result($result,0);
                            if ($codi_espai=="") {$codi_espai=$codi_noroom;}
                            // Inserim la unitat classe
                            $sql="INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('".$id_professor."','".$codi_dia_franja."','".$codi_noroom."')";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);if (!$result) {die(ERROR_INTRO_GUARDIES.mysql_error());}	
                            }
                    }		
            }
        }
        introduir_fase('lessons',1);
        $page = "./menu.php";
        $sec="0";
        header("Refresh: $sec; url=$page");		
    }
   

function primera_uf($id_uf)
   {
   include("../config.php");
	
   // Aquesta funció retorna el darrer nombre del codi de la unitat formativa per saber
   // Si és la primera uf d'un módul
   
//	$conexion=mysql_connect(localhost,$_USR_GASSIST,$_PASS_GASSIST);
//	$db=mysql_select_db($_BD_GASSIST,$conexion);
//	mysql_set_charset("utf8");
   
   $sql="SELECT codi_uf FROM unitats_formatives WHERE idunitats_formatives='".$id_uf."';";
   $result=mysql_query($sql);if (!$result) {die(SELECT_FIRST_UF.mysql_error());}
	$fila=mysql_fetch_row($result);
        //echo "<br>".$fila[0];
	return substr($fila[0],-1);
   }
      
      
function crea_horaris_PN_ccff($exportsagaxml,$exporthorarixml) 
	{
	
    include("../config.php");

    introduir_fase('lessons',0);    
    
    $dates = treuDatesUnitatsFormatives();
    $data_inici = $dates[0];
    $data_tmp2 = $dates[1];
    $data_fi = $dates[2];
		
	if (!extreu_fase('segona_carrega'))
            {buidatge('desdecreahoraris');}
	
	$sql="SELECT idespais_centre FROM espais_centre WHERE codi_espai='NOROOM'; ";
	$result=mysql_query($sql);
	$codi_noroom=mysql_result($result,0);
	// S'han de fer dos recorregut paral.lels per això es crida dues vegades

        $resultatconsulta=simplexml_load_file($exporthorarixml);
	$resultatconsulta2=simplexml_load_file($exporthorarixml);
     
        if ( !$resultatconsulta ) {echo "Carrega fallida";}
	else 
            {
            //echo "Carrega correcta ghc";
            foreach ($resultatconsulta->sesionesLectivas->sesion as $franja)
            	{
		$sessio=$franja[id];
                $materia=$franja->materia;
                $grup=neteja_item_grup_materia($franja->grupoMateria);
                // Extreiem l'id del módul i l'id del pla d'estudis
                $sql="SELECT grup_ga,pla_saga FROM equivalencies WHERE grup_gp='".$grup."'";
                //echo "<br>".$sql;
                $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                $fila=mysql_fetch_row($result);
                $id_grup=$fila[0];
                $id_pla=$fila[1];

                // Amb l'id del pla d'estudis i la materia, podem treure l'id real del mòdul
                $sql="SELECT materia_saga FROM equivalencies WHERE pla_saga='".$id_pla."' AND materia_gp='".$materia."'";
                $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                $fila=mysql_fetch_row($result);
                $id_modul=$fila[0];

                //echo "<br>".$sessio." >>>".$grup." ".$id_pla." ".$id_modul."<br>";;

                // per cada unitat formativa el módul, hem de fer tot el procés
               $sql = "SELECT A.id_ufs FROM moduls_ufs A, unitats_formatives B WHERE A.id_moduls =  '".$id_modul."' ";
               $sql.= "AND B.codi_UF NOT LIKE  '%DESD%' AND A.id_ufs = B.idunitats_formatives;";
                $resultat=mysql_query($sql);

                if (!$resultat) {die(SELECT_GRUP_MATERIA2.mysql_error());}
                while ($fila=mysql_fetch_row($resultat))
                    {
                    // Comprovem si aquest binomi grup materia ja existeix
                    $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$fila[0]."';";
                    //echo "<br>".$sql;
                    $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA.mysql_error());}
                    $present=mysql_num_rows($result);
                    if ($present==1) {$id_grup_materia=mysql_result($result,0);$es_nou_grup_materia = 0;}
                    if ($present==0)
                       {
                       $es_nou_grup_materia = 1;
                       // Si es tracta de la primera Uf , aquesta acaba 90 dies després
                       // La posteriors comencen d'aquesta data fins a final de curs.
                       if (primera_uf($fila[0])==1) 
                          { 
                          $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                          $sql.="VALUES ('".$id_grup."','".$fila[0]."','".$data_inici."','".$data_tmp2."');";
                          }
                       else
                          {
                          $sql="INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                          $sql.="VALUES ('".$id_grup."','".$fila[0]."','".$data_tmp2."','".$data_fi."');";
                          }               
                       //echo $sql;
                       $result=mysql_query($sql);if (!$result) {die(INSERT_GRUP_MATERIA2.mysql_error());}    

                       // Extreiem l'identificador d'aquest grup materia
                       $sql="SELECT idgrups_materies FROM grups_materies WHERE id_grups='".$id_grup."' AND id_mat_uf_pla='".$fila[0]."';";
                       //echo "<br>".$sql;
                       $result=mysql_query($sql);if (!$result) {die(SELECT_GRUP_MATERIA.mysql_error());}
                       $id_grup_materia=mysql_result($result,0);
                       }
                    // Ja sabem que la uf està introduida i que només hi és una vegada   
                    // Ara hem d'introuir el grup matèria
                    foreach ($resultatconsulta2->horario->tramo as $horari)
                        {
                        //echo "<br>Ha entrat 1739";
                        // A Peñalara els dies i franges comencen per 0, però l'aplicació comença a pintar els horaris dels diferents dies per 1
                        // es fa la modificació per tal que em doni la dada correctament
                        $dia=$horari[dia]+1;
                        $franja=$horari[indice]+1;
                        $franja_tmp=$franja;
                        //echo "<br>franja -->".$franja;

                        if(extreu_fase('segona_carrega'))
                            {
                            $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;

                            $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
                            $franja=mysql_result($result,0);
                            if ($franja=="") {$franja=$franja_tmp;}
                            //echo "<br>franja -->".$franja;
                            }

                        $sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."' ";
                        //echo "<br>".$sql;
                        $result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
                        $codi_dia_franja=mysql_result($result,0);

                        foreach($horari->aula as $horari2)
                            {
                            $sessio2=$horari2->sesion;
                            //echo "<br>Segona lectura de sessio".$sessio2;
                            if (!strcmp($sessio2,$sessio))
                                {
                                $espai=$horari2[id];
                                $professor=$horari2->profesor;
                                // Assignem al professor al seu grup matèria
                                //Treurem l'id del professor de la taual d'equivalències
                                $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);

                                // Inserim el registre que relaciona professor i grup materia
                                if ($id_professor!='')
                                    {
                                    if ($es_nou_grup_materia)
                                        {
                                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$id_grup_materia."');";
                                        //echo "<br>>>>>>>".$sql;
                                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                        }
                                    else 
                                        {
                                        // Comprovem si existeix ja una assignació d'aquest grup matèria per si fos un desdoblament
                                        $id_grup_materia_original = $id_grup_materia;
                                        $comprovacio = comprova_desdoblament($id_professor,$id_grup_materia);
                                        if ($comprovacio == 1 ) 
                                            {
                                            //echo "<br>Id grup materia abans: ".$idgrup_materia_original;
                                            $id_grup_materia = treu_darrer_desdoblament($id_grup_materia_original);
                                            //echo "<br>Id grup materia després: ".$idgrup_materia;
                                            $id_grup_materia = creadesdoblament($id_grup_materia,$id_modul);
                                            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$id_professor."','".$id_grup_materia."');";
                                            //echo "<br>>>>>>>".$sql;
                                            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
                                            }
                                        else
                                           {
                                           $idgrup_materia = $comprovacio;
                                           }                                            
                                        }
                                    }		



                                //echo "<br>>>".$codi_dia_franja." >> ".$dia." >>>>".$hora."<br>";		
                                // Extreiem l'id de l'espai
                                $sql="SELECT idespais_centre FROM espais_centre WHERE descripcio='".$espai."'; ";
                                //echo "<br>".$sql;
                                $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
                                $codi_espai=mysql_result($result,0);
                                if ($codi_espai=="") {$codi_espai=$codi_noroom;}

                                $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$id_grup_materia."')";
                                //echo $sql;
                                $result=mysql_query($sql);;if (!$result) {die(INSERT_UNITAT_CLASSE.mysql_error());}	

                                }
                            }
                        }
            }
         }
			
      }
     	// Crearem les guardies
		foreach ($resultatconsulta2->horario->tramo as $horari)
			{
			$dia=$horari[dia]+1;
			$franja=$horari[indice]+1;
         $franja_tmp=$franja;
         //echo "<br>franja -->".$franja;

         if(extreu_fase('segona_carrega'))
            {
            $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;

            $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
            $franja=mysql_result($result,0);
            if ($franja=="") {$franja=$franja_tmp;}
            //echo "<br>franja -->".$franja;
            }			
			$sql="SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."'; ";
			//echo "<br>".$sql;
			$result=mysql_query($sql);if (!$result) {die(Select_id_dia_franja.mysql_error());}
			$codi_dia_franja=mysql_result($result,0);
         if ($codi_dia_franja!="")
            {
            foreach($horari->guardia as $guardia)
               {
               $professor=$guardia->profesor;
               //Treurem l'id del professor de la taual d'equivalències
               $id_professor=extreu_id('equivalencies','codi_prof_gp','prof_ga',$professor);
               if ($id_professor!='')
                  {
                  $sql="INSERT INTO guardies(idprofessors, id_dies_franges,idespais_centre) VALUES ('".$id_professor."','".$codi_dia_franja."','".$codi_noroom."')";
                  //echo "<br>".$sql;
                  $result=mysql_query($sql);if (!$result) {die(ERROR_INTRO_GUARDIES.mysql_error());}
                  }
               }
            }   
		
			}
    introduir_fase('lessons',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");		
      }
	



?>