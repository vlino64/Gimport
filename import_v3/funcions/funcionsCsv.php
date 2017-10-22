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


function carregaFrangesDies()
    {
    include("../config.php");

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S' ;";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
    $fila = mysql_fetch_row($result);$periode = $fila[0];

    $sql = "SELECT idtorn FROM torn WHERE nom_torn = 'TORN GLOBAL' ;";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
    $fila = mysql_fetch_row($result);$torn = $fila[0];    
    
    
    
    $sql = "INSERT INTO `franges_horaries` (`idfranges_horaries`, `idtorn`, `activada`, `esbarjo`, `hora_inici`, `hora_fi`) VALUES";
    $sql .= "(1, ".$torn.", 'S', ' ', '08:15:00', '09:15:00'),";
    $sql .= "(2, ".$torn.", 'S', ' ', '09:15:00', '10:15:00'),";
    $sql .= "(3, ".$torn.", 'S', ' ', '10:15:00', '11:15:00'),";
    $sql .= "(4, ".$torn.", 'S', ' ', '11:45:00', '12:45:00'),";
    $sql .= "(5, ".$torn.", 'S', ' ', '12:45:00', '13:45:00'),";
    $sql .= "(6, ".$torn.", 'S', ' ', '13:45:00', '14:45:00'),";
    $sql .= "(7, ".$torn.", 'S', ' ', '15:30:00', '16:25:00'),";
    $sql .= "(8, ".$torn.", 'S', ' ', '16:25:00', '17:20:00'),";
    $sql .= "(9, ".$torn.", 'S', ' ', '17:20:00', '18:15:00'),";
    $sql .= "(10, ".$torn.", 'S', ' ', '18:45:00', '19:40:00'),";
    $sql .= "(11, ".$torn.", 'S', ' ', '19:40:00', '20:35:00'),";
    $sql .= "(12, ".$torn.", 'S', ' ', '20:35:00', '21:30:00');";
    $result=mysql_query($sql);if (!$result) {die(INSERT_FRANGES.mysql_error());}
    
    $sql = "SELECT iddies_setmana FROM dies_setmana WHERE iddies_setmana < 6 ;";
    $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
    while ($fila = mysql_fetch_row($result))
        {
        $sql2 = "SELECT idfranges_horaries FROM franges_horaries";
        $result2=mysql_query($sql2);if (!$result2) {die(SELECT_FRANGES.mysql_error());}
        while ($fila2 = mysql_fetch_row($result2))
            {
            $sql3 = "INSERT INTO dies_franges(iddies_setmana,idfranges_horaries,idperiode_escolar) ";
            $sql3 .="VALUES($fila[0],$fila2[0],$periode)";
            $result3=mysql_query($sql3);if (!$result3) {die(INSERT_DIES_FRANGES.mysql_error());}
            }
        
        
        }
    }

function extreuGrupsCsv()
    {
    $csvFile = $_SESSION['upload_horaris'];
    
    $data= array();
    $grups = array();
    $data = netejaCsv($csvFile);
    $i = 0;
    
    foreach ($data as $fila)
        {
        $array_fila=explode(";",$fila);
        $grupFila = $array_fila[2];
        $trobat = false;
        foreach ($grups as $grup)
            {
            //echo "<br>>>>>".$arr_prof;
            if (!strcmp($grup,$grupFila))
                {
                $trobat = true;
                }            
            }    
        if (!$trobat)
            {
            $grups[$i] = $grupFila;
            $i++;
            }
        }
    sort($grups);
    return $grups;
    }

function extreuDia($dia)    
    {
    switch ($dia)
        {
        case "A":
            $dia = 1;
            break;
        case "B":
            $dia = 2;
            break;
        case "C":
            $dia = 3;
            break;
        case "D":
            $dia = 4;
            break;
        case "E":
            $dia = 5;
            break;
        // Ens hem trobat algun cas que no entra en cap i per tant donava un error
        default:
            $dia = 1;
        }
    return $dia;
    
    }
    
