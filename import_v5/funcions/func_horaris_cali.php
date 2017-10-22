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
// 					CREACIÓ D'HORARIS INSTITUT CAL.LIPOLIS
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function genera_horaris_cali()
    {
    include("../config.php");
    
    $sql = "SELECT data_inici,data_fi FROM periodes_escolars WHERE actual = 'S';";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_INSERT_PROF_GRUPMATERIA . mysql_error());}
    $fila = mysql_fetch_row($result);
    $dataInici = $fila[0];
    $dataFi = $fila[1];
    
    $sql = "SELECT idcarrecs FROM carrecs WHERE nom_carrec = 'TUTOR';";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_EXTRACT_CARREC . mysql_error());}
    $fila = mysql_fetch_row($result);$idCarrec = $fila[0];
    
    $exporthorarixml=$_SESSION['upload_horaris'];
    $resultatconsulta=simplexml_load_file($exporthorarixml);
    
    
    
    if ( !$resultatconsulta ) {echo "Carrega Horaris fallida";}
    else 
        {
        // Fem un recorregut de les lessons.
        // Per cada lesson generem tot
        foreach($resultatconsulta->lessons->lesson as $lesson)
            {
            
            $materia = neteja_apostrofs($lesson->lesson_subject[id]);
            $array1 = explode("-",$materia);
            $array2 = explode("_",$array1[0]);
            if ($array1[1]=='Tutoria') {$tutoria = true;}
            else {$tutoria = false;}
            
            $grup = $array2[1];
            
            // Si les tres primer lletres son CAR, afecgir fins que sigui CARAC
            if (substr($grup,0,3)=='CAR') {$grup=substr($grup,0,3).'AC'.substr($grup,3,strlen($grup)-3);}
            
            $idGrup=extreu_id('grups','nom','idgrups',$grup);
            $array3 = explode(" ",extreu_id('grups','nom','codi_grup',$grup));
            $acronimPla = $array3[1];
            
            // Per solucionar CAS
            if ($array3[1]=='') {$acronimPla = $array3[0];}
            
            $idPla = extreu_id('equivalencies','grup_ga','pla_saga',$idGrup);
            $esLoe = esbrinaLOE_cali($acronimPla);
            // Si no és una materia LOE



            if ($idGrup!='')
                {
                
                if ($esLoe == 0)
                    {
                    if ($tutoria) 
                        {
                        $materia = genera_tutoria($dataInici,$dataFi,$idPla,$acronimPla,$esLoe);

                        $idProfessor = extreu_id('equivalencies','codi_prof_gp','prof_ga',$lesson->lesson_teacher[id]);
                        if ($idProfessor != '')
                            {
                            $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) VALUES ('".$idProfessor."','".$idCarrec."','".$idGrup."',1);";
                            $result=mysql_query($sql);
                            if (!$result) {die(_ERR_INSERT_TUTOR . mysql_error());}                        
                            }
                        }
                    $idGrupMateria = generaGrupMateriaNoLoe($materia,$idGrup,$acronimPla,$idPla,$dataInici,$dataFi,$tutoria);
                    //if ($tutoria) {echo "<br>".$materia." >> ".$idGrupMateria;}
                    if ($lesson->lesson_teacher[id] != "")
                        {
                        $nouGrupMateriaDesdoblat = assigna_profe($idGrupMateria,$lesson->lesson_teacher[id]);
                        if ($nouGrupMateriaDesdoblat != $idGrupMateria) 
                            {
                            //echo "<br>".$idGrupMateria." >> ".$nouGrupMateriaDesdoblat;
                            $idGrupMateria = $nouGrupMateriaDesdoblat;
                            }
                        }    
                            
                    creaHorari($lesson,$idGrupMateria,$idGrup);
                         
                    }
                else
                    {
                    
                    if ($tutoria) 
                        {
                        $materia = genera_tutoria($dataInici,$dataFi,$idPla,$acronimPla,$esLoe);
                        // Extreiem l'id de la uf
                        $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$materia."';";
//                        echo "<br>...".$sql2;
                        $result2=mysql_query($sql2);
                        //echo "<br>".$sql2;
                        if (!$result2) {die(_ERR_SELECT_ID_MATERIA . mysql_error());}
                        $fila2 = mysql_fetch_row($result2);
                        $idUf=$fila2[0];
                        // Crearem el grup_materia i n'extreuirem l'id
                        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                        $sql2.= "VALUES ('".$idGrup."','".$idUf."','".$dataInici."','".$dataFi."');";
//                        echo "<br>...".$sql2;
                        $result2=mysql_query($sql2);
                        if (!$result2) {die(_ERR_INSERT_GRUPMATERIA . mysql_error());}

                        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '".$idGrup."' AND id_mat_uf_pla = '".$idUf."';";
//                        echo "<br>...".$sql2;
                        $result2=mysql_query($sql2);
                        if (!$result2) {die(_ERR_SELECT_ID_GRUPMATERIA . mysql_error());}
                        $fila2 = mysql_fetch_row($result2);                        
                        
                        creaHorari($lesson,$fila2[0],$idGrup);

                        $idProfessor = extreu_id('equivalencies','codi_prof_gp','prof_ga',$lesson->lesson_teacher[id]);
                        if ($idProfessor != '')
                            {
                            $sql = "INSERT INTO professor_carrec(idprofessors,idcarrecs,idgrups,principal) VALUES ('".$idProfessor."','".$idCarrec."','".$idGrup."',1);";
                            $result=mysql_query($sql);
                            if (!$result) {die(_ERR_INSERT_TUTOR . mysql_error());}                        
                            }
                        
                        }
                    else 
                        {
                        $modul= str_pad($array1[1],3, "0", STR_PAD_LEFT);
                        //echo "<br>Mòdul: ".$modul;
                        if ($array1[2]!='') 
                            {
                            $unitatsFormatives = str_split($array1[2]);
                            for ($i=0 ; $i < count($unitatsFormatives) ; $i++) 
                                {
                                $unitatsFormatives[$i] = str_pad($unitatsFormatives[$i],2, "0", STR_PAD_LEFT);
                                $unitatsFormatives[$i] = $acronimPla."_".$modul.$unitatsFormatives[$i];
                                $unitatsFormatives[$i]   = extreu_id('unitats_formatives','codi_uf','idunitats_formatives',$unitatsFormatives[$i]);
                                }

                            }
                        else
                            {
                            unset($unitatsFormatives);
                            $sql = "SELECT idmoduls FROM moduls WHERE idplans_estudis='".$idPla."' AND codi_modul='".$modul."';";
                            $result=mysql_query($sql);if (!$result) {die(SELECT_ID_MODULS.mysql_error());}
                            $fila = mysql_fetch_row($result);$idModul = $fila[0];

                            $sql = "select id_ufs FROM moduls_ufs WHERE id_moduls = '".$idModul."';";
                            $result=mysql_query($sql);if (!$result) {die(SELECT_ID_UFS.mysql_error());}
                            $i=0;
                            while($fila = mysql_fetch_row($result))
                                {
                                $unitatsFormatives[$i] = $fila[0];
                                $i++;
                                }   
                            }
                        //foreach ($unitatsFormatives as $ufs) {echo "<br>...".$ufs;}
                        for ($i=0; $i < count($unitatsFormatives); $i++ )
                            {
                            if ($unitatsFormatives[$i] != '')
                                {
                                $idGrupMateria = generaGrupMateriaLoe($i,$unitatsFormatives[$i],$idGrup,$acronimPla,$idPla,$dataInici,$dataFi);
                                if ($lesson->lesson_teacher[id] != "")
                                    {
                                    $nouGrupMateriaDesdoblat = assigna_profe($idGrupMateria,$lesson->lesson_teacher[id]);
                                    if ($nouGrupMateriaDesdoblat != $idGrupMateria) 
                                        {
                                        //echo "<br>".$idGrupMateria." >> ".$nouGrupMateriaDesdoblat;
                                        $idGrupMateria = $nouGrupMateriaDesdoblat;
                                        }
                                    creaHorari($lesson,$idGrupMateria,$idGrup);                    
                                    }                        
                                }    

                            }

                        }

                    }
                }
            }        
            
        }
