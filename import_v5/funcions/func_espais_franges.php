<?php

/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:funcions_saga.php
 * Autor: Víctor Lino
 * Descripció: Funcions relacionades amb tasques d'importació de dades de SAGA
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2
//					DIES / FRANGES	/ ESPAIS
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function carregaFrangesDiesKW($db) {

    $sql = "SELECT idperiodes_escolars FROM periodes_escolars WHERE actual = 'S' ;";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $periode = $fila['idperiodes_escolars'];

    $sql = "SELECT idtorn FROM torn WHERE nom_torn = 'TORN GLOBAL' ;";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();
    $torn = $fila['idtorn'];

    $sql = "INSERT INTO `franges_horaries` (`idfranges_horaries`, `idtorn`, `activada`, `esbarjo`, `hora_inici`, `hora_fi`) VALUES";
    $sql .= "(1, " . $torn . ", 'S', ' ', '08:15:00', '09:10:00'),";
    $sql .= "(2, " . $torn . ", 'S', ' ', '09:10:00', '10:05:00'),";
    $sql .= "(3, " . $torn . ", 'S', ' ', '10:05:00', '11:00:00'),";
    $sql .= "(4, " . $torn . ", 'S', ' ', '11:30:00', '12:25:00'),";
    $sql .= "(5, " . $torn . ", 'S', ' ', '12:25:00', '13:20:00'),";
    $sql .= "(6, " . $torn . ", 'S', ' ', '13:20:00', '14:15:00'),";
    $sql .= "(7, " . $torn . ", 'S', ' ', '14:15:00', '15:30:00'),";
    $sql .= "(8, " . $torn . ", 'S', ' ', '15:30:00', '16:25:00'),";
    $sql .= "(9, " . $torn . ", 'S', ' ', '16:25:00', '17:20:00'),";
    $sql .= "(10, " . $torn . ", 'S', ' ', '17:20:00', '18:15:00'),";
    $sql .= "(11, " . $torn . ", 'S', ' ', '18:45:00', '19:40:00'),";
    $sql .= "(12, " . $torn . ", 'S', ' ', '19:40:00', '20:35:00'),";
    $sql .= "(13, " . $torn . ", 'S', ' ', '20:35:00', '21:30:00');";
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "SELECT iddies_setmana FROM dies_setmana WHERE iddies_setmana < 6 ;";
    $result = $db->prepare($sql);
    $result->execute();

    foreach ($result->fetchAll() as $fila) {
        $sql2 = "SELECT idfranges_horaries FROM franges_horaries";
        $result2 = $db->prepare($sql2);
        $result2->execute();
        foreach ($result2->fetchAll() as $fila2) {
            $sql3 = "INSERT INTO dies_franges(iddies_setmana,idfranges_horaries,idperiode_escolar) ";
            $sql3 .="VALUES(" . $fila['iddies_setmana'] . "," . $fila2['idfranges_horaries'] . "," . $periode . ")";
            $result3 = $db->prepare($sql3);
            $result3->execute();
        }
    }
}

function extreu_codi_franja_guardies($dia, $fran, $db) {
    
    $exporthorarixml = $_SESSION['upload_horaris'];
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        foreach ($resultatconsulta2->timeperiods->timeperiod as $franja) {
            $dia_tmp = $franja->day;
            $franja_tmp = $franja->period;
            if (($dia_tmp == (integer) $dia) AND ( $franja_tmp == (integer) $fran)) {
                $horainici = $franja->starttime;
                $horafi = $franja->endtime;
                //echo "<br>Hora inici:fi: ".$horainici." >> ".$horafi;
            }
        }
        $horainici = $horainici * 100;
        $horainici = arregla_hora_gpuntis($horainici);
        $horafi = $horafi * 100;
        $horafi = arregla_hora_gpuntis($horafi);
    }
    $sql = "SELECT idfranges_horaries FROM franges_horaries ";
    $sql.="WHERE activada='S' AND hora_inici='" . $horainici . "' AND hora_fi='" . $horafi . "';";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
    $id_franja = $fila['idfranges_horaries'];

    $sql = "SELECT id_dies_franges FROM dies_franges ";
    $sql.="WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $id_franja . "';";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $fila = $result->fetch();
    $iddia_franja = $fila['id_dies_franges'];

    return $iddia_franja;
}