function creaSessionsEso($sessions, $idgrup_materia,$codi_noroom,$periode)
    {
    include("../config.php");

    $unitatsclasse = explode(" ",$sessions);

    foreach ($unitatsclasse as $uclasse)
        {
        $franja = substr($uclasse, 0, strpos($uclasse,"("))+1;
        $dies = substr($uclasse,(strpos($uclasse,"(")+1),((strpos($uclasse,")"))-(strpos($uclasse,"(")+1)));
        $arrayDies = explode(",",$dies);
        foreach ($arrayDies as $dia)
            {
            $dia=extreuDia($dia);
            //echo "<br>".$franja." >> ".$dia;
            $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."'";
            $sql.=" AND idperiode_escolar='".$periode."' ;";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
            $fila = mysql_fetch_row($result);$diaFranja = $fila[0];            
            
            $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
            $sql.="(".$diaFranja.",".$codi_noroom.",".$idgrup_materia.")";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(INSERT_UC_ESO.mysql_error());}
            
            }    
        //echo "<br>=====================";
        }    
    }

function creaSessionsCCFF($sessions,$arrayUfs,$codi_noroom,$periode)
    {
    include("../config.php");

    $unitatsclasse = explode(" ",$sessions);

    foreach ($unitatsclasse as $uclasse)
        {
        $franja = substr($uclasse, 0, strpos($uclasse,"("))+1;
        $dies = substr($uclasse,(strpos($uclasse,"(")+1),((strpos($uclasse,")"))-(strpos($uclasse,"(")+1)));
        $arrayDies = explode(",",$dies);
        foreach ($arrayDies as $dia)
            {
            
            $dia=extreuDia($dia);
            //echo "<br>".$franja." >> ".$dia;
            $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana='".$dia."' AND idfranges_horaries='".$franja."'";
            $sql.=" AND idperiode_escolar='".$periode."' ;";
            //echo "<br>".$sql;
            $result=mysql_query($sql);if (!$result) {die(SELECT_DIES.mysql_error());}
            $fila = mysql_fetch_row($result);$diaFranja = $fila[0];            
            for ($i=0;$i<count($arrayUfs);$i++)
                {
                $sql="INSERT INTO unitats_classe(id_dies_franges,idespais_centre,idgrups_materies) VALUES ";
                $sql.="(".$diaFranja.",".$codi_noroom.",".$arrayUfs[$i][0].")";
                //echo "<br>".$sql;
                $result=mysql_query($sql);if (!$result) {die(INSERT_UC_FP.mysql_error());}
                }
            }     
        
        //echo "<br>=====================";
        }    
    }
    
function extreuGrupsCsvtmp()
    {
    $csvFile = $_SESSION['upload_horaris'];
    
    $data= array();
    $grups = array();
    $data = netejaCsv($csvFile);
    $i = 0;
    
    foreach ($data as $fila)
        {
        $array_fila=explode(";",$fila);
        $grupFila = $array_fila[2];
        $array_grup = explode(",", $grupFila);
        foreach ($array_grup as $arr_grup) 
            {
//            echo "<br>>>>>".$arr_grup;            
            $trobat = false;
            foreach ($grups as $grup)
                {
                //echo "<br>>>>>".$arr_prof;
                if (!strcmp($grup,$arr_grup))
                    {
                    $trobat = true;
                    }            
                }    
            if (!$trobat)
                {
                $grups[$i] = $arr_grup;
                $i++;
                }
            }
        
        }
    sort($grups);
    return $grups;
    
    }

function netejaCsv($csvFile)
    {
    $data2= array();
    $data= array();
    $data=file($csvFile);
    $linies = count($data);
    $j=0;
    for ($i=0;$i<$linies;$i++)
        {
        if (($i>0) && (strlen($data[$i]) > 2)) // Poso 2 perquè el csv posa algun caràcter especial que no es veu
            {
            $data2[$j] = $data[$i];
            $data2[$j] = substr($data2[$j],1);
            $data2[$j] = str_replace("\",\"",";",$data2[$j]);
            //echo "<br>>>".$data2[$j];
            $j++;
            }
        }
    return $data2;
    }

