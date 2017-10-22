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
include("../config.php");
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

    include("../config.php");

    
    $exporthorarixml=$_SESSION['upload_horaris'];
    if (extreu_fase('app_horaris') == 4) {$resultatconsulta = 1;}
    else {$resultatconsulta=simplexml_load_file($exporthorarixml);}
    
    $opcio = $_POST['opc_materies'];
    $acord = $_POST['dacord'];
    
    /// Si es fitxer ESO/BAT/CAS/LOGSE >> Passa directament
//    if(extreu_fase('modalitat_fitxer') == 0)
//        {
//        $page = "./intro_mat.php";
//        $sec="0";
//        header("Refresh: $sec; url=$page");        
//        }
    
    
    if ((($opcio=='opc1') OR ($opcio=='opc2')) AND ($acord=='dacord'))
        {
        if ($opcio=='opc1') 
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
                carrega_CCFF_de_SAGA();
                //GPUNTIS
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
 
            if (extreu_fase('modalitat_fitxer')==2) // DUAL ESO CCFF
                 {
                 carrega_CCFF_de_SAGA();
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
        }
    else if (extreu_fase('modalitat_fitxer') != 0)    
         {
         $page = "./main_mat_banner.php";
         $sec="0";
         header("Refresh: $sec; url=$page");
         }        
	
?>
</body>

	




