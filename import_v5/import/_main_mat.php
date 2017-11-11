<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:prof-act.php
* Autor: Víctor Lino
* Descripció: Actualització o càrrega de professorat
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../bbdd/connect.php');
include("../funcions/func_grups_materies.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
	header("location: ../login/access-denied.php");
	exit();
	}

//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function marcar(source) 
    {	
	var patt1 ="es_CCFF_LOE";
        checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
        for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
        {
            if((checkboxes[i].type == "checkbox") && (checkboxes[i].name.indexOf(patt1) != -1))
            {
                checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
            }
        }
    }
</script>
</head>

<body>
<?php

    require_once('../../bbdd/connect.php');

    
    $exporthorarixml=$_SESSION['upload_horaris'];
    if (extreu_fase('app_horaris') == 4) {$resultatconsulta = 1;}
    else {$resultatconsulta=simplexml_load_file($exporthorarixml);}
    
    $ambProgramaHoraris = $_POST['programaHoraris'];
    $senseHorarisCentreNou = $_POST['novaIncorporacioNoHoraris'];
    $senseHorarisMantenirInfo = $_POST['mantenirNoHoraris'];
    $tensCCFFLOE = $_POST['tensCCFFLOE'];
    $ufsComMateries = $_POST['comMateries'];
    $ambHorarisCentreNou = $_POST['novaIncorporacioHoraris'];
    $ambHorarisMantenirInfo = $_POST['mantenirAmbHoraris'];
    
    
    //Provem el formulari
//    echo "<br>programa d'horaris ".$ambProgramaHoraris;
//    echo "<br>Sense programa d'horaris centre nou ".$senseHorarisCentreNou;
//    echo "<br>Sense programa d'horari centre vell Mantenir ".$senseHorarisMantenirInfo;
//    echo "<br>.......................................";
//    
//    echo "<br>programa d'horaris ".$ambProgramaHoraris;
//    echo "<br>Tens CCFF LOE ".$tensCCFFLOE;
//    echo "<br>Tracta com matèries ".$ufsComMateries;
//    echo "<br>centre nou ".$ambHorarisCentreNou;
//    echo "<br>Mantenir ".$ambHorarisMantenirInfo;
            
    if (($ambProgramaHoraris=="0") && ($senseHorarisCentreNou=="1")) $opcio = 1;
    else if (($ambProgramaHoraris=="0") && ($senseHorarisCentreNou=="0") && ($senseHorarisMantenirInfo=="1")) $opcio = 0;
    else if (($ambProgramaHoraris=="0") && ($senseHorarisCentreNou=="0") && ($senseHorarisMantenirInfo=="0")) $opcio = 1;
    else if (($ambProgramaHoraris=="1") && ($tensCCFFLOE=="0")) $opcio = 2;
    else if (($ambProgramaHoraris=="1") && ($tensCCFFLOE=="1") && ($ufsComMateries=="1")) $opcio = 2;
    else if (($ambProgramaHoraris=="1") && ($tensCCFFLOE=="1") && ($ufsComMateries=="0") && ($ambHorarisCentreNou=="1")) $opcio = 3;
    else if (($ambProgramaHoraris=="1") && ($tensCCFFLOE=="1") && ($ufsComMateries=="0") && ($ambHorarisCentreNou=="0") && ($ambHorarisMantenirInfo=="1")) $opcio = 4;
    else if (($ambProgramaHoraris=="1") && ($tensCCFFLOE=="1") && ($ufsComMateries=="0") && ($ambHorarisCentreNou=="0") && ($ambHorarisMantenirInfo=="0")) $opcio = 3;
    else 
        {
    	$page = "./main_mat_banner.php?retorn=yes";
	$sec="0";
	header("Refresh: $sec; url=$page");    
        }
    
    // *************************************************************************************
    // opcio0: No fer res
    // opcio1: Carregar-ho tot des de SAGA
    // opcio2: Es carrega tot des del programa d'horaris
    // opcio3: Matèries del  del programa d'horaris i móduls des de SAGA
    // opcio4: Matèries des del programa d'horaris oi mantenir móduls i unitats formatives
    // *************************************************************************************