function _extreu_codi_franja($dia, $fran, $id_grup,$db) {

    $id_torn = extreu_id('grups', 'idgrups', 'idtorn', $id_grup);

    $exporthorarixml = $_SESSION['upload_horaris'];
    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta2) {
        echo "Carrega fallida Horaris >> " . $exporthorarixml;
    } else {
        foreach ($resultatconsulta2->timeperiods->timeperiod as $franja) {
            $dia_tmp = $franja->day;
            $franja_tmp = $franja->period;
            //echo "<br>".$dia_tmp." >> ".$dia." >> ".$franja_tmp." >> ".$fran;
            if (($dia_tmp == (integer) $dia) AND ( $franja_tmp == (integer) $fran)) {
                $horainici = $franja->starttime;
                $horafi = $franja->endtime;
                //echo "<br>Hora inici:fi: ".$horainici." >> ".$horafi;
            }
        }
        $horainici = $horainici * 100;
        $horainici = arregla_hora_gpuntis($horainici);
        $horafi = $horafi * 100;
        $horafi = arregla_hora_gpuntis($horafi);
    }
    $sql = "SELECT idfranges_horaries FROM franges_horaries ";
    $sql.="WHERE idtorn='" . $id_torn . "' AND activada='S' AND hora_inici='" . $horainici . "' AND hora_fi='" . $horafi . "';";
    $result = mysql_query($sql);
    if (!$result) {
        die(Select_franja2 . mysql_error());
    }
    $id_franja = mysql_result($result, 0);
    //echo "<br>".$sql;
    $sql = "SELECT id_dies_franges FROM dies_franges ";
    $sql.="WHERE iddies_setmana='" . $dia . "' AND idfranges_horaries='" . $id_franja . "';";
    $result = mysql_query($sql);
    if (!$result) {
        die(Select_franja2 . mysql_error());
    }
    $iddia_franja = mysql_result($result, 0);
    //echo "<br>".$sql;

    return $iddia_franja;
}

function carrega_dies($exporthorarixml, $db) {

    $dies = array("Dilluns", "Dimarts", "Dimecres", "Dijous", "Divendres", "Dissabte", "Diumenge");
    $comptador = 1;
    foreach ($dies as $dies_setmana) {
        $sql = "INSERT INTO dies_setmana(iddies_setmana,dies_setmana,laborable) VALUES ('" . $comptador . "','" . $dies_setmana . "','S');";
        //echo $sql;
        $comptador++;
        $result = $db->prepare($sql);
        $result->execute();
    }

    $sql = "UPDATE  `dies_setmana` SET  `laborable` =  'N' WHERE  `dies_setmana`.`iddies_setmana` =6;";
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "UPDATE  `dies_setmana` SET  `laborable` =  'N' WHERE  `dies_setmana`.`iddies_setmana` =7;";
    $result = $db->prepare($sql);
    $result->execute();
}