function gestionaProfessorESO($profFila,$idgrup_materia,$es_nou_grup_materia)
    {
    $array_prof = explode(",", $profFila);
    foreach ($array_prof as $arr_prof) 
        {
        $idProfessor = extreu_id("equivalencies","nom_prof_gp","prof_ga",$arr_prof);
        if ($idProfessor != "")
            {
            if ($es_nou_grup_materia)
                {
                $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$idgrup_materia."');";
                //echo "<br>>>>>>>".$sql;
                $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}            
                }
            else 
                {
                // Hem de comprovar que la relació no estiguija establerta
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
            }    
        }
    }    

function gestionaProfessorCCFF($profFila,$arrayUfs)
    {
    $array_prof = explode(",", $profFila);
    foreach ($array_prof as $arr_prof) 
        {
        $idProfessor = extreu_id("equivalencies","nom_prof_gp","prof_ga",$arr_prof);
        if ($idProfessor != "")
            {        
            for ($i=0; $i < count($arrayUfs);$i++)
                {
                if ($arrayUfs[$i][1] == 1)
                    {
                    $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$arrayUfs[$i][0]."');";
                    //echo "<br> Inserint".$sql;
                    $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA.mysql_error());}            
                    }
                else 
                    {
                    // Hem de comprovar que la relació no estiguija establerta
                    $sql="SELECT idprof_grup_materia FROM prof_agrupament WHERE idprofessors = '".$idProfessor."' AND idagrups_materies ='".$arrayUfs[$i][0]."';";
                    //echo "<br>>>>>>>".$sql;
                    $result=mysql_query($sql);if (!$result) {	die(_SELECT_PROF_GRUP_MATERIA.mysql_error());}
                    if (mysql_num_rows($result) == 0)
                        {
                        $sql="INSERT INTO prof_agrupament(idprofessors,idagrups_materies) VALUES ('".$idProfessor."','".$arrayUfs[$i][0]."');";
                        //echo "<br>>>>>>>".$sql;
                        $result=mysql_query($sql);if (!$result) {	die(_INSERINT_PROF_GRUP_MATERIA(2).mysql_error());}                            
                        }
                    }
                }
            }    
        }
    }
    
function extreuProfessoratCsv()
    {
    $csvFile = $_SESSION['upload_horaris'];

    $data= array();
    $professorat = array();
    $data = netejaCsv($csvFile);
    foreach ($data as $fila)
        {
        //echo "<br>>".$fila;
        $array_fila=explode(";",$fila);
        $profFila = $array_fila[3];
       
        $array_prof = explode(",", $profFila);
        foreach ($array_prof as $arr_prof) 
            {
            $trobat = false;
            foreach ($professorat as $prof)
                {
                //echo "<br>>>>>".$arr_prof;
                if (!strcmp($prof,$arr_prof))
                    {
                    $trobat = true;
                    }            
                }    
            if (!$trobat)
                {
                $professorat[$i] = $arr_prof;
                $i++;
                }
            }
        
        }
    sort($professorat);
    return $professorat;    
    }

function extreuMateriesCsv()
    {
    $csvFile = $_SESSION['upload_horaris'];

    $data= array();
    $materies = array();
    $data = netejaCsv($csvFile);
    $i = 0;
    foreach ($data as $fila)
        {
        //echo "<br>>".$fila;
        $array_fila=explode(";",$fila);
        $codiMateria = $array_fila[0];
        $materia = $array_fila[1];
        $trobat = false;
        for ($fila=0; $fila<=count($materies)-1; $fila++) 
            {
            if ((!strcmp($codiMateria,$materies[$fila][0])) && (!strcmp($materia,$materies[$fila][1])) )
                {
                $trobat = true;
                break;
                }            
            }    
        if (!$trobat)
            {
            $materies[$i][0] = $codiMateria;
            $materies[$i][1] = $materia;
            //echo "<br>nou: ".$materies[$i][0]." /// ".$materies[$i][1]." >>> ".$i;
            $i++;
            }
        }
    sort($materies);
    return $materies;  
    }
?>