introduir_fase('lessons',1);
$page = "./menu.php";
$sec="0";
header("Refresh: $sec; url=$page");		
}

function creaHorari($classe,$idGrupMateria,$idgrup)
    {
    foreach ($classe->times->time as $franges)
        {
	//echo "<br>",$id_gp_materia." >> ".$codi_gp_materia;
	// Extreiem el codi de la franja/dia
        $dia=$franges->assigned_day;
        $franja=$franges->assigned_period;
        $horainici=$franges->assigned_starttime;
        $horafi=$franges->assigned_endtime;
//        if(extreu_fase('segona_carrega'))
//               {
//            $sql="SELECT id_taula_franges FROM franges_tmp WHERE id_xml_horaris='".$franja."';" ;
//            $result=mysql_query($sql);if (!$result) {die(Select_franja.mysql_error());}
//            $franja=mysql_result($result,0);
//            }

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
            //echo "<br>".$dia." >> ".$franja." >> ".$idgrup." >> ".$codi_dia_franja;
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
        if ($codi_espai=="") 
            {
            $sql="SELECT idespais_centre FROM espais_centre WHERE descripcio='NOROOM'; ";
            $result=mysql_query($sql);if (!$result) {die(Select_id_espai_centre.mysql_error());}
            $codi_espai=mysql_result($result,0);            
                      
            }
        // Inserim la unitat classe
        $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ('".$codi_dia_franja."','".$codi_espai."','".$idGrupMateria."')";
         //echo "<br>>>>".$sql;
        $result=mysql_query($sql);if (!$result) {die(INSERT_UNITATS_CLASSE.mysql_error());}
        }
    }    
        