/// Si es fitxer ESO/BAT/CAS/LOGSE >> Passa directament
    if ($opcio == 0) 
        {
        $page = "./menu.php";
        $sec="0";
        header("Refresh: $sec; url=$page");
        }
    else if ($opcio == 1)
        {
        select_plaestudis_saga();
        }
    else if($opcio == 2)
        {
        $page = "./intro_mat.php";
        $sec="0";
        header("Refresh: $sec; url=$page");        
        }
    else 
        {
        $j = 0 ;
        if (extreu_fase('modalitat_fitxer')==1) // CCFF
            {
//            if ($opcio == 3) carrega_CCFF_de_SAGA();
            if ($opcio == 3) {
                //Si és primera càrrega
                if (extreu_fase('segona_carrega')==0){
                    buidatge("desdemateries");
                }
                // Si és segona càrrega
                else {
                    buidatge("ufs_mantenint_materies");
                }
                carrega_CCFF_de_SAGA();
            }    
            if ($opcio == 4 ) {
                // NO s'ha de fer res tant si és una primera càrrega, com segona
                //buidatge("materies");
            }
            //GPUNTIS+
            $app = extreu_fase('app_horaris');
            switch ($app)    
                {    
                case  0 :
                    echo "GPuntis";
                    foreach ($resultatconsulta->subjects->subject as $materies)
                        {
                        $nom_materia=$materies->longname;
                        $nom_materia=neteja_apostrofs($nom_materia);
                        $codi_materia=$materies[id];
                        $codi_materia=neteja_apostrofs($codi_materia);
                        $nom_materia="(".$codi_materia.")".$nom_materia;
                        //echo "<br>".$nom_materia;
                        $materia[$j][0]= $codi_materia;
                        $materia[$j][1]= $nom_materia;
                        $j++;
                        }

                    break;    

            //PEÑALARA 
                case 1:       
                echo "Peñalara";    
                foreach ($resultatconsulta->materias->materia as $materia)
                    {
                    $nom_materia=$materia->nombreCompleto;
                    $nom_materia=neteja_apostrofs($nom_materia);
                    $codi_materia=$materia->nombre;
                    $codi_materia=neteja_apostrofs($codi_materia);
                    $nom_materia="(".$codi_materia.")".$nom_materia;
                    $materia2[$j][0]= $codi_materia;
                    $materia2[$j][1]= $nom_materia;
//                        echo "<br>>>".$materia2[$j][0];
//                        echo ">>>".$materia2[$j][1];
                    $j++;
                    }                        
                $materia = $materia2;    
                break;    

            //kRONOWIN
                case 2:
                echo "Kronowin";
                foreach ($resultatconsulta->ASIGT->ASIGF as $materia)
                    {
                    $codi_materia=$materia[ASIG];
                    $exporthorarixml=$_SESSION['upload_horaris'];
                    $resultatconsulta2=simplexml_load_file($exporthorarixml);
                    if ( !$resultatconsulta2 ) {echo "Carrega fallida";}
                    else 
                       {
                       foreach ($resultatconsulta2->NOMASIGT->NOMASIGF as $nomMateria)
                           {
                           if ($nomMateria[ABREV] == $codi_materia )
                               { $nom_materia = $nomMateria[NOMBRE];break;}
                           }
                       }
                    $codi_materia = neteja_apostrofs($codi_materia);
                    $nom_materia = neteja_apostrofs($nom_materia);
                    $nom_materia="(".$codi_materia.")".$nom_materia;                    
                    $materia[$j][0]= $codi_materia;
                    $materia[$j][1]= $nom_materia;
                    $j++;
                    }                        


            break;    

            //HORWIN
                case 3 :
                echo "Horwin";
                foreach ($resultatconsulta->DATOS->ASIGNATURAS->ASIGNATURA as $materia)
                    {
                    $nom_materia=$materia->nombre;
                    $nom_materia=neteja_apostrofs($nom_materia);
                    $codi_materia=$materia->num_int_as;
                    $codi_materia=neteja_apostrofs($codi_materia);
                    $nom_materia="(".$codi_materia.")".$nom_materia;                   
                    $materia[$j][0]= $codi_materia;
                    //echo "<br>>>".$materia[$j][0];
                    $materia[$j][1]= $nom_materia;
                    //echo ">>".$materia[$j][1];
                    $j++;
                    }



                break;
            }

            //Enviem els móduls a la funció
            alta_moduls($materia);    
            }

        if (extreu_fase('modalitat_fitxer')==2) 
            // DUAL ESO CCFF
            // Si es DUAL no té sentit parlar de segona càrrega
             {
//            if ($opcio == 3) carrega_CCFF_de_SAGA();
            if ($opcio == 3) {
                buidatge("desdemateries");
                carrega_CCFF_de_SAGA();
            }    
            if ($opcio == 4 ) {
                buidatge("materies");
            }
             // Ara msotrem totes les matèries seleccionades per defecte de forma que
             // Haurem de desmarcar les que siguin LOE

             if (( !$resultatconsulta ) && ( extreu_fase('app_horaris') != 5 )) {echo "Carrega Horaris fallida";}
             else 
                {
                echo "<br>Carregues correctes";
                print("<form method=\"post\" action=\"./DUAL_select_pla.php\" enctype=\"multipart/form-data\" id=\"profform\">");
                print("<table align=\"center\" >");
                print("<tr><td align=\"center\" colspan=\"4\">");
                print("<h3>Desmarca els mòduls que siguin de CCFF LOE</h3>");
                print("El primer checkbox indica si aquest element s'ha de crear. Hauries de desmarcar reunions, guardies, mòdusl i crèdits obsolets.. <br>");
                print("El segon checkbox indica si aquest element és una matèria/crèdit ESO/BAT/CAS/LOGSE ");
                print("<br>Pots marcar tots o cap amb aquest checkbox i després afinar...<input type=\"checkbox\" onclick=\"marcar(this);\" >");
                print("<br>Tot el que estigui marcat en el segon checkbox es carregarà com a matèrìa/crèdit ESO/BAT/CAS/LOGSE, ");
                print("<br> la resta com a mòduls LOE. </td></tr> ");

                print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"4\">Matèries del fitxer d'horaris</td></tr>");
                $pos=1;
                $franges_pintades =0;
                //GPUNTIS
                if (extreu_fase('app_horaris') == 0 )                    
                    {
                    //echo "<br>app_horaris = 0";
                    foreach ($resultatconsulta->subjects->subject as $materia)
                        {
                        if ($pos%4==1) 
                            {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {print("bgcolor=\"#ffbf6d\"");}
                            print(">");
                            }
                        $nom_materia=$materia->longname;
                        $nom_materia=neteja_apostrofs($nom_materia);
                        $codi_materia=$materia[id];
                        $codi_materia=neteja_apostrofs($codi_materia);
                        $nom_materia="(".$codi_materia.")".$nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_".$pos."\" >");
                        print("<input type=\"text\" name=\"codi_mat_".$pos."\" value=\"".$codi_materia."\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_".$pos."\" value=\"".$nom_materia."\" SIZE=\"30\" READONLY></td>");
                        if ($pos%4==0) {print("</tr>");}
                        $pos++;
                        }
                    }

                //PEÑALARA    
                else if (extreu_fase('app_horaris') == 1 )                        
                    {
                    //echo "<br>app_horaris = 1";
                    foreach ($resultatconsulta->materias->materia as $materia)
                        {
                        if ($pos%4==1) 
                            {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {print("bgcolor=\"#ffbf6d\"");}
                            print(">");
                            }
                        $nom_materia=$materia->nombreCompleto;
                        $nom_materia=neteja_apostrofs($nom_materia);
                        $codi_materia=$materia->nombre;
                        $codi_materia=neteja_apostrofs($codi_materia);
                        $nom_materia="(".$codi_materia.")".$nom_materia;
                        print("<td bgcolor=\"#ffbf6d\" ><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_".$pos."\" >");
                        print("<input type=\"text\" name=\"codi_mat_".$pos."\" value=\"".$codi_materia."\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_".$pos."\" value=\"".$nom_materia."\" SIZE=\"30\" READONLY></td>");
                        if ($pos%4==0) {print("</tr>");}
                        $pos++;
                        }                        
                    }  

                //kRONOWIN 
                else if (extreu_fase('app_horaris') == 2 )
                    {
                    //echo "<br>app_horaris = 2";
                    foreach ($resultatconsulta->NOMASIGT->NOMASIGF as $materia)
                        {
                        if ($pos%4==1) 
                            {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {print("bgcolor=\"#ffbf6d\"");}
                            print(">");
                            }
                        $codi_materia=$materia[ABREV];
                        $nom_materia = $materia[NOMBRE];
                        $codi_materia = neteja_apostrofs($codi_materia);
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $nom_materia="(".$codi_materia.")".$nom_materia;                    
                        print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_".$pos."\" >");
                        print("<input type=\"text\" name=\"codi_mat_".$pos."\" value=\"".$codi_materia."\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_".$pos."\" value=\"".$nom_materia."\" SIZE=\"30\" READONLY></td>");
                        if ($pos%4==0) {print("</tr>");}
                        $pos++;
                        }                        
                    }

                //HORWIN
                else if (extreu_fase('app_horaris') == 3 )
                    {                        
                    //echo "<br>app_horaris = 3";
                    foreach ($resultatconsulta->DATOS->ASIGNATURAS->ASIGNATURA as $materia)
                        {
                        if ($pos%4==1) 
                            {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {print("bgcolor=\"#ffbf6d\"");}
                            print(">");
                            }
                        $nom_materia=$materia['nombre'];
                        $nom_materia=neteja_apostrofs($nom_materia);
                        $codi_materia=$materia['num_int_as'];
                        $nivell = $materia['nivel'];
                        $codi_materia=neteja_apostrofs($codi_materia);
                        $nom_materia="(".$nivell.")".$nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_".$pos."\" >");
                        print("<input type=\"text\" name=\"codi_mat_".$pos."\" value=\"".$codi_materia."\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_".$pos."\" value=\"".$nom_materia."\" SIZE=\"30\" READONLY></td>");
                        if ($pos%4==0) {print("</tr>");}
                        $pos++;
                        }                         
                    }

                //ASC Horaris
                else if (extreu_fase('app_horaris') == 4 )
                    {                        
                    $materies = extreuMateriesCsv();

                    for ($fila=0; $fila<=count($materies)-1; $fila++)
                        {
                        if ($pos%4==1) 
                            {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {print("bgcolor=\"#ffbf6d\"");}
                            print(">");
                            }
                        $nom_materia=$materies[$fila][0]."-".$materies[$fila][1];
                        $nom_materia=neteja_apostrofs($nom_materia);
                        $codi_materia=$nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_".$pos."\" >");
                        print("<input type=\"text\" name=\"codi_mat_".$pos."\" value=\"".$codi_materia."\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_".$pos."\" value=\"".$nom_materia."\" SIZE=\"30\" READONLY></td>");
                        if ($pos%4==0) {print("</tr>");}
                        $pos++;
                        }                         
                    }                        

                $pos--;
                print("<tr><td align=\"center\" colspan=\"4\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
                print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
                print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
                print("</table>");
                print("</form>");
                }
             }         
        }
?>
</body>

	




