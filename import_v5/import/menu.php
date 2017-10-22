<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:menu.php
* Autor: Víctor Lino
* Descripció: Presenta diiferents menús de càrrega en funció de la selecció d'una opció o una altra
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include("../config.php");
include("../funcions/func_historic.php");
include("../funcions/funcions_generals.php");
include("../funcions/func_prof_alum.php");
include("../funcions/funcionsCsv.php");

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
	header("location: ../login/access-denied.php");
	exit();
	}
?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    function marcar(source) 
    {	
		var patt1 ="alta";
        checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
        for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
        {
            if((checkboxes[i].type == "checkbox") && (checkboxes[i].name.indexOf(patt1) != -1))
            {
                checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
            }
        }
    }
    
    function mostrarReferencia()
	{
	if (document.fcontacto.materies[0].checked == true)
		{
		document.getElementById('subgeisoft2').style.display='block';
		document.getElementById('subgeisoft3').style.display='none';
		}
	else if (document.fcontacto.materies[1].checked == true)
		{
		document.getElementById('subgeisoft3').style.display='block';
		document.getElementById('subgeisoft2').style.display='none';
		}
	else	
		{
		document.getElementById('subgeisoft2').style.display='none';
		document.getElementById('subgeisoft3').style.display='none';
		}
	document.getElementById('subgeisoft4').style.display='none';
	}
	
    function mostrarReferencia1()
	{
	if ((document.fcontacto.app_materies[0].checked == true)||(document.fcontacto.app_materies[1].checked == true))
		{
		document.getElementById('subgeisoft4').style.display='block';
		}
	else
		{
		document.getElementById('subgeisoft4').style.display='none';
		}
	}   

    function mostrarReferencia2()
	{
	if ((document.fcontacto.app_materies2[0].checked == true)||(document.fcontacto.app_materies2[1].checked == true))
		{
		document.getElementById('subgeisoft4').style.display='block';
		}
	else
		{
		document.getElementById('subgeisoft4').style.display='none';
		}
	} 
    
</script>
</head>

<body>

<?php

    $exportsagaxml=$_SESSION['upload_saga'];
    $exporthorarixml=$_SESSION['upload_horaris'];
    
    $callipolis = false;
    
			