function assigna_profe($idGrupMateria,$codiProfe)
    {
    $idProfessor = extreu_id('equivalencies','codi_prof_gp','prof_ga',$codiProfe);
    
    $id_materia = extreu_id('grups_materies','idgrups_materies','id_mat_uf_pla',$idGrupMateria);
    
    $nouIdGrupMateria = $idGrupMateria;
    // Si ja està posat el grupmateria i el professor, no s'ha de fer res. Encara que semble mentida pot haver
    // lessons amb tota la informació de grup/materia/classe  iguals
    $sql = "SELECT COUNT(idprof_grup_materia) FROM prof_agrupament WHERE idagrups_materies='".$idGrupMateria."' AND idprofessors = '".$idProfessor."';";
    //echo "<br>".$sql;

    $result=mysql_query($sql);if (!$result) {	die(_SELECT_PROF_GRUP_MATERIA.mysql_error());}
    $fila = mysql_fetch_row($result);
    if ($fila[0] == 0)
        {
        $sql2 = "SELECT COUNT(idprof_grup_materia) FROM prof_agrupament WHERE idagrups_materies='".$idGrupMateria."';";
//        echo "<br>>>".$sql2;
        $result2=mysql_query($sql2);if (!$result2) {	die(_SELECT_PROF_GRUP_MATERIA.mysql_error());}
        $fila2 = mysql_fetch_row($result2);
        //Si entra aqui és que hi ha una o més assignacions a altres professors d'aquest grup-materia
        if ($fila2[0] != 0)
            {
            $idGrupMateriaOriginal = $idGrupMateria;
            $desdoblem = comprova_desdoblament($idProfessor,$idGrupMateria);
            //echo "<br>Desdoblem ...".$desdoblem;
            if ($desdoblem == 1 ) 
                {
                //echo "<br>Id grup materia abans: ".$idGrupMateriaOriginal;
                $idGrupMateria = treu_darrer_desdoblament($idGrupMateriaOriginal);
                //echo "<br>Id grup materia després: ".$idGrupMateria;
                $idGrupMateria = creadesdoblament($idGrupMateria,$id_materia,$idProfessor);
                //echo "<br>Id grup materia desprésssss: ".$idGrupMateria;
                $nouIdGrupMateria = $idGrupMateria;
                }
            else
                {$nouIdGrupMateria = $desdoblem;}
            }
        
        if ($idProfessor!= '')
            {
            $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$nouIdGrupMateria."');";
            //echo "<br>>>>>>>".$sql;
            $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}
            }
        }
    return $nouIdGrupMateria;
//    //Comrpvem si ja està assignat
//    $sql = "SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '".$idProfessor."' AND idagrups_materies = '".$idGrupMateria."'";
//    $result=mysql_query($sql);
//    if (!$result) {die(_ERR_SELECT_PROF_GRUPMATERIA . mysql_error());}
//    if (mysql_num_rows($result) == 0)
//        {
//        $sql = "INSERT INTO prof_agrupament(idprofessors,idagrups_materies) ";
//        $sql.= "VALUES ('".$idProfessor."','".$idGrupMateria."');";
//        $result=mysql_query($sql);
//        if (!$result) {die(_ERR_INSERT_PROF_GRUPMATERIA . mysql_error());}
//        }
    
    }
    
function creaMateriaGenerica($materia,$acronimPla)
    {
    $arrayMateria = explode("-",$materia);
    $materiaGenerica = $acronimPla."_".$arrayMateria[1];
    return $materiaGenerica;
    }
    
    