function formulari_franges_GP($exporthorarixml,$db) {

    print("<form method=\"post\" action=\"./franges_intro.php\" enctype=\"multipart/form-data\" id=\"profform\">");
    //echo $exporthorarixml;
    $resultatconsulta = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta) {
        echo "Carrega fallida";
    } else {
        echo "<br>Carrega correcta";
        print("<table align=\"center\"  border=\"0\" >");
        if (extreu_fase('segona_carrega',$db)) {
            print("<tr><td align=\"center\" colspan=\"7\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen.<br>Crea només les franges necessàries marcant el checkbox");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
            print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\">");
            print(" Només per INS CJ. Han de marcar el checkbox, seleccionar les hores titulars, i a totes les altres marcar: 'Sense Torn assignat'</td></tr>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\"\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Correspondència</td><td>Crea ?</td><td>Torn a assignar</td></tr>");
        } else {
            print("<tr><td align=\"center\" colspan=\"5\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen<br>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
            print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\">");
            print(" Can Jofresa. Han de marcar el checkbox, seleccionar les hores titulars, i a totes les altres marcar: 'Sense Torn assignat'</td></tr>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Torn a assignar</td></tr>");
        }

        $pos = 1;

        foreach ($resultatconsulta->timeperiods->timeperiod as $franges) {
            $dia = $franges->day;
            if ($dia == "1") { // Només analitza les franges del primer dia
                $franja = $franges->period;
                $horainici = $franges->starttime;
                $horainici = $horainici * 100;
                $horainici = arregla_hora_gpuntis($horainici);
                $horafi = $franges->endtime;
                $horafi = $horafi * 100;
                $horafi = arregla_hora_gpuntis($horafi);

                $sql1 = "SELECT A.idfranges_horaries as idFrangesHora,A.hora_inici as horaIni,A.hora_fi as horaFi,B.nom_torn as nomTorn FROM franges_horaries A, torn B	";
                $sql1.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
                $result1 = $db->prepare($sql1);
                $result1->execute();

                $sql = "SELECT idtorn,nom_torn FROM torn;";
                //echo $sql;
                $result = $db->prepare($sql);
                $result->execute();

                print("<tr  align=\"center\"");
                if ((($pos / 5) % 2) == "0") {
                    print("bgcolor=\"#ffbf6d\"");
                }
                print("><td><input type=\"text\" name=\"id_codi_" . $pos . "\" value=\"" . $franja . "\" SIZE=\"5\" READONLY></td>");
                print("<td><input type=\"text\" name=\"inici_" . $pos . "\" value=\"" . $horainici . "\" SIZE=\"8\" READONLY ></td>");
                print("<td><input type=\"text\" name=\"fi_" . $pos . "\" value=\"" . $horafi . "\" SIZE=\"8\" READONLY ></td>");
                print("<td><input type=\"checkbox\" name=\"esbarjo_" . $pos . "\" value=\"1\"></td>");
                if (extreu_fase('segona_carrega', $db)) {
                    print("<td><select name=\"fran_parella_" . $pos . "\">");
                    print("<option value=\"0\">No correspon a cap franja ja creada</option>");
                    foreach ($result1->fetchAll() as $fila1) {
                        print("<option value=\"" . $fila1['idFrangesHora'] . "\">" . $fila1['horaIni'] . "-" . $fila1['horaFi'] . "-" . $fila1['nomTorn'] . "</option>");
                    }
                    print("</select></td>");
                    print("<td><input type=\"checkbox\" name=\"crea_franja_" . $pos . "\" value=\"1\" CHECKED >Crea la franja</td>");
                } else {
                    print("</td>");
                }


                print("<td><select multiple name=\"id_torn_" . $pos . "[]\">");
                print("<option value=\"0\">Cap Torn assignat</option>");
                foreach ($result->fetchAll() as $fila) {
                    print("<option value=\"" . $fila['idtorn'] . "\">" . $fila['nom_torn'] . "</option>");
                }
                print("</select></td>");
                print("</tr> ");
                $pos++;
            }
        }
        $pos--;

        print("<tr><td align=\"center\" colspan=\"5\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
        print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
        print("<tr><td align=\"center\" colspan=\"5\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
        print("</table>");
        print("</form>");
    }
}

function _valorPresent($franges, $hora) {
    $j = 0;
    echo "<br>>>" . count($franges);
    for ($j = 0; $j < count($franges); $j++) {
        if ($hora == $franges[$j])
            return 1;
    }
    return 0;
}

