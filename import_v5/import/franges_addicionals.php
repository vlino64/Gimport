<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:grups_act.php
* Autor: Víctor Lino
* Descripció: Carrega els grups del fitxer de saga
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include("../config.php");
include("../funcions/func_grups_materies.php");
include("../funcions/funcions_generals.php");
include("../funcions/func_espais_franges.php");

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
</head>

<body>
<?php

   
   
   $comptador=0;
   $exporthorarixml=$_SESSION['upload_horaris'];
   $resultatconsulta=simplexml_load_file($exporthorarixml);
   if ( !$resultatconsulta ) {echo "Carrega fallida";}
   else 
      {
      echo "<br>Carrega correcta";
      foreach ($resultatconsulta->lessons->lesson as $classe)
			{
			foreach ($classe->times->time as $franges)
				{
            $horainici=$franges->assigned_starttime;
            $horainici=$horainici*100;
            $horainici=arregla_hora_gpuntis($horainici);
            $horafi=$franges->assigned_endtime;
            $horafi=$horafi*100;
            $horafi=arregla_hora_gpuntis($horafi);
           
            $sql="SELECT COUNT('idfranges_horaries') FROM franges_horaries WHERE ";
            $sql.="hora_inici='".$horainici."' AND hora_fi='".$horafi."';";
            $result=mysql_query($sql); if (!$result) {die(SELECT_FRANGES_ADDICIONALS.mysql_error());}
            $present=mysql_result($result,0);
            $ja_esta=0;
            if ($present==0)
               {
               
               for($i=0;$i<=$comptador;$i++)
                  {
                  if(($matriu[$i][0]==$horainici) AND ($matriu[$i][1]==$horafi)) {$ja_esta=1;}
                  }
               if ($ja_esta==0)
                  {
                  $matriu[$comptador][0]=$horainici;
                  $matriu[$comptador][1]=$horafi;
                  $comptador++;
                  }
               }
            }
			}
      
      print("<form method=\"post\" action=\"./franges_intro_extras.php\" enctype=\"multipart/form-data\" id=\"profform\">");
      print("<table align=\"center\" border=\"0\">");
      print("<tr><td align=\"center\" colspan=\"5\"><h3>INSTRUCCIONS:<br>");
      print("S'han detectat franges addicionals<br>");
      print("<tr align=\"center\" bgcolor=\"#635656\" ><td>Franja</td><td> Hora inici</td><td>Hora fi</td><td>Esbarjo ?</td><td>Torn a assignar</td></tr>");
      for($i=0;$i<=$comptador-1;$i++)
         {
         $sql1="SELECT A.idfranges_horaries,A.hora_inici,A.hora_fi,B.nom_torn FROM franges_horaries A, torn B	";
         $sql1.="WHERE A.idtorn=B.idtorn AND A.activada='S'";
         $result1=mysql_query($sql1);if (!$result1) {die(SELECT_franges.mysql_error());}              

         $sql="SELECT idtorn,nom_torn FROM torn;";
         //echo $sql;
         $result=mysql_query($sql); if (!$result) {die(Select_id_torn.mysql_error());}

         print("<tr ");
         if ((($pos/5)%2)=="0") 
            {print("bgcolor=\"#3f3c3c\"");}
         print("><td><input type=\"text\" name=\"id_codi_".$i."\" value=\"".$franja."\" SIZE=\"5\" READONLY></td>");
         print("<td><input type=\"text\" name=\"inici_".$i."\" value=\"".$matriu[$i][0]."\" SIZE=\"8\" READONLY ></td>");
         print("<td><input type=\"text\" name=\"fi_".$i."\" value=\"".$matriu[$i][1]."\" SIZE=\"8\" READONLY ></td>");
         print("<td><input type=\"checkbox\" name=\"esbarjo_".$i."\" value=\"1\"></td>");
         print("</td>");

         print("<td><select multiple name=\"id_torn_".$i."\">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
            print("<option value=\"".$fila[0]."\">".$fila[1]."</option>");
            }
         print("</select></td>");
         print("</tr> ");
        
			}
		}
		           
   print("<tr><td align=\"center\" colspan=\"5\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
   print("&nbsp&nbsp<input type=button onClick=\"location.href='./main_dies_franges_espais.php'\" value=\"Torna\" ></td></tr>");
   print("<tr><td align=\"center\" colspan=\"5\"><input type=\"text\" name=\"recompte\" value=\"".$comptador."\" HIDDEN ></td></tr>");
   print("</table>");
   print("</form>");
   
   


?>
</body>