function generaGrupMateriaLoe($i,$idUf,$idGrup,$acronimPla,$idPla,$dataInici,$dataFi)
    {
    // Generem el grup materia i el retornem: primer comprovem que no existeixi ja
    
    $dataInici_tmp=date_create($dataInici);
    date_add($dataInici_tmp,date_interval_create_from_date_string("240 days"));
    $dataInici_tmp= date_format($dataInici_tmp,"Y-m-d");

    $dataFi_tmp=date_create($dataInici);
    date_add($dataFi_tmp,date_interval_create_from_date_string("239 days"));
    $dataFi_tmp = date_format($dataFi_tmp,"Y-m-d");    
    
//    echo "<br>",$dataInici." >> ".$dataFi;
//    echo "<br>",$dataInici_tmp." >> ".$dataFi_tmp;
    
    $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '".$idGrup."' AND id_mat_uf_pla = '".$idUf."';";
    
    $result2=mysql_query($sql2);
    if (!$result2) {die(_ERR_SELECT_ID_GRUPMATERIA . mysql_error());}
    $fila2 = mysql_fetch_row($result2);
    if (mysql_num_rows($result2) == 0)
        {
        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql2.= "VALUES ('".$idGrup."','".$idUf."',";
        if ($i !=0 )
            {

            $sql2.= "'".$dataInici_tmp."','".$dataFi."')";
            }
        else
            {

            $sql2.= "'".$dataInici."','".$dataFi_tmp."')";
            }
        //echo "<br>...".$sql2;
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_INSERT_GRUPMATERIA . mysql_error());}

        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '".$idGrup."' AND id_mat_uf_pla = '".$idUf."';";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_SELECT_ID_GRUPMATERIA . mysql_error());}
        $fila2 = mysql_fetch_row($result2);
        }
    
    return $fila2[0];        
    
    }

function generaGrupMateriaNoLoe($materia,$idGrup,$acronimPla,$idPla,$dataInici,$dataFi,$tutoria)
    {
    // Una materia genèrica és el seu nom sense el grup al que pertany
    if (!$tutoria) {$materiaGenerica = creaMateriaGenerica($materia,$acronimPla);}
    else {$materiaGenerica = $materia;}
    // Comporvem si la materia ja existeix
    $sql = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$materiaGenerica."'";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_SELECT_MATERIA . mysql_error());}
    $fila = mysql_fetch_row($result);
    if (mysql_num_rows($result) == 1)
        {$idMateria = $fila[0];}
    else
        {
        //Hem de crear la materia a les dues taules i exterure el seu id
        $sql2 = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
        $sql2.= "VALUES ('".$idPla."','".$materiaGenerica."','S')";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_INSERT_MATERIA2 . mysql_error());}
        $fila2 = mysql_fetch_row($result2);
        
        $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$materiaGenerica."';";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_SELECT_ID_MATERIA . mysql_error());}
        $fila2 = mysql_fetch_row($result2);
        $idMateria=$fila2[0];
        
        $sql2 = "INSERT INTO materia(idmateria,codi_materia,nom_materia) ";
        $sql2.= "VALUES ('".$idMateria."','".$materiaGenerica."','".$materiaGenerica."')";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_INSERT_MATERIA3 . mysql_error());}       
        }
    // Generem el grup materia i el retornem: primer comprovem que no existeixi ja
    
    $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '".$idGrup."' AND id_mat_uf_pla = '".$idMateria."';";
    $result2=mysql_query($sql2);
    if (!$result2) {die(_ERR_SELECT_ID_GRUPMATERIA . mysql_error());}
    $fila2 = mysql_fetch_row($result2);
    if (mysql_num_rows($result2) == 0)
        {
        $sql2 = "INSERT INTO grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
        $sql2.= "VALUES ('".$idGrup."','".$idMateria."','".$dataInici."','".$dataFi."')";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_INSERT_GRUPMATERIA . mysql_error());}

        $sql2 = "SELECT idgrups_materies FROM grups_materies WHERE id_grups = '".$idGrup."' AND id_mat_uf_pla = '".$idMateria."';";
        $result2=mysql_query($sql2);
        if (!$result2) {die(_ERR_SELECT_ID_GRUPMATERIA . mysql_error());}
        $fila2 = mysql_fetch_row($result2);
        }
    
    return $fila2[0];        
    
    }
    
    
function esbrinaLOE_cali($idPla)
    {
    $sql = "SELECT COUNT(*) FROM plans_estudis WHERE Acronim_pla_estudis LIKE  '%".$idPla."%'  AND Nom_plan_estudis LIKE  '%LOE%';";
    //echo $sql."<br>";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_SELECT_PLA . mysql_error());}
    $fila = mysql_fetch_row($result); 
    if ($fila[0] == 0) {return 0;}
    else {return 1;}
    }


?>