function _formulari_franges_KW($exporthorarixml) {
//    require_once('../../bbdd/connect.php');
//
//    print("<form method=\"post\" action=\"./franges_intro.php\" enctype=\"multipart/form-data\" id=\"profform\">");
//    //echo $exporthorarixml;
//    $resultatconsulta=simplexml_load_file($exporthorarixml);
//    if ( !$resultatconsulta ) {echo "Carrega fallida";}
//    else 
//        {
//        echo "<br>Carrega correctas";
//        print("<table align=\"center\" border=\"0\">");
//        if(extreu_fase('segona_carrega'))
//           {
//           print("<tr><td align=\"center\" colspan=\"7\"><h3>INSTRUCCIONS:<br>");
//           print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen.<br>Crea només les franges necessàries marcant el checkbox");
//           print('Introdueix també les hores d\'inici i de fi (Dues xifres per caixa)');
//           print("<tr align=\"center\" bgcolor=\"#635656\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
//           print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\"></td></tr>");
//           print("<tr align=\"center\" bgcolor=\"#635656\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Correspondència</td><td>Crea ?</td><td>Torn a assignar</td></tr>");
//           }         
//       else
//           {
//           print("<tr><td align=\"center\" colspan=\"5\"><h3>INSTRUCCIONS:<br>");
//           print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen<br>");
//           print('Introdueix també les hores d\'inici i de fi (Dues xifres per caixa)');
//           print("<tr align=\"center\" bgcolor=\"#635656\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
//           print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\"></td></tr>");
//           print("<tr align=\"center\" bgcolor=\"#635656\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Torn a assignar</td></tr>");
//           }
//
//        $pos = 1;
//        $i = 0;
//        $franja = array();
//        foreach ($resultatconsulta->SOLUCT->SOLUCF as $franges)
//            {
//            if ($franges['DIA'] == 1)
//                {
//                $trobat = 0;
//                $hora = $franges['HORA'];
//                for($j=0;$j<count($franja);$j++)
//                    {
//                    if (!strcmp($hora,$franja[$j])) {$trobat=1;}
//                    }
//                if (!$trobat) 
//                    {
//                    $franja[$i]=(int) $hora;
//                    $i++;
//                    }
//                }
//            }
//        sort($franja); 
//        $sql1="SELECT A.idfranges_horaries,A.hora_inici,A.hora_fi,B.nom_torn FROM franges_horaries A, torn B	";
//        $sql1.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
//        $result1=mysql_query($sql1);if (!$result1) {die(SELECT_franges.mysql_error());}              
//
//
//
//        foreach ($franja as $valor)
//            {
//            print("<tr  align=\"center\"");
//            if ((($pos/5)%2)=="0") 
//                    {print("bgcolor=\"#3f3c3c\"");}
//            print("><td><input type=\"text\" name=\"id_codi_".$pos."\" value=\"".$valor."\" SIZE=\"5\" READONLY></td>");
//            print("<td><input type=\"text\" name=\"inicih_".$pos."\" value=\"".$horainici_h."\" SIZE=\"2\"  > :");
//            print("<input type=\"text\" name=\"inicim_".$pos."\" value=\"".$horainici_m."\" SIZE=\"2\"  ></td>");
//            print("<td><input type=\"text\" name=\"fih_".$pos."\" value=\"".$horafi_h."\" SIZE=\"2\" > : ");
//            print("<input type=\"text\" name=\"fim_".$pos."\" value=\"".$horafi_m."\" SIZE=\"2\"  ></td>");
//            print("<td><input type=\"checkbox\" name=\"esbarjo_".$pos."\" value=\"1\">");
//            if(extreu_fase('segona_carrega'))
//                {
//                print("<td><select name=\"fran_parella_".$pos."\">");
//                print("<option value=\"0\">No correspon a cap franja ja creada</option>");
//                while ($fila1=mysql_fetch_row($result1))
//                   {
//                   print("<option value=\"".$fila1[0]."\">".$fila1[1]."-".$fila1[2]."-".$fila1[3]."</option>");
//                   }
//                print("</select></td>");
//                print("<td><input type=\"checkbox\" name=\"crea_franja_".$pos."\" value=\"1\">Crea la franja</td>");
//                }
//            else
//                {print("</td>");}					
//
//            //NO em deixa posar-ho fora del for
//            $sql="SELECT idtorn,nom_torn FROM torn;";
//            //echo $sql;
//            $result=mysql_query($sql); if (!$result) {die(Select_id_torn.mysql_error());}
//                
//                
//            print("<td><select multiple name=\"id_torn_".$pos."\">");
//            //print("<option value=\"0\">Cap Torn assignat</option>");
//            while ($fila=mysql_fetch_row($result))
//                    {
//                    print("<option value=\"".$fila[0]."\">".$fila[1]."</option>");
//                    }
//            print("</select></td>");
//            print("</tr> ");
//            $pos++;
//            }
//        
//
//        }
//            $pos--;
//
//            print("<tr><td align=\"center\" colspan=\"5\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
//            print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
//            print("<tr><td align=\"center\" colspan=\"5\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
//            print("</table>");
//            print("</form>");
}