//	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@22
	
	
	
	print("<br><br>");
        if ($callipolis) {print("<h1>VERSIÓ CAL·LIPOLIS</h1>");}
        print("<br>");
	print("<table border=\"0\" align=\"center\"><tr><td width=\"25%\" bgcolor=\"#ffbf6d\" >Fases de la càrrega de dades...<br><br>");
	print("Si durant el procés de càrrega vols anar comprovant la informació a l'aplicació, ho hauràs de fer des d'un altre navegador.<br><br>");
	print("<sub><sub><font color=\"white\">");

	if (extreu_fase('carrega')==0)
		{
		print("<b>Primera càrrega</b><br>(absoluta)<br>");
		print("Es durà a terme la primera càrrega de dades en l'aplicatiu. En aquest cas hi haurà alguna tasca una mica pesada que no es tornarà a repetir en posteriors càrregues");
		}
	else if (extreu_fase('carrega')==1)
		{
		print("<b>Primera càrrega</b><br>(curs nou)<br>");
		print("En aquest cas es respectarà el professorat i alumnat present i es permetrà l'actualització i la càrrega de professorat i alumnat nou. ");
		print("A continuació es generarà la informació històrica.<br>");
		print("Tota la informació a partir del sgrups es reintroduirà de nou. El sistema permetrà recuperar certes informacions del curs anterior");
		}
	else
		{
		print("<b>Actualització</b><br>(Seguir on m'havia quedat)<br>");
		print("En aquest cas seguiràs des del punt on t'havies quedat.<br>Si vols repetir algun dels passos que ja has realitzat hauràs de clicar en el botó tenint en compte que (en alguns casos) part de la informació s'esborrarà i l'hauràs de tornar a introduir.<br> ");
		print("Per exemple, si vols a tornar a introduir tots els grups, els elements vinculats als grups: horaris, assignacions,.. seran esborrats.");
		}
	print("</font></sub></sub></td><td bgcolor=\"cf9d02\">");
	
		
	// @@@@@@@@@@@@@@@@@@@
	// BLOC DE PROFESSORAT
	// @@@@@@@@@@@@@@@@@@@
	
   if(!extreu_fase('professorat'))
		{
		print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_prof.php'\" value=\"1. Carrega/actualitza Professorat\" ><br><br>");
		}
	else
		{
		$fitxerProfessorat = $_SESSION['professorat.csv'];
                print("<img src=\"../images/checked.gif\">");
		print("&nbsp;&nbsp;	El professorat ja ha estat actualitzat<br>");
		print("<sub><a href=\"../uploads/".$fitxerProfessorat."\">Descarrega't el csv amb els usuaris i passwords</a></sub><br><br>");
				
		}
	
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	// @@@@@@@@@@@@@@@@@@@@
	// BLOC D' ALUMNAT
	// @@@@@@@@@@@@@@@@@@@@	
	
	if ((!extreu_fase('alumnat')) AND (!extreu_fase('segona_carrega')))
		{
      print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_alum.php'\" value=\"2. Carrega/actualitza alumnes \" ><br>");
		print("<sub><sub><font color=\"black\"> Curs 16/17. Actualitzem dates de naixement. El llistat pot trigar en aparéixer</font></sub></sub><br>");
      if (extreu_fase('carrega')!=0)
			{
			print("<sub><sub><font color=\"black\">A continuació es gestionarà la informació històrica</font></sub></sub><br>");
			}
		}
	else 
		{
		$fitxerAlumnat = $_SESSION['alumnat.csv'];
                print("<img src=\"../images/checked.gif\">");
		print("&nbsp;&nbsp;	L'alumnat i les dades de famílies ja han estat actualitzades<br>");
		print("<sub><a href=\"../uploads/".$fitxerAlumnat."\">Descarrega't el csv amb els usuaris i passwords</a></sub><br><br>");

		}

	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	// @@@@@@@@@@@@@@@@@@@@
	// BLOC DE GRUPS
	// @@@@@@@@@@@@@@@@@@@@	
	
    if (!$callipolis)
        {
	if (!extreu_fase('grups')) 
		{
		print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_grups.php'\" value=\"Carrega grups \" ><br>");
		print("<sub><sub><font color=\"white\">Es carregarà la informació de grups i agrupaments.</font></sub></sub><br><br>");
		}
	else 
		{
		print("<img src=\"../images/checked.gif\">");
		print("&nbsp;&nbsp;	Els grups han estat creats<br>");
		}
        }
    else
        {
	if (!extreu_fase('grups')) 
		{
		print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_grups_cali.php'\" value=\"Carrega grups \" ><br>");
		print("<sub><sub><font color=\"white\">Es carregarà la informació de grups i agrupaments.</font></sub></sub><br><br>");
		}
	else 
		{
		print("<img src=\"../images/checked.gif\">");
		print("&nbsp;&nbsp;	Els grups han estat creats<br>");
		}
        }        
        
        
        
        
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
	// @@@@@@@@@@@@@@@@@@@@
	// BLOC DE MATÈRIES
	// @@@@@@@@@@@@@@@@@@@@	

    if (!$callipolis)
        {	
	if (!extreu_fase('materies'))  
            {
            print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_mat_banner.php'\" ");
            if (extreu_fase('app_horaris')==5)
               {
               print("value=\"Carrega matèries des del fitxer de SAGA \" ><br>");
               print("<sub><sub><font color=\"white\">Es carregarà tota la informació vinculada a matèries/mòduls i assignacions de professors</font></sub></sub>");
               print("<br><sub><sub><font color=\"white\">Es carregarà des del fitxer de SAGA tota la informació.</font></sub></sub>");
               }
            else
               {		
               if (extreu_fase('modalitat_fitxer')==0)
                  {print("value=\"Carrega matèries ESO/BAT/CAS \" ><br>");}
               else
                  {print("value=\"Carrega mòduls i unitats formatives CCFF \" ><br>");}
               print("<sub><sub><font color=\"white\">Es carregarà tota la informació vinculada a matèries i assignacions de professors</font></sub></sub>");
               print("<br><sub><sub><font color=\"white\">Es carregarà des del fitxer dels horaris. Si no existeix, es carregarà de SAGA tota la informació.</font></sub></sub>");
               }
            }
	else 
            {
            print("<br><img src=\"../images/checked.gif\">");
            print("&nbsp;&nbsp;	Les matèries han estat carregades.<br>");
            }
        }        
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	
	// @@@@@@@@@@@@@@@@@@@@@@@@
	// BLOC DE FRANGES I ESPAIS
	// @@@@@@@@@@@@@@@@@@@@@@@@	
	
	print("<br><br>");
   if(extreu_fase('app_horaris')!=5)
      {
      if (!extreu_fase('dies_espais_franges'))
         {
         introduir_fase('espais',0);
         introduir_fase('dies_espais_franges',0);
         introduir_fase('franges',0);
         introduir_fase('dies_setmana',0);
         introduir_fase('lessons',0);
         print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_dies_franges_espais.php'\" value=\"5. Carrega dies, franges i espais\" ><br>");
         print("<sub><sub><font color=\"white\">Carregarà les franges i els espais</font></sub></sub><br><br>");
         }
      else
         {
         print("<img src=\"../images/checked.gif\">");
         print("&nbsp;&nbsp;	Les franges i els espais han estat carregats<br>");
         //if (extreu_fase('carrega')==2)
         //	{print("<sub><a href=\"./main_dies_franges_espais.php\">Torna a carregar franges i espais</a> (<font color=\"red\">Es perdrà certa informació ja carregada de les fases posteriors!!</font>)</sub><br><br>");}		
         }
      }
   else
      {
      print("<img src=\"../images/unchecked.gif\">");
      print("&nbsp;&nbsp;	La càrrega de les franges i els espais s'ha de realitzar manualment<br>");
      }
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	
	// @@@@@@@@@@@@@@@@@@@@@@@@@
	// BLOC DE CREACIÓ D'HORARIS
	// @@@@@@@@@@@@@@@@@@@@@@@@@	
    if (!$callipolis)
       {	
       if(extreu_fase('app_horaris')!=5)
          {
          if (!extreu_fase('lessons'))
             {
             print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_horaris.php'\" value=\"6. Carrega horaris i assigna professors\" ><br>");
             print("<sub><sub><font color=\"white\">Crearà els horaris i assignarà professorat.</font></sub></sub><br><br>");
             }
          else
             {
             print("<br><img src=\"../images/checked.gif\">");
             print("&nbsp;&nbsp;	Els horaris han estat creats. Alea jacta est !<br>");
             //if (extreu_fase('carrega')==2)
             //	{print("<sub><a href=\"./main_horaris.php\">Torna a carregar els horaris</a></sub><br><br>");}				
             }
          }   
       else
          {
          print("<br><img src=\"../images/unchecked.gif\">");
          print("&nbsp;&nbsp;	La càrrega dels horaris s'haurà de realitzar manualment<br>");
          }
	
       }
    else
        {
       if(extreu_fase('app_horaris')!=5)
          {
          if (!extreu_fase('lessons'))
             {
             print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_horaris_cali.php'\" value=\"6. Carrega horaris i assigna professors\" ><br>");
             print("<sub><sub><font color=\"white\">Crearà els horaris i assignarà professorat.</font></sub></sub><br><br>");
             }
          else
             {
             print("<br><img src=\"../images/checked.gif\">");
             print("&nbsp;&nbsp;	Els horaris han estat creats. Alea jacta est !<br>");
             //if (extreu_fase('carrega')==2)
             //	{print("<sub><a href=\"./main_horaris.php\">Torna a carregar els horaris</a></sub><br><br>");}				
             }
          }   
       else
          {
          print("<br><img src=\"../images/unchecked.gif\">");
          print("&nbsp;&nbsp;	La càrrega dels horaris s'haurà de realitzar manualment<br>");
          }        
        }

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@		
	
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@
	// BLOC D'ASSIGNACIÓ D'ALUMNES
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@	
if (!$callipolis)
   {
        
   if (extreu_fase('app_horaris')==5)
      {
      if (!extreu_fase('assig_alumnes'))
         {
         print("<br><img src=\"../images/down2.png\">&nbsp;7. Assigna els alumnes a grups-matèries<br>");
         print("<form method=\"post\" action=\"./main_seleccio_alumnes.php\" enctype=\"multipart/form-data\" id=\"profform\">");
         print("<br><sub>A CCFF es disposa d'automatrícula. <br>");
         print("Indica si vols gestionar-ho per automatrícula o carregar els alumnes de forma automàtica amb la informació de SAGA.</sub><br>");
         print("<font color=\"#dbdccd\"><input type=\"checkbox\" name=\"automatricula\" value=\"1\">Vull automatrícula, no carreguis la informació de SAGA del CCFF<br>");
         print("<input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
         print("</form>");
         print("<sub><br><b>Pot trigar més de 10 minuts !</b></sub><br>");
         }
      else
         {
         print("<br><img src=\"../images/checked.gif\">");
         print("&nbsp;&nbsp;	Els alumnes han estat assignat als grups i matèries !<br>");
         //if (extreu_fase('carrega')==2)
         //   {print("<sub><a href=\"./main_assignacio_alumnes.php\">Torna a carregar les assignacions d'alumnes</a></sub><br><br>");}
         }      
      }
   else
      {
      if (extreu_fase('modalitat_fitxer')==0)
         {
         if (!extreu_fase('assig_alumnes'))
            {
            print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_seleccio_alumnes.php'\" value=\"7. Assigna els alumnes a grups-matèries\" ><br>");
            print("<sub><b>Pot trigar més de 10 minuts !</b></sub><br>");
            }
         else
            {
            print("<br><img src=\"../images/checked.gif\">");
            print("&nbsp;&nbsp;	Els alumnes han estat assignat als grups i matèries !<br>");
            //if (extreu_fase('carrega')==2)
            //   {print("<sub><a href=\"./main_assignacio_alumnes.php\">Torna a carregar les assignacions d'alumnes</a></sub><br><br>");}
            }
         }
      else
         {
         //print("&nbsp;&nbsp;<br>	En el cas de cicles formatius els alumnes s'inscriuen per automatrícula !<br>");
         if (!extreu_fase('assig_alumnes'))
            {
            print("<br><img src=\"../images/down2.png\">&nbsp;7. Assigna els alumnes a grups-matèries<br>");
            print("<form method=\"post\" action=\"./main_seleccio_alumnes.php\" enctype=\"multipart/form-data\" id=\"profform\">");
//            print("<br><sub>A CCFF es disposa d'automatrícula. <br>");
//            print("Indica si vols gestionar-ho per automatrícula o carregar els alumnes de forma automàtica amb la informació de SAGA.>/sub><br>");
//            print("<font color=\"#dbdccd\"><input type=\"checkbox\" name=\"automatricula\" value=\"1\">Vull automatrícula, no carreguis la informació de SAGA<br>");
            print("<input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
            print("</form>");
            print("<sub><br><b>Pot trigar més de 10 minuts !</b></sub><br>");
            }
         else
            {
            print("<br><img src=\"../images/checked.gif\">");
            print("&nbsp;&nbsp;	Els alumnes han estat assignat als grups i matèries !<br>");
            //if (extreu_fase('carrega')==2)
            //   {print("<sub><a href=\"./main_assignacio_alumnes.php\">Torna a carregar les assignacions d'alumnes</a></sub><br><br>");}
            }
         }
      }
   } 
 else 
    {
// ESPECIAL CALLIPOLIS
     if (!extreu_fase('assig_alumnes'))
        {
        print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_assignacio_alumnes_cali.php'\" value=\"7. Assigna els alumnes a grups-matèries\" ><br>");
        print("<sub><b>Pot trigar més de 10 minuts !</b></sub><br>");
        }
    else
        {
        print("<br><img src=\"../images/checked.gif\">");
        print("&nbsp;&nbsp;	Els alumnes han estat assignat als grups i matèries !<br>");
        //if (extreu_fase('carrega')==2)
        //   {print("<sub><a href=\"./main_assignacio_alumnes.php\">Torna a carregar les assignacions d'alumnes</a></sub><br><br>");}
        }      
    }
    
    
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@			
	
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// BLOC DE GRUPS SENSE MATERIES - NOMÉS SAGA
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	
	if (extreu_fase('app_horaris')==2)
      {
      if (!extreu_fase('grups_sense_amteries'))
         {
         print("<br><img src=\"../images/unchecked.gif\">&nbsp;<input type=button onClick=\"location.href='./main_grups_saga_no_materies.php'\" value=\"Gestiona grups sense matèries\" ><br>");
         print("<br><sub><b>Compte</b>:<font color=\"white\"> Aquesta opció matrícularà tots els alumnes que hi hagi assignats. Això pot provocar que");
         print("<br>que tots els alumnes d'un nivell acabin matriculats a aquesta grup. Depèn de com tinguis configurat SAGA.");
         print("<br>Per la gestió d'optatives , disposes de l'automatrícula</sub></font><br> ");
         print("<sub><sub><font color=\"white\">Gestionarà els grups que no tenen matèries assignades al fitxer de SAGA.</font></sub></sub><br><br>");
         }
      else
         {
         print("<br><img src=\"../images/checked.gif\">");
         print("&nbsp;&nbsp;	Els grups sense matèries han estat gestionats !<br>");
         }
      }

      
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@      
      
      
	print("</td></tr></table>");

?>

</body>

	