function formulari_franges_HW($exporthorarixml, $db) {

    print("<form method=\"post\" action=\"./franges_intro.php\" enctype=\"multipart/form-data\" id=\"profform\">");
    //echo $exporthorarixml;
    $resultatconsulta = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta) {
        echo "Carrega fallida";
    } else {
        echo "<br>Carrega correctas";
        print("<table align=\"center\" border=\"0\">");
        if (extreu_fase('segona_carrega',$db)) {
            print("<tr><td align=\"center\" colspan=\"7\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen.<br>Crea només les franges necessàries marcant el checkbox");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
            print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\"></td></tr>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Correspondència</td><td>Crea ?</td><td>Torn a assignar</td></tr>");
        } else {
            print("<tr><td align=\"center\" colspan=\"5\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen<br>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"7\"><input type=\"checkbox\" name=\"mesfranges\" value=\"1\">");
            print("Marca si tens altres franges addicionals apart de les llistades a sota</td></tr><tr><td  colspan=\"5\"></td></tr>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Torn a assignar</td></tr>");
        }

        $pos = 1;

        foreach ($resultatconsulta->DATOS->TRAMOS_HORARIOS->TRAMO as $franges) {
            $dia = $franges['numero_dia'];
            if ($dia == "1") { // Només analitza les franges del primer dia
                $franja = $franges['numero_hora'];
                $horainici = $franges['hora_inicio'];
                $hora_inici = $horainici . ":00";
                $horafi = $franges['hora_final'];
                $hora_fi = $horafi . ":00";

                $sql1 = "SELECT A.idfranges_horaries,A.hora_inici,A.hora_fi,B.nom_torn FROM franges_horaries A, torn B	";
                $sql1.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
                $result1 = $db->prepare($sql1);
                $result1->execute();

                $sql = "SELECT idtorn,nom_torn FROM torn;";
                //echo $sql;
                $result = $db->prepare($sql);
                $result->execute();

                print("<tr  align=\"center\"");
                if ((($pos / 5) % 2) == "0") {
                    print("bgcolor=\"#ffbf6d\"");
                }
                print("><td><input type=\"text\" name=\"id_codi_" . $pos . "\" value=\"" . $franja . "\" SIZE=\"5\" READONLY></td>");
                print("<td><input type=\"text\" name=\"inici_" . $pos . "\" value=\"" . $horainici . "\" SIZE=\"8\" READONLY ></td>");
                print("<td><input type=\"text\" name=\"fi_" . $pos . "\" value=\"" . $horafi . "\" SIZE=\"8\" READONLY ></td>");
                print("<td><input type=\"checkbox\" name=\"esbarjo_" . $pos . "\" value=\"1\"></td>");
                if (extreu_fase('segona_carrega', $db)) {
                    print("<td><select name=\"fran_parella_" . $pos . "\">");
                    print("<option value=\"0\">No correspon a cap franja ja creada</option>");
                    foreach ($result1->fetchAll() as $fila1) {
                        print("<option value=\"" . $fila1['idFrangesHora'] . "\">" . $fila1['horaIni'] . "-" . $fila1['horaFi'] . "-" . $fila1['nomTorn'] . "</option>");
                    }
                    print("</select></td>");
                    print("<td><input type=\"checkbox\" name=\"crea_franja_" . $pos . "\" value=\"1\" CHECKED >Crea la franja</td>");
                } else {
                    print("</td>");
                }


                print("<td><select multiple name=\"id_torn_" . $pos . "[]\">");
                //print("<option value=\"0\">Cap Torn assignat</option>");
                foreach ($result->fetchAll() as $fila) {
                    print("<option value=\"" . $fila['idtorn'] . "\">" . $fila['nom_torn'] . "</option>");
                }
                print("</select></td>");
                print("</tr> ");
                $pos++;
            }
        }
        $pos--;

        print("<tr><td align=\"center\" colspan=\"5\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
        print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
        print("<tr><td align=\"center\" colspan=\"5\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
        print("</table>");
        print("</form>");
    }
}

function arregla_hora_gpuntis($hora) {
    if (strlen($hora) == "5") {
        $hora = str_pad($hora, 6, "0", STR_PAD_LEFT);
    }
    $hora = substr($hora, 0, 2) . ":" . substr($hora, 2, 2) . ":" . substr($hora, 4, 2);
    return $hora;
}

/* function _espais_ja_introduits()
  {

  $sql="SELECT A.hora_inici,A.hora_fi,B.nom_torn FROM franges_horaries A, torn B	";
  $sql.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
  $result=mysql_query($sql);if (!$result) {die(SELECT_franges.mysql_error());}
  print("<br><br><br><br> <h4>Torns que ja s'han donat d'alta <br>a l'aplicació</h4><br>");
  while($fila=mysql_fetch_row($result))
  {
  print("<br>".$fila[0]." - ".$fila[1]." . Torn assignat: ".$fila[2]);
  }
  }
 */

function formulari_franges_PN($exportgpuntisxml, $db) {

    print("<form method=\"post\" action=\"./franges_intro.php\" enctype=\"multipart/form-data\" id=\"profform\">");
    $resultatconsulta = simplexml_load_file($exportgpuntisxml);
    if (!$resultatconsulta) {
        echo "Carrega fallida";
    } else {
        echo "<br>Carrega correcta";
        print("<table align=\"center\">");
        if (extreu_fase('segona_carrega',$db)) {
            print("<tr><td align=\"center\" colspan=\"7\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen.<br>Crea només les franges necessàries marcant el checkbox");
            print("<br>Si hi ha correspondencia, no cal crear la franja ni indicar el torn");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Correspondència</td><td>Crea ?</td><td>Torn a assignar</td></tr>");
        } else {
            print("<tr><td align=\"center\" colspan=\"5\"><h3>INSTRUCCIONS:<br>");
            print("Indica si les diferents franges són d'esbarjo<br> i a quin torn corresponen<br>");
            print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Torn a assignar</td></tr>");
        }
        $pos = 1;

        foreach ($resultatconsulta->marcosDeHorario->marcoHorario->tramo as $franges) {

            $dia = $franges->dia;
            if ($dia == "0") {
                $franja = $franges->indice;
                $horainici = $franges->horaEntrada;
                //$horainici=$horainici*100;
                //$horainici=arregla_hora_gpuntis($horainici);
                $horafi = $franges->horaSalida;
                //$horafi=$horafi*100;
                //$horafi=arregla_hora_gpuntis($horafi);

                $sql1 = "SELECT A.idfranges_horaries as idFrangesHora,A.hora_inici as horaIni,A.hora_fi as horaFi,B.nom_torn as nomTorn FROM franges_horaries A, torn B	";
                $sql1.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
                $result1 = $db->prepare($sql1);
                $result1->execute();


                $sql = "SELECT idtorn,nom_torn FROM torn;";
                //echo $sql;
                $result = $db->prepare($sql);
                $result->execute();

                print("<tr ");
                if ((($pos / 5) % 2) == "0") {
                    print("bgcolor=\"#ffbf6d\"");
                }
                print("><td><input type=\"text\" name=\"id_codi_" . $pos . "\" value=\"" . $franja . "\" SIZE=\"5\" READONLY></td>");
                print("<td><input type=\"text\" name=\"inici_" . $pos . "\" value=\"" . $horainici . "\" SIZE=\"8\" READONLY ></td>");
                print("<td><input type=\"text\" name=\"fi_" . $pos . "\" value=\"" . $horafi . "\" SIZE=\"8\" READONLY ></td>");
                if ($franges->Tipo == "lectivo") {
                    print("<td><input type=\"checkbox\" name=\"esbarjo_" . $pos . "\" value=\"1\">");
                } else {
                    print("<td><input type=\"checkbox\" name=\"esbarjo_" . $pos . "\" value=\"1\" CHECKED>");
                }
                if (extreu_fase('segona_carrega', $db)) {
                    print("<td><select name=\"fran_parella_" . $pos . "\">");
                    print("<option value=\"0\">No correspon a cap franja ja creada</option>");
                    foreach ($result1->fetchAll() as $fila1) {
                        print("<option value=\"" . $fila1['idFrangesHora'] . "\">" . $fila1['horaIni'] . "-" . $fila1['horaFi'] . "-" . $fila1['nomTorn'] . "</option>");
                    }
                    print("</select></td>");
                    print("<td><input type=\"checkbox\" name=\"crea_franja_" . $pos . "\" value=\"1\" CHECKED>Crea la franja</td>");
                } else {
                    print("</td>");
                }
                print("<td><select name=\"id_torn_" . $pos . "\">");
                print("<option value=\"0\">Cap Torn assignat</option>");
                foreach ($result->fetchAll() as $fila) {
                    print("<option value=\"" . $fila['idtorn'] . "\">" . $fila['nom_torn'] . "</option>");
                }
                print("</select></td>");
                print("</tr> ");
                $pos++;
            }
        }
        $pos--;
        print("<tr><td align=\"center\" colspan=\"5\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
        print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
        print("<tr><td align=\"center\" colspan=\"5\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
        print("</table>");
        print("</form>");
    }
}

function form_espais2_gp($exporthorarixml, $db) {
    print("<form method=\"post\" action=\"./espais_intro2.php\" enctype=\"multipart/form-data\" id=\"espaisform\">");
    // Mostrem els espais ja introduits
    print("<table  align=\"center\"><tr ><td width =\"25%\">&nbsp</td><td width =\"25%\">ESPAIS JA DONATS D'ALTA</td><td width =\"25%\">NOUS ESPAIS DEL FITXER CARREGAT</td><td width =\"25%\">&nbsp</td></tr><tr><td></td><td>");
    espais_intro2_gp($exporthorarixml,$db);
    print("<br><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Crea els espais marcats\">");
//    $recompte = $recompte - 1;
    print("</td><td></td></tr></table>");
}

function espais_intro_GP($exporthorarixml, $db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    foreach ($resultatconsulta->rooms->room as $espai) {
        $nom = $espai->longname;
        $nom = neteja_apostrofs($nom);
        if ($nom == "") {
            $nom = $espai['id'];
        }
        $id = $espai['id'];
        $sql = "INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
        $sql.="VALUES ('" . $id . "','S','" . $nom . "');";
        //echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
    }
    introduir_fase('espais', 1, $db);
}

function espais_intro_PN($exporthorarixml, $db) {
    $resultatconsulta = simplexml_load_file($exporthorarixml);
    foreach ($resultatconsulta->aulas->aula as $espai) {
        $nom = $espai->nombre;
        $nom = neteja_apostrofs($nom);
        $sql = "INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
        $sql.="VALUES ('" . $nom . "','S','" . $nom . "');";
        $result = $db->prepare($sql);
        $result->execute();
    }
    introduir_fase('espais', 1, $db);
}

function espais_intro_KW($exporthorarixml, $db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    foreach ($resultatconsulta->AULAT->AULAF as $espai) {
        $nom = $espai['NOMBRE'];
        $nom = neteja_apostrofs($nom);
        if ($nom == "") {
            $nom = $espai['ABREV'];
        }
        $id = $espai['ABREV'];
        $sql = "INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
        $sql.="VALUES ('" . $id . "','S','" . $nom . "');";
        //echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
    }
    introduir_fase('espais', 1, $db);
}

function espais_intro_HW($exporthorarixml, $db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    foreach ($resultatconsulta->DATOS->AULAS->AULA as $espai) {
        $nom = $espai['nombre'];
        $nom = neteja_apostrofs($nom);
        if ($nom == "") {
            $nom = $espai['num_int_au'];
        }
        $id = $espai['num_int_au'];
        $sql = "INSERT INTO `espais_centre`(codi_espai,activat,descripcio) ";
        $sql.="VALUES ('" . $id . "','S','" . $nom . "');";
        //echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
    }
    introduir_fase('espais', 1, $db);
}

function espais_intro2_gp($exporthorarixml,$db) {

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    $sql = "SELECT descripcio,codi_espai FROM espais_centre;	";
    $result=$db->prepare($sql);
    $result->execute();
    foreach ($result->fetchAll() as $fila) {
        if ($fila['descripcio'] != '') {
            print($fila['descripcio'] . "<br>");
        } else {
            print($fila['codi_espai'] . "<br>");
        }
    }
    print("</td><td>Indica quins espais vols donar d'alta<br><br> ");

    $resultatconsulta = simplexml_load_file($exporthorarixml);
    if (!$resultatconsulta) {
        echo "<br>Carrega fallida";
    } else {
        $recompte = 1;
        $app = extreu_fase('app_horaris', $db);
        switch ($app) {
            case 0 :
                foreach ($resultatconsulta->rooms->room as $espai) {
                    $nom = $espai->longname;
                    $nom = neteja_apostrofs($nom);
                    $id = $espai['id'];
                    print("<input type=\"checkbox\" name=\"espaicheck_" . $recompte . "\" value=\"1\">");
                    print("<input type=\"text\" name=\"espainom_" . $recompte . "\" value=\"" . $nom . "-" . $id . "\" size=\"30\" >");
                    print("<input type=\"text\" name=\"espaiid_" . $recompte . "\" value=\"" . $id . "\" HIDDEN ><br>");
                    $recompte++;
                }
                break;
            case 1 :
                foreach ($resultatconsulta->aulas->aula as $espai) {
                    $nom = $espai->nombre;
                    $nom = neteja_apostrofs($nom);
                    $id = $espai->abreviatura;
                    print("<input type=\"checkbox\" name=\"espaicheck_" . $recompte . "\" value=\"1\">");
                    print("<input type=\"text\" name=\"espainom_" . $recompte . "\" value=\"" . $nom . "-" . $id . "\" size=\"30\" >");
                    print("<input type=\"text\" name=\"espaiid_" . $recompte . "\" value=\"" . $id . "\" HIDDEN ><br>");
                    $recompte++;
                }
                break;
        }
        print("<input type=\"text\" name=\"recompte\" value=\"" . $recompte . "\" HIDDEN >");
    }
    print("</form>");
}

?>