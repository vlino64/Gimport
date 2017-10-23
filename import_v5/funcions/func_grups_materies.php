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
//						GRUPS I MATÈRIES
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2

function genera_tutoria($dataInici,$dataFi,$idPla,$acronimPla,$esLoe)
    {
    $materia = $acronimPla."_Tutoria";
    if (!$esLoe)
        {
        $sql = "SELECT COUNT(idmateria) FROM materia WHERE codi_materia = '".$materia."';";
        $result=mysql_query($sql);
            

        if (!$result) {die(_ERR_INSERT_MATERIA2 . mysql_error());}
        $fila = mysql_fetch_row($result); 
        if ($fila[0] == 0) 
            {
            $sql2 = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
            $sql2.= "VALUES ('".$idPla."','".$materia."','S')";
            //echo "<br>".$sql2;

            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MATERIA2 . mysql_error());}

            $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$materia."';";
            //echo "<br>".$sql2;

            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_SELECT_ID_MATERIA . mysql_error());}
            $fila2 = mysql_fetch_row($result2);
            $idMateria=$fila2[0];

            $sql2 = "INSERT INTO materia(idmateria,codi_materia,nom_materia) ";
            $sql2.= "VALUES ('".$idMateria."','".$materia."','".$materia."')";
            //echo "<br>".$sql2;

            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MATERIA3 . mysql_error());}
            
            }
        }
    // Crea la tutoria com a unitat formativa i módul
    else 
        {
        $sql = "SELECT COUNT(idunitats_formatives) FROM unitats_formatives WHERE nom_uf = '".$materia."';";
        $result=mysql_query($sql);
        if (!$result) {die(_ERR_INSERT_MATERIA2 . mysql_error());}          
        $fila = mysql_fetch_row($result); 
        if ($fila[0] == 0) 
            {
            // Crearem primer el módul i extreurem el seu id
            $sql2 = "INSERT INTO moduls(idplans_estudis,nom_modul,codi_modul,hores_finals) ";
            $sql2.= "VALUES ('".$idPla."','".$materia."','".$materia."', 0 )";
            //echo "<br>".$sql2;
            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MODUL . mysql_error());}

            $idModul = extreu_id('moduls','nom_modul','idmoduls',$materia);
            
            $sql2 = "INSERT INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
            $sql2.= "VALUES ('".$idPla."','".$materia."','S')";
            //echo "<br>".$sql2;
            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MATERIA2 . mysql_error());}
            $fila2 = mysql_fetch_row($result2);

            $sql2 = "SELECT id_mat_uf_pla FROM moduls_materies_ufs WHERE codi_materia = '".$materia."';";
            $result2=mysql_query($sql2);
            //echo "<br>".$sql2;
            if (!$result2) {die(_ERR_SELECT_ID_MATERIA . mysql_error());}
            $fila2 = mysql_fetch_row($result2);
            $idMateria=$fila2[0];

            $sql2 = "INSERT INTO unitats_formatives(idunitats_formatives,nom_uf,codi_uf) ";
            $sql2.= "VALUES ('".$idMateria."','".$materia."','".$materia."')";
            //echo "<br>".$sql2;
            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MATERIA3 . mysql_error());}
            
            $sql2 = "INSERT INTO moduls_ufs(id_moduls,id_ufs) ";
            $sql2.= "VALUES ('".$idModul."','".$idMateria."')";
            //echo "<br>".$sql2;
            $result2=mysql_query($sql2);
            if (!$result2) {die(_ERR_INSERT_MATERIA3 . mysql_error());}            
            }        
            
         
       }
    //echo "<br>".$materia;
    return $materia;
   }

function cali_intro_grups($exportsagaxml,$exporthorarixml)
    {
    // Tot es gestionarà amb el torn global
    echo "***************************************************<br>";
    echo "S'ha de crear un torn que es digui \"Torn Global\"<br>";
    echo "S'agafen tots els grups de SAGA ja que hi són tots els desdoblaments.<br>";
    echo "També aprofitarem per carregar els plans d'estudis<br>";
    echo "A partir del nom del grup podem assignar també el pla d'estudis i el torn.<br>";
    echo "***************************************************<br>";

    // Extreiem el identificador del torn global
    $sql = "SELECT idtorn FROM torn WHERE nom_torn = 'Torn Global';";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_SELECT_TORNS . mysql_error());}
    $fila = mysql_fetch_row($result);$idTorn = $fila[0];

    carrega_plans_estudis();
    
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    if ( !$resultatconsulta ) {echo "Carrega Saga fallida";}
    else 
        {
        foreach($resultatconsulta->grups->grup as $grup)
            {
            $idGrup = $grup[id];
            $nomGrup = $grup[nom];
            $arrayNomGrup = explode(" ",$nomGrup);
            $arrayNomGrup2 = explode("-",$arrayNomGrup[0]);
            $nomGrup2 = $arrayNomGrup2[0].$arrayNomGrup2[1];
            
            $idPla = $arrayNomGrup[1];
            
            //Comprovem que aquest grup no existeixi
            $sql = "SELECT COUNT(idgrups) FROM grups WHERE codi_grup = '".$nomGrup."';";
            $result=mysql_query($sql);
            if (!$result) {die(_ERR_SELECT_PLANS . mysql_error());}
            $fila = mysql_fetch_row($result);$present = $fila[0];            
            
            if ($present == 0)
                {
                $sql = "INSERT INTO grups(idtorn,codi_grup,nom) VALUES ('".$idTorn."','".$nomGrup."','".$nomGrup2."');";
                $result=mysql_query($sql);
                if (!$result) {die(_ERR_INSERT_GROUPS . mysql_error());}

                //Extreiem l'identificador del grup 
                $id_grup=extreu_id('grups','codi_grup','idgrups',$nomGrup);    

                // Extreiem l'identificador del pla d'estudis
                $sql = "SELECT idplans_estudis FROM plans_estudis WHERE  `Acronim_pla_estudis` LIKE  '%".$idPla."%' ";
                $result=mysql_query($sql);
                if (!$result) {die(_ERR_SELECT_PLANS . mysql_error());}
                $fila = mysql_fetch_row($result);$id_pla = $fila[0];

                //Desem l'emparellament a la taula equivalencies per quan s'hagin de carregat els alumnes i matèries
                $sql="INSERT INTO equivalencies(grup_gp,grup_ga,pla_saga) VALUES ('".$nomGrup."','".$id_grup."','".$id_pla."');";
                $result=mysql_query($sql);	
                if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}            
                }
            }    
        }    
    introduir_fase('grups',1);
    $page = "./menu.php";
    $sec="0";
    header("Refresh: $sec; url=$page");
    }

function clonar_modul($id_modul,$duplicat)
   {}
function crea_grup_sense_materies_i_assigna_alumnes($tipus_pla,$id_pla,$nom_materia,$id_materia,$id_grup,$idg_grup)
   {}

function relaciona_grups_torns_sol_saga($exportsagaxml)
    {
    require_once('../../bbdd/connect.php');
	
   $resultatconsulta=simplexml_load_file($exportsagaxml);
   if ( !$resultatconsulta ) {echo "Carrega fallida";}
   else 
      {
      echo "<br>Carrega correcta";
      print("<form method=\"post\" action=\"./act_grups_sol_saga.php\" enctype=\"multipart/form-data\" id=\"profform\">");
      print("<table align=\"center\">");
      print("<tr><td align=\"center\" colspan=\"4\"><h1>INSTRUCCIONS:<br>");
      print("<h3>Relaciona els grups de SAGA <br>amb el torns de l'aplicatiu</h3><br>");
      print("<tr align=\"center\" bgcolor=\"orange\" ><td>Crea?</td><td>Codi grup (S)</td><td>Grup (S)</td><td>Torn a assignar</td><td>&nbsp;</td></tr>");
      $pos=1;

      $sql="SELECT idtorn,nom_torn FROM torn;";
      //echo $sql;
      $result=mysql_query($sql); if (!$result) {	die(mysql_error());}


      foreach ($resultatconsulta->grups->grup as $grup)
         {
         $sql="SELECT idtorn,nom_torn FROM torn;";
         //echo $sql;
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}	

         // Comprovem si el grup té continguts i professors assignats
         $comptador=0;
         foreach ($grup->continguts->contingut as $mat_prof)
            {
            $comptador++;
            }
         //echo "Comptador de contiguts: ".$comptador;
         print("<tr ");
         if ((($pos/5)%2)=="0") 
            {print("bgcolor=\"orange\"");}
         print("><td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");
         print("><td><input type=\"text\" name=\"id_grup_".$pos."\" value=\"".$grup[id]."\" SIZE=\"15\" READONLY></td>");
         print("<td><input type=\"text\" name=\"nom_grup_".$pos."\" value=\"(".$grup[codi].") ".$grup[nom]."\" SIZE=\"35\" READONLY ></td>");
         print("<td><select name=\"id_torn_".$pos."\" ");
         if ($comptador==0) {echo " DISABLED ";}
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
            print("<option value=\"".$fila[0]."\">".$fila[1]."</option>");
            }
         print("</select></td>");
         if ($comptador==0)
            {echo "<td>Aquest grup no té matèries assignades a SAGA.</td>";}
         else
            {echo "<td>&nbsp;</td>";}
         print("</tr> ");
         $pos++;
         }
      $pos--;
      print("<tr><td align=\"center\" colspan=\"8\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
      print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
      print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
      print("</table>");
      print("</form>");
      }

	
	} 
        
function relaciona_grups_torns_sense_materies($exportsagaxml)
    {}

function relaciona_grups_torns_csv()
	{
	require_once('../../bbdd/connect.php');
        
        
        
        $sql="INSERT IGNORE  INTO `grups` (`idgrups`, `idtorn`, `codi_grup`, `nom`, `Descripcio`) VALUES (0, 1, 'SENSE_GRUP', 'SENSE_GRUP', NULL);";
	if (!extreu_fase('segona_carrega'))
            {
            $result=mysql_query($sql);	
            if (!$result) 
                    {die(_ERR_INSERT_NO_GROUP . mysql_error());}	
            }

        echo "<br>Carregues correctes";
        print("<form method=\"post\" action=\"./act_grups.php\" enctype=\"multipart/form-data\" id=\"profform\">");
        print("<table align=\"center\">");
        if (extreu_fase('app_horaris') == 3)
            {   
            print("<tr><td align=\"center\" colspan=\"6\">");
            }
        else
            {   
            print("<tr><td align=\"center\" colspan=\"5\">");
            }
        print("<h3>Relaciona els grups/agrupaments del programa d'horaris,<br> amb els torns<br>Desmarca els que no vulguis crear</h3>");
        // secció per carregar els plans d'estudis
        print("<font color = 'blue'>Prem el botó si vols carregar els plans d'estudis del curs passat.<br>");
        print("Si hi ha algun pla d'estudis nou, carrega'ls i posterioment, afegeix-lo manualment a l'aplicació abans de seguir.<br></font>");
        print("&nbsp&nbsp<input type=button onClick=\"location.href='./carregaPlans.php'\" value=\"Carrega els plans d'estudis\" ><br><br>");



        print("Si no tens horaris superposats és recomanable escollir en tots els casos \"Torn global\" </tr>");
        print("<tr align=\"center\" bgcolor=\"orange\" ><td>Crea?</td><td>Codi grup (S)</td><td>Grup (S)</td>");
        if (extreu_fase('app_horaris') == 3) {print("<td></td>");}
        print("<td>Torn a assignar</td><td>Pla d'estudis</td>");
        print("</tr>");
        $pos=1;


      $pos = crea_form_grup_CSV();
      
      $pos--;
                print("<tr><td align=\"center\" colspan=\"6\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
                print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
                print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
                print("</table>");
                print("</form>");
    }    
    
function relaciona_grups_torns($exportalumnes,$exporthorarixml)
	{
	require_once('../../bbdd/connect.php');

	$exportsagaxml=$_SESSION['upload_saga'];
	$exporthorarixml=$_SESSION['upload_horaris'];

	$sql="INSERT IGNORE  INTO `grups` (`idgrups`, `idtorn`, `codi_grup`, `nom`, `Descripcio`) VALUES (0, 1, 'SENSE_GRUP', 'SENSE_GRUP', NULL);";
	if (!extreu_fase('segona_carrega'))
		{
		$result=mysql_query($sql);	
		if (!$result) 
			{die(_ERR_INSERT_NO_GROUP . mysql_error());}	
		}
		
		echo "<br>Carregues correctes";
		print("<form method=\"post\" action=\"./act_grups.php\" enctype=\"multipart/form-data\" id=\"profform\">");
		print("<table align=\"center\">");
		if (extreu_fase('app_horaris') == 3)
                    {   
                    print("<tr><td align=\"center\" colspan=\"6\">");
                    }
                else
                    {   
                    print("<tr><td align=\"center\" colspan=\"5\">");
                    }
                print("<h3>Relaciona els grups/agrupaments del programa d'horaris,<br> amb els torns<br>Desmarca els que no vulguis crear</h3>");
                // secció per carregar els plans d'estudis
                print("<font color = 'blue'>Prem el botó si vols carregar els plans d'estudis des del fitxer de SAGA que has carregat.<br>");
                print("Si hi ha algun pla d'estudis nou, carrega'ls i posterioment, afegeix-lo manualment a l'aplicació abans de seguir.<br></font>");
                print("&nbsp&nbsp<input type=button onClick=\"location.href='./carregaPlans.php'\" value=\"Carrega els plans d'estudis\" ><br><br>");
                
                
                
                print("Si no tens horaris superposats és recomanable escollir en tots els casos \"Torn global\" </tr>");
                print("<tr align=\"center\" bgcolor=\"orange\" ><td>Crea?</td><td>Codi grup (S)</td><td>Grup (S)</td>");
                if (extreu_fase('app_horaris') == 3) {print("<td></td>");}
                print("<td>Torn a assignar</td><td>Pla d'estudis</td>");
                print("</tr>");
		$pos=1;
		
      
      if (extreu_fase('app_horaris')==0) {$pos = crea_form_grup_GP($exporthorarixml);}
      else if (extreu_fase('app_horaris')==1) {$pos = crea_form_grup_PN($exporthorarixml);}
      else if (extreu_fase('app_horaris')==2) {$pos = crea_form_grup_KW($exporthorarixml);}
      else if (extreu_fase('app_horaris')==3) {$pos = crea_form_grup_HW($exporthorarixml);}
      else {$pos = crea_form_grup_ASC($exporthorarixml);}
//      else {$pos = crea_form_grup_ESF($exportalumnescsv);}
      
      $pos--;
		print("<tr><td align=\"center\" colspan=\"6\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
		print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
		print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
		print("</table>");
		print("</form>");
   }   
	
function  crea_form_grup_CSV()
    {
    $grups = extreuGrupsCsv2();
    $pos=1;
    foreach($grups as $grup)
         {
         print("<tr>");
         print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");
         print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup."\" SIZE=\"10\" READONLY></td>");
         print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup."\" SIZE=\"25\" READONLY></td>");
         $sql="SELECT idtorn,nom_torn FROM torn;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_torn_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[0]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[1]."</option>");
            }
         print("</select></td>");

         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_pla_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap pla assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[1]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[0]."</option>");
            }
         print("</select></td><tr>");         

         $pos++;
         }      
      
   return $pos;
   }
   
   
function  crea_form_grup_GP($exporthorarixml)
   {
	$resultatconsulta=simplexml_load_file($exporthorarixml);
	if ( !$resultatconsulta ) {echo "Carrega horaris fallida";}
	else 
		{
      //if (!extreu_fase('segona_carrega')) {carrega_plans_estudis();}      
//          carrega_plans_estudis();  
      $pos=1;
      foreach($resultatconsulta->classes->class as $grup)
         {
         print("<tr>");
         print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");
         print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup[id]."\" SIZE=\"10\" READONLY></td>");
         print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup->longname."\" SIZE=\"25\" READONLY></td>");
         $sql="SELECT idtorn,nom_torn FROM torn;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_torn_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[0]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[1]."</option>");
            }
         print("</select></td>");

         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_pla_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap pla assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[1]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[0]."</option>");
            }
         print("</select></td><tr>");         

         $pos++;
         }      
      }
   return $pos;
   }

function  crea_form_grup_ASC($exporthorarixml)
    {
    //include("./funcionsCsv.php");
    
    $pos=1;
    $grups = extreuGrupsCsv();
//     if (!extreu_fase('segona_carrega')) {carrega_plans_estudis();}    
    foreach($grups as $grup)
        {
        $grup = neteja_apostrofs($grup);
        print("<tr>");
        print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");
        print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup."\" SIZE=\"55\" READONLY></td>");
        print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup."\" SIZE=\"30\" READONLY></td>");
        $sql="SELECT idtorn,nom_torn FROM torn;";
        $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
        $torns=  mysql_num_rows($result);
        print("<td><select name=\"id_torn_".$pos."\" ");
        print(">");
        print("<option value=\"0\">Cap Torn assignat</option>");
        while ($fila=mysql_fetch_row($result))
           {
        print("<option value=\"".$fila[0]."\" ");
        if ($torns==1) {print(" selected ");}        
        print(">".$fila[1]."</option>");
           }
        print("</select></td>");
         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
        $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
        $torns=  mysql_num_rows($result);
        print("<td><select name=\"id_pla_".$pos."\" ");
        print(">");
        print("<option value=\"0\">Cap pla assignat</option>");
        while ($fila=mysql_fetch_row($result))
           {
        print("<option value=\"".$fila[1]."\" ");
        if ($torns==1) {print(" selected ");}        
        print(">".$fila[0]."</option>");
           }
        print("</select></td></tr>");         
        $pos++;
        }      
    return $pos;
    }
   

function  crea_form_grup_PN($exporthorarixml)
   {
	echo $exporthorarixml;
   $resultatconsulta=simplexml_load_file($exporthorarixml);
	if ( !$resultatconsulta ) {echo "Carrega horaris fallida";}
	else 
		{
//      if (!extreu_fase('segona_carrega')) {carrega_plans_estudis();}
            $pos=1;
      foreach($resultatconsulta->grupos->grupo as $grup)
			{
         print("<tr>");
         print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");         
         print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup->nombre."\" SIZE=\"15\" READONLY>");
         print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup->nombre."\" SIZE=\"15\" READONLY></td>");
         $sql="SELECT idtorn,nom_torn FROM torn;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}	
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_torn_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
            print("<option value=\"".$fila[0]."\" ");
            if ($torns==1) {print(" selected ");}        
            print(">".$fila[1]."</option>");
            }
         print("</select></td>");	

         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_pla_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap pla assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[1]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[0]."</option>");
            }
         print("</select></td><tr>");         

         $pos++;
         }      
      }   
   return $pos;   
   }
   
   
function  crea_form_grup_KW($exporthorarixml)
   {
	$resultatconsulta=simplexml_load_file($exporthorarixml);
	if ( !$resultatconsulta ) {echo "Carrega horaris fallida";}
	else 
		{
//      if (!extreu_fase('segona_carrega')) {carrega_plans_estudis();}
            $pos=1;
      foreach($resultatconsulta->GRUPT->GRUPF as $grup)
			{
         print("<tr>");
         print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");         
         print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup['ABREV']."\" SIZE=\"15\" READONLY>");
         print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup['DESCRIP']."\" SIZE=\"60\" READONLY></td>");
         $sql="SELECT idtorn,nom_torn FROM torn;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_torn_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
            print("<option value=\"".$fila[0]."\" ");
            if ($torns==1) {print(" selected ");}        
            print(">".$fila[1]."</option>");
            }
         print("</select></td>");	

         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_pla_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap pla assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[1]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[0]."</option>");
            }
         print("</select></td><tr>");         

         $pos++;
         }      
      }   
   return $pos;   
   }
   
   
function  crea_form_grup_HW($exporthorarixml)
   {
	echo $exporthorarixml;
   $resultatconsulta=simplexml_load_file($exporthorarixml);
	if ( !$resultatconsulta ) {echo "Carrega horaris fallida";}
	else 
		{
//      if (!extreu_fase('segona_carrega')) {carrega_plans_estudis();}
            $pos=1;
      foreach($resultatconsulta->DATOS->GRUPOS->GRUPO as $grup)
			{
         print("<tr>");
         print("<td><input type=\"checkbox\" name=\"crea_".$pos."\" CHECKED></td>");         
         print("<td><input type=\"text\" name=\"id_grup_gp_".$pos."\" value=\"".$grup['abreviatura']."\" SIZE=\"15\" READONLY>");
         print("<td><input type=\"text\" name=\"name_grup_gp_".$pos."\" value=\"".$grup['nombre']."\" SIZE=\"25\" READONLY></td>");
         print("<td><input type=\"text\" name=\"codi_grup_gp_".$pos."\" value=\"".$grup['num_int_gr']."\" SIZE=\"25\" HIDDEN></td>");
         $sql="SELECT idtorn,nom_torn FROM torn;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_torn_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap Torn assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
            print("<option value=\"".$fila[0]."\" ");
            if ($torns==1) {print(" selected ");}        
            print(">".$fila[1]."</option>");
            }
         print("</select></td>");	

         $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
         $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
         $torns=  mysql_num_rows($result);
         print("<td><select name=\"id_pla_".$pos."\" ");
         print(">");
         print("<option value=\"0\">Cap pla assignat</option>");
         while ($fila=mysql_fetch_row($result))
            {
         print("<option value=\"".$fila[1]."\" ");
         if ($torns==1) {print(" selected ");}        
         print(">".$fila[0]."</option>");
            }
         print("</select></td><tr>");         

         $pos++;
         }      
      }   
   return $pos;  
   }

function extreuGrupsAlumnatCsv()
    {
    $grups = array();
    $j = 0;
    $alumnat = extreuAlumnatCsv();
    foreach ($alumnat as $alumne)
        {
        for ($i = 0 ; $i < 3 ; $i++)
            {
            $existeix = false;
            foreach ($grups as $checkGrup)
                {
                if (!strcmp($checkGrup,$alumne[$i])) $existeix = true;
                }
            if (!$existeix) 
                {
                $grups[$j] = $alumne[$i];
                $j++;
                }    
            }
        }
    return grups;
    }
   
  
function  crea_form_grup_ESF()
    {
//    $grups = extreuGrupsAlumnatCsv();
//    $pos=1;
//    foreach ($grups as $grup)
//        {
//        $sql="SELECT idtorn,nom_torn FROM torn;";
//        $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
//        $torns=  mysql_num_rows($result);
//        print("<tr ");
//        print("><td><input type=\"text\" name=\"id_grup_".$pos."\" value=\"".$grup."\" SIZE=\"15\" READONLY></td>");
//        print("<td><input type=\"text\" name=\"nom_grup_".$pos."\" value=\"(".$grup.")\" SIZE=\"15\" READONLY ></td>");
//        print("<td><select name=\"id_torn_".$pos."\" ");
//        print(">");
//        print("<option value=\"0\">Cap Torn assignat</option>");
//        while ($fila=mysql_fetch_row($result))
//           {
//           print("<option value=\"".$fila[0]."\" ");
//           if ($torns==1) {print(" selected ");}        
//           print(">".$fila[1]."</option>");
//           }
//        print("</select></td>");
//        $sql="SELECT Nom_plan_estudis,idplans_estudis FROM plans_estudis;";
//        $result=mysql_query($sql); if (!$result) {	die(mysql_error());}
//        $torns=  mysql_num_rows($result);
//        print("<td><select name=\"id_pla_".$pos."\" ");
//        print(">");
//        print("<option value=\"0\">Cap pla assignat</option>");
//        while ($fila=mysql_fetch_row($result))
//            {
//            print("<option value=\"".$fila[1]."\" ");
//            if ($torns==1) {print(" selected ");}        
//            print(">".$fila[0]."</option>");
//            }
//        print("</select></td></tr>");
//        $pos++;
//        }      
//         
//   return $pos;
   }

function crea_agrupaments_GP($exporthorarixml)
   {
   $exporthorarixml=$_SESSION['upload_horaris'];
   $resultatconsulta=simplexml_load_file($exporthorarixml);
	if ( !$resultatconsulta ) {echo "Carrega Gpuntis fallida";}
	else 
		{
      foreach($resultatconsulta->lessons->lesson as $grupss)
         {
         // Comprovem que no estigui a equivalencies
         $sql="SELECT COUNT(grup_gp) FROM equivalencies WHERE grup_gp='".$grupss->lesson_classes[id]."';";
         $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT1_GRUPS.mysql_error());}
         $present=mysql_result($result,0);
         if ($present==0)
            {
            // Si no hi és, cerquem en la taula grups
            $sql="SELECT COUNT(idgrups) FROM grups WHERE codi_grup='".$grupss->lesson_classes[id]."';";
            $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
            $present=mysql_result($result,0);				
            if ($present==0)
               {
               // Si tampoc hi és és tracta d'un grup de no docència o un desdoblament/optativa
               // El trenquem per veure si es desdoblament/optativa
               $grup_ext=explode("CL_",$grupss->lesson_classes[id]);
               for ($i=1;$i<count($grup_ext);$i++)
                  {
                  $grup_ext[$i]=trim("CL_".$grup_ext[$i]);
                  }
               for ($i=1;$i<count($grup_ext);$i++)
                  {
                  {
                  $id_torn = torna_torn($grup_ext[$i]);   
                  $id_pla = torna_pla($grup_ext[$i]);
                  //echo "<br>".$id_torn." >> ".$id_pla." >> ".$grup_ext[$i];
                  if (($id_torn!='') AND ($id_pla!='')) break;
                  }
                  }	
               if (($id_torn!='') AND ($id_pla!=''))
                  {	
                  $sql="INSERT grups(codi_grup,nom,idtorn) ";
                  $sql.="VALUES ('".$grupss->lesson_classes[id]."','".$grupss->lesson_classes[id]."','".$id_torn."');";
                  //echo $sql;
                  $result=mysql_query($sql);	
                  if (!$result) {die(_ERR_INSERT_GROUPS_1 . mysql_error());}

                  //Extreiem l'identificador
                  $id_grup=extreu_id('grups','codi_grup','idgrups',$grupss->lesson_classes[id]);    

                  //Desem l'emparellament a la taula equivalencies per quan s'hagin de carregat els alumnes i matèries
                  $sql="INSERT INTO equivalencies(grup_gp,grup_ga,pla_saga) VALUES ('".$grupss->lesson_classes[id]."','".$id_grup."','".$id_pla."');";
                  $result=mysql_query($sql);	
                  if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}	
                  
                  }
               }
            }			
         }   
      }
   }
   
function crea_agrupaments_PN($exporthorarixml)
    {
    $exporthorarixml = $_SESSION['upload_horaris'];
    $resultatconsulta=simplexml_load_file($exporthorarixml);
    if ( !$resultatconsulta ) {echo "Carrega Peñalara fallida";}
    else 
		{
      foreach($resultatconsulta->sesionesLectivas->sesion as $grupss)
         {
         // Netegem l'item grupoMateria
         $agrupament=neteja_item_grup_materia($grupss->grupoMateria);

         // Comprovem que no estigui a equivalencies
         $sql="SELECT COUNT(grup_gp) FROM equivalencies WHERE grup_gp='".$agrupament."';";

         $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT1_GRUPS.mysql_error());}
         $present=mysql_result($result,0);
         if ($present==0)
            {
            // Si no hi és, cerquem en la taula grups
            $sql="SELECT COUNT(idgrups) FROM grups WHERE codi_grup='".$agrupament."';";

            $result=mysql_query($sql);if (!$result) {	die(_ERROR_DESAGREGANT_GRUPS(2).mysql_error());}
            $present=mysql_result($result,0);				
            if ($present==0)
               {
               // Si tampoc hi és és tracta d'un grup de no docència o un desdoblament/optativa
               // El trenquem per veure si es desdoblament/optativa
               $grup_ext=explode("/",$agrupament);
               for ($i=0;$i<count($grup_ext);$i++)
                  {
                  $id_torn = torna_torn($grup_ext[$i]);   
                  $id_pla = torna_pla($grup_ext[$i]);
                  //echo "<br>".$id_torn." >> ".$id_pla." >> ".$grup_ext[$i];
                  if (($id_torn!='') AND ($id_pla!='')) break;
                  }	
               if (($id_torn!='') AND ($id_pla!=''))
                  {	
                  $sql="INSERT grups(codi_grup,nom,idtorn) ";
                  $sql.="VALUES ('".$agrupament."','".$agrupament."','".$id_torn."');";
                  //echo $sql;
                  $result=mysql_query($sql);	
                  if (!$result) {die(_ERR_INSERT_GROUPS_1 . mysql_error());}

                  //Extreiem l'identificador
                  $id_grup=extreu_id('grups','codi_grup','idgrups',$agrupament);    

                  //Desem l'emparellament a la taula equivalencies per quan s'hagin de carregat els alumnes i matèries
                  $sql="INSERT INTO equivalencies(grup_gp,grup_ga,pla_saga) VALUES ('".$agrupament."','".$id_grup."','".$id_pla."');";
                  $result=mysql_query($sql);	
                  if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}
                  
                  }
               }
            }			
         }   
      }
   }  
   
//function crea_agrupaments_KW($exporthorarixml)
//   {
//   Crearem els agrupament al generar les unitats classe. fer-ho ara és perdre recursos
//   Per crear les unitats classe recorrerem ASIGT.
//   De cada línia extreurem materia, grup,espai i professor, i també hores setmanals (per limitar recorreguts)
//   Si el grup materia ja existeix es crea un nou agrupament i es genera el grup-materia
//   Amb la informació extreta de la línia cercarem a SOLUCF la mateixa combinacio de materia, grup, espais i professor.
//   i generarem les unitats classe
//   }   
//   
function crea_agrupaments_HW($exporthorarixml)
   {
    //   Partim dels horaris de les assignatures. Per cada assignatura, mirem els grups en els que es fa
    //   Amb els grups generem l'agrupament 
    //   A cada assignatura comprovem que l'agrupament no exiteixi ja. Si existeix es saltam, sinó, es crea

    // Esborrem equivaencies prèvies
    $sql = "DELETE FROM equivalencies WHERE grup_gp!=' ' AND altres!=' ';";
    $result=mysql_query($sql);	
    if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}      
    // Posem a equivalendies les relacions entre els codis de grups i el número amb el que es gestiona al fitxer
    $exporthorarixml=$_SESSION['upload_horaris'];
    //echo $exporthorarixml."<br>";
    $resultatconsulta=simplexml_load_file($exporthorarixml);
    if ( !$resultatconsulta ) {echo "Carrega programa horaris fallida";}
    else 
        {
        foreach($resultatconsulta->DATOS-> GRUPOS -> GRUPO as $grup)
            {
            $sql="INSERT INTO equivalencies(grup_gp,altres) VALUES ('".$grup[abreviatura]."','".$grup[num_int_gr]."');";
            $result=mysql_query($sql);	
            if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}        
            }
        }



    if ( !$resultatconsulta ) {echo "Carrega programa horaris fallida";}
    else 
        {
        foreach($resultatconsulta->HORARIOS-> HORARIOS_ASIGNATURAS -> HORARIO_ASIG as $assig)
            {
            $materia = $assig[hor_num_int_as];
            // Extreiem la informació per veure si l'agrupament ja exiteix
            foreach ($assig -> ACTIVIDAD as $activitat)
                {
                $nombre_grups = $activitat -> GRUPOS_ACTIVIDAD[tot_gr_act];
                if ($nombre_grups >=1)
                    {
                    $agrupament = "";
                    $codi_agrupament = "";
                    for ( $i=1; $i<=$nombre_grups ;$i++)
                        {
                        $grup= $activitat -> GRUPOS_ACTIVIDAD['grupo_'.$i];
                        // Cerquem el grup que li correspon
                        if ($grup != '')
                            {
                            if ($i == 1) {$codi_agrupament=$grup;}
                            else {$codi_agrupament=$codi_agrupament."_".$grup;}
                            //echo "Materia >> ".$materia;
                            $sql = "SELECT grup_gp FROM equivalencies WHERE altres = '".$grup."';";
                            //echo "<br>".$sql;
                            $result=mysql_query($sql);	
                            if (!$result) {die(_ERR_AGRUPS1 . mysql_error());}
                            $fila = mysql_fetch_row($result);
                            $nom_grup = $fila[0];
                            if ($i == 1) {$agrupament=$fila[0];}
                            else {$agrupament=$agrupament."_".$fila[0];}
                            }
                        //if ($nombre_grups>4)  {echo "<br>".$nombre_grups." >>".$agrupament;}
                        }
                      //echo "<br>".$agrupament;
                      //Extreiem el tron
                      $sql = "SELECT idtorn FROM grups WHERE codi_grup = '".$nom_grup."';"; 
                      //echo "<br>".$sql;
                      $result=mysql_query($sql);	
                      if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}                     
                      $fila = mysql_fetch_row($result);
                      $id_torn = $fila[0];
                      // Extreiem el pla d'estudis
                      $sql = "SELECT pla_saga FROM equivalencies WHERE grup_gp = '".$grup."' AND grup_ga !='';"; 
                      //echo "<br>".$sql;
                      $result=mysql_query($sql);	
                      if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}                     
                      $fila = mysql_fetch_row($result);
                      $id_pla = $fila[0];                     
                      
                      if (($id_torn != '') AND ($id_pla != ''))
                         {	
                        $sql = "SELECT COUNT(codi_grup) FROM grups WHERE codi_grup = '".$agrupament."';";  
                        $result=mysql_query($sql);	
                        //echo "<br>".$sql   ; 
                        if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}                     
                        $fila = mysql_fetch_row($result);
                        //echo "<br>comptar: ".$fila[0]  ; 
                        if ($fila[0] == 0)
                            {
                            $sql="INSERT grups(codi_grup,nom,idtorn) ";
                            $sql.="VALUES ('".$agrupament."','".$agrupament."','".$id_torn."');";
                            //echo $sql;
                            $result=mysql_query($sql);	
                            if (!$result) {die(_ERR_INSERT_GROUPS_1 . mysql_error());}
                            //Extreiem l'identificador
                            $id_grup=extreu_id('grups','codi_grup','idgrups',$agrupament);    

                            //Desem l'emparellament a la taula equivalencies per quan s'hagin de carregat els alumnes i matèries
                            $sql="INSERT INTO equivalencies(grup_gp,grup_ga,pla_saga) VALUES ('".$codi_agrupament."','".$id_grup."','".$id_pla."');";
                            $result=mysql_query($sql);	
                            if (!$result) {die(_ERR_INSERT_GROUPS_3 . mysql_error());}     
                            }
                        }
                    }
                }		
            }   
        } 
    $sql="DELETE FROM equivalencies WHERE ((grup_gp!='') AND (altres!=''));";
    $result=mysql_query($sql);	
    if (!$result) {die(_ERR_DELTE-RELATIONS . mysql_error());}
        
    }   

 function torna_torn_pla_HW($grup)
   {}
   
 function torna_torn($grup)
   {
   // Aprofitem treure el torn per veure si és un grup de docència i està en la taula equivalencies
   $sql="SELECT A.idtorn FROM grups A, equivalencies B WHERE B.grup_saga=A.codi_grup AND B.grup_gp='".$grup."';";
   //echo "<br>".$sql;
   $result=mysql_query($sql);if (!$result) {	die(_ERROR_SELECT_TORN.mysql_error());}
   $present=mysql_num_rows($result);
   if ($present==0)
      {
      // Aprofitem treure el torn per veure si és un grup de docència i està en la taula grups
      $sql="SELECT idtorn FROM grups WHERE codi_grup='".$grup."';";
      $result=mysql_query($sql);if (!$result) {	die(_ERROR_SELECT_TORN(2).mysql_error());}
      }
   $fila = mysql_fetch_row($result);$id_torn = $fila[0];    
   return $id_torn;
   }

 function torna_pla($grup)
   {
   // Aprofitem treure el torn per veure si és un grup de docència i està en la taula equivalencies
   $sql="SELECT pla_saga FROM equivalencies WHERE grup_gp='".$grup."';";
   //echo "<br>".$sql;
   $result=mysql_query($sql);if (!$result) {	die(_ERROR_SELECT_TORN.mysql_error());}
   $fila = mysql_fetch_row($result);$id_pla = $fila[0];    
   return $id_pla;
   }   
   
function emparella_moduls_gp_DUAL_cali2($moduls,$materies)
    {}   
function emparella_cali2_Logse($moduls,$materia)
    {}   
function matricula_alumnes($idgrupmateria,$grup_saga)
    {}
function matricula_alumnes_logse($materia)
    {}
   
function alta_materies($materia,$id_pla)
    {
    
    for($i=0;$i<count($materia);$i++)
       {
       $nom_materia = $materia[$i][1];
       $nom_materia = neteja_apostrofs($nom_materia);
       $codi_materia = $materia[$i][0];
       $codi_materia = neteja_apostrofs($codi_materia);
       $nom_materia="(".$codi_materia.")".$nom_materia;
       // Inserció a moduls_materies_ufs
       $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
       $sql.="VALUES ('".$id_pla."','".$codi_materia."','S');";
       //echo $sql."<br>";
       $result=mysql_query($sql);	
       if (!$result) {die(_ERR_INSERT_SUBJECT1_ESO . mysql_error());}	
       $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$codi_materia);
       // Inserció a la taula materies
       $sql="INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
       $sql.="VALUES ('".$id_taula_materies."','".$codi_materia."','".$nom_materia."');";
       //echo $sql."<br>";
       $result=mysql_query($sql);	
       if (!$result) 
          {die(_ERR_INSERT_SUBJECT2_ESO . mysql_error());}									
       }

    }

function alta_moduls($moduls)
    {
    
//    for($i=0;$i<count($moduls);$i++)
//       {
//       $nom_materia = $moduls[$i][1];
//       $nom_materia = neteja_apostrofs($nom_materia);
//       $codi_materia = $moduls[$i][0];
//       $codi_materia = neteja_apostrofs($codi_materia);
//       $nom_materia="(".$codi_materia.")".$nom_materia;
//       }
    print("<form method=\"post\" action=\"./emparella_moduls.php\" enctype=\"multipart/form-data\" id=\"profform\">");
    print("<table align=\"center\">");
    print("<tr><td align=\"center\" colspan=\"5\">");
    print("<h3>Carregats ja tots els móduls i unitats formatives de Saga</h3><h3>Per poder carregar les unitats formatives, necessitem els emparellaments</h3></td></tr>");
    print("<tr align=\"center\" bgcolor=\"#635656\" ><td></td><td>Cicle formatiu</td><td></td><td>Móduls de SAGA</td><td>Móduls programa horaris</td></tr>");
    $pos=1;
    $sql="SELECT A.idplans_estudis,A.Nom_plan_estudis,B.idmoduls,B.nom_modul FROM plans_estudis A, moduls B ";
    $sql.="WHERE A.idplans_estudis=B.idplans_estudis";
    //echo $sql;
    $result=mysql_query($sql);
    if (!$result) 
            {die(_ERR_SELECT_PLA_MODULS. mysql_error());}
    $pos=1;
    while($fila=mysql_fetch_row($result))
            {
            print("<tr><td><input type=\"text\" name=\"id_pla_".$pos."\" value=\"".$fila[0]."\" HIDDEN ></td>");
            print("<td><input type=\"text\" name=\"nom_pla_".$pos."\" value=\"".$fila[1]."\" size=\"50\" ></td>");
            print("<td><input type=\"text\" name=\"id_modul_".$pos."\" value=\"".$fila[2]."\" HIDDEN></td>");
            print("<td><input type=\"text\" name=\"nom_modul_".$pos."\" value=\"".$fila[3]."\" size=\"50\"></td>");
            print("<td><select name=\"nom_modul_gp_".$pos."\" ");
            print(">");
            print("<option value=\"0\">---Cap correspondència---</option>");
            foreach ( $moduls as $materia) 
                    {
                    $codi_materia=$materia[0];
                    $codi_materia=neteja_apostrofs($codi_materia);
                    $nom_materia=$materia[1];
                    $nom_materia="(".$codi_materia.")".$nom_materia;
                    print("<option value=\"".$codi_materia."\">".$nom_materia."</option>");
                    }
            print("</select>");
            print("</tr>");
            $pos++;
            }		
    $pos--;
    print("<tr><td align=\"center\" colspan=\"3\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
    print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
    print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\"  ></td></tr>");
    print("</table>");    
    }   


function emparella_moduls_gp_DUAL_cali($moduls)
    {}   
function emparella_moduls_gp_DUAL($moduls)
    {}
function emparella_moduls_gp()
    {}
function emparella_moduls_pena()
    {}

function select_plaestudis_saga()
	{
	// Carreguem tot del fitxer de SAGA
        // Prèviament haurem indicat si volem carrgar tot de nou o 
        // aprofitar la informació del fixxer de SAGA encara que sigui antiga
        $exportsagaxml=$_SESSION['upload_saga'];
        $resultatconsulta=simplexml_load_file($exportsagaxml);
	if ( !$resultatconsulta ) {echo "Carrega fallida";}
	else 
            {
            echo "<br>Carrega correcta";
            print("<form method=\"post\" action=\"./relaciona_pla_estudis_saga.php\" enctype=\"multipart/form-data\" id=\"profform\">");
            print("<table>");
            print("<tr><td align=\"center\" colspan=\"4\"><h1>INSTRUCCIONS:<br></h1>");
            print("<h3>Selecciona la modalitat de cada pla d'estudis del fitxer de SAGA<br></h3>");
            print("<tr align=\"center\" bgcolor=\"#635656\" ><td>Etapa(S)</td><td>Subetapa(S)</td><td>Nom(S)</td><td>Pla d'estudis a escollir</td></tr>");
            $pos=1;
            foreach ($resultatconsulta->{'plans-estudi'}->{'pla-estudis'} as $pla)
                    {
                    $codi_etapa=$pla[etapa];
                    $codi_subetapa=$pla[subetapa];
                    $nom_pla=$pla[nom];
                    print("<tr ");
                    if ((($pos/5)%2)=="0") 
                            {print("bgcolor=\"#3f3c3c\"");}
                    print("><td><input type=\"text\" name=\"placurt".$pos."\" VALUE=\"".$codi_etapa."\" SIZE=\"10\" ></td>");
                    print("<td><input type=\"text\" name=\"plamig".$pos."\" VALUE=\"".$codi_subetapa."\" SIZE=\"10\" ></td>");
                    print("<td><input type=\"text\" name=\"plallarg".$pos."\" VALUE=\"".$nom_pla."\" SIZE=\"55\"></td>");
                    print("<td><select name=\"etapa".$pos."\">");
                    print("<option value=\"0\">---</option>");
                    print("<option value=\"4\">PRIMÀRIA (No disponible)</option>");
                    print("<option value=\"1\">ESO/BAT/CAS</option>");
                    print("<option value=\"2\">CF LOE</option>");
                    print("<option value=\"3\">CF LOGSE</option>");
                    print("</select></td>");
                    print("</tr> ");
                    $pos++;
                    }
            $pos--;
            print("<tr><td align=\"center\" colspan=\"8\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
            print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
            print("<tr><td align=\"center\" colspan=\"4\"><input type=\"text\" name=\"recompte\" value=\"".$pos."\" HIDDEN ></td></tr>");
            print("</table>");
            print("</form>");
            }
	}

	
function neteja_item_grup_materia($cadena_grups)
	{
	//$cadena_grups="MAT3/ESO3A/ESO3B/ESO3C#4";
	$cadena_grup=explode('#',$cadena_grups);
	$cadena_grup2=substr(substr($cadena_grup[0],strpos($cadena_grup[0],'/')),1);
	return $cadena_grup2;
	}

   
function carrega_CCFF_de_SAGA()
    {
    
    require_once('../../bbdd/connect.php');

    $exportsagaxml=$_SESSION['upload_saga'];
    $exporthorarixml=$_SESSION['upload_horaris'];
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    if ( !$resultatconsulta ) {echo "Carrega fallida";}
        {
        $sql = "SELECT COUNT(idunitats_formatives) FROM unitats_formatives;";
        $result=mysql_query($sql);$files0=mysql_fetch_row($result);
        foreach($resultatconsulta->{'plans-estudi'}->{'pla-estudis'} as $pla)
            {
            $cad = "LOE";
            $pos = strpos($pla[nom],$cad);

            if ((($pla[etapa]=="CFPM") OR ($pla[etapa]=="CFPS")) AND ($pos !== false) AND ($files0[0] == 0))
                {
                $pla[nom]=  neteja_apostrofs($pla[nom]);
                $acronim=$pla[etapa]."(".$pla[subetapa].")";
                $id_pla=extreu_id(plans_estudis,Acronim_pla_estudis,idplans_estudis,$acronim);
                if ($id_pla=='')
                    {
                    $sql="INSERT plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) ";
                    $sql.="VALUES ('S','".$pla[nom]."','".$pla[etapa]."(".$pla[subetapa].")');";
                    $result=mysql_query($sql);
                    //echo $sql."<br>";
                    if (!$result) 	{die(_ERR_INSERT_PLA_ESTUDIS . mysql_error());}	

                    $id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,$pla[nom]);
                    }
                    // Retoquem la taula d'equivalències ja que al donar d'alta els grups, els plasn d'estudis encaa no estaven 
                    // donats d'alta i per tant no es podia desar el identificador sinó el nom.
                    $sql="UPDATE equivalencies SET pla_saga='".$id_pla."' WHERE pla_saga='".$pla[subetapa]."'; ";
                    $result=mysql_query($sql);	
                    if (!$result) 	{die(_ERR_RENAME_PLA_ESTUDIS . mysql_error());} 

                    $id_pla=extreu_id(plans_estudis,Acronim_pla_estudis,idplans_estudis,$acronim);
                    
                    $codi_subetapa=$pla[subetapa];
                    foreach ($pla->contingut as $materies)
                        {
                        if (strlen($materies[codi])=="3")
                            {
                            //echo "mòdul".$materies[codi]."<br>";
                            $materies[nom]=neteja_apostrofs($materies[nom]);
                            $sql="INSERT IGNORE INTO moduls(idplans_estudis,nom_modul,codi_modul) ";
                            $sql.="VALUES ('".$id_pla."','(".$codi_subetapa.")".$materies[nom]."','".$materies[codi]."');";
                            //echo $sql."<br>";
                            $result=mysql_query($sql);	
                            if (!$result) 
                               {die(_ERR_INSERT_MOD_CCFF . mysql_error());}
                            }
                        }
                    foreach ($pla->contingut as $materies)
                        {
                        if (strlen($materies[codi])=="5")
                           {
                           $codi_materia=$codi_subetapa."_".$materies[codi];
                           // Introduim a la taula general de matèries
                           $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
                           $sql.="VALUES ('".$id_pla."','".$materies[id]."','S');";
                           //echo $sql."<br>";
                           $result=mysql_query($sql);	
                           if (!$result) 
                              {die(_ERR_INSERT_UF_GENERAL_TABLE . mysql_error());}	

                           // Extreiem l'identificador de la mattèria
                           $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$materies[id]);

                           // Extreiem l'identificador del módul
                           //echo "UF".$materies[codi]."<br>";
                           $codi_modul=substr($materies[codi],0,3);
                           $sql="SELECT idmoduls FROM moduls WHERE (idplans_estudis='".$id_pla."' AND codi_modul='".$codi_modul."');";
                           //echo $sql."<br>";
                           $result=mysql_query($sql);
                           if (!$result) 
                              {die(_ERR_EXTRACT_MOD_ID . mysql_error());}

                           // Comprovem que el módul existeix. Et pots trobar en els casos en que un módul només tingui una UF
                           //amb una UF que no té el seu módul corresponent
                           $files=mysql_num_rows($result);
                           //echo "<br>".$files."<br>";
                           if ($files==0)
                              {
                              $sql="INSERT IGNORE INTO moduls(idplans_estudis,nom_modul,codi_modul) ";
                              $sql.="VALUES ('".$id_pla."','".$codi_modul."_".$nom_pla."_No en saga','".$codi_modul."');";
                              //echo "<br>".$sql."<br>";
                              $result=mysql_query($sql);	
                              if (!$result) 
                                 {die(_ERR_INSERT_MODUL_FICTICI . mysql_error());}
                              //Extreiem ara el seu id per poer seguir
                              $sql="SELECT idmoduls FROM moduls WHERE (idplans_estudis='".$id_pla."' AND codi_modul='".$codi_modul."');";
                              //echo $sql."<br>";
                              $result=mysql_query($sql);
                              if (!$result) 
                                 {die(_ERR_EXTRACT_MOD_ID . mysql_error());}
                              }
                            $modul_id=mysql_result($result,0);
                              //echo $modul_id."<br>";								

                           // Extreiem dates de periode escolar
                           $sql2="SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S'";
                           //echo $sql2."<br>";
                           $result2=mysql_query($sql2);
                           $fila=mysql_fetch_row($result2);
                           // Inserim la UF
                           $materies[nom]=neteja_apostrofs($materies[nom]);
                           $sql="INSERT IGNORE INTO unitats_formatives(idunitats_formatives,nom_uf,hores,codi_uf,data_inici,data_fi) ";
                           $sql.="VALUES ('".$id_taula_materies."','".$materies[nom]."',50,'".$codi_materia."','".$fila[0]."','".$fila[1]."');";
                           //echo $sql."<br>";
                           $result=mysql_query($sql);	
                           if (!$result) 
                              {die(_ERR_INSERT_UF_CCFF . mysql_error());}


                           // Inserim el registre que relaciona el módul i la UF
                           $sql="INSERT IGNORE INTO moduls_ufs(id_moduls,id_ufs) ";
                           $sql.="VALUES ('".$modul_id."','".$id_taula_materies."');";
                           //echo $sql."<br>";
                           $result=mysql_query($sql);	
                           if (!$result) 
                              {die(_ERR_INSERT_RELATE_MODXUF . mysql_error());}
                           }
                        }	
                    }
                }
            }
        }
//   }

function carrega_plans_estudis()
    {
   
    require_once('../../bbdd/connect.php');
	
    $exportsagaxml=$_SESSION['upload_saga'];
    $exporthorarixml=$_SESSION['upload_horaris'];
    $resultatconsulta=simplexml_load_file($exportsagaxml);
    if ( !$resultatconsulta ) {echo "Carrega fallida";}
        {
        foreach($resultatconsulta->{'plans-estudi'}->{'pla-estudis'} as $pla)
            {
            $pla[nom]=  neteja_apostrofs($pla[nom]);
            $acronim=$pla[etapa]."(".$pla[subetapa].")";
            $id_pla=extreu_id(plans_estudis,Acronim_pla_estudis,idplans_estudis,$acronim);
            if ($id_pla=='')
                {
                $sql="INSERT plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) ";
                $sql.="VALUES ('S','".$pla[nom]."','".$pla[etapa]."(".$pla[subetapa].")');";
                $result=mysql_query($sql);
                //echo $sql."<br>";
                if (!$result) 	{die(_ERR_INSERT_PLA_ESTUDIS . mysql_error());}	

                //$id_pla=extreu_id(plans_estudis,Nom_plan_estudis,idplans_estudis,$pla[nom]);
                }
            }	
        }
                
    }
    
   
function intro_mat_GP($resultatconsulta,$id_pla)
    {
    foreach ($resultatconsulta->subjects->subject as $materia)
     {
     $nom_materia=$materia->longname;
     $nom_materia=neteja_apostrofs($nom_materia);
     $codi_materia=$materia[id];
     $codi_materia=neteja_apostrofs($codi_materia);
     $nom_materia="(".$codi_materia.")".$nom_materia;
     // Inserció a moduls_materies_ufs
     $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
     $sql.="VALUES ('".$id_pla."','".$codi_materia."','S');";
     //echo $sql."<br>";
     $result=mysql_query($sql);	
     if (!$result) {die(_ERR_INSERT_SUBJECT1_ESO . mysql_error());}	
     $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$codi_materia);
     // Inserció a la taula materies
     $sql="INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
     $sql.="VALUES ('".$id_taula_materies."','".$codi_materia."','".$nom_materia."');";
     //echo $sql."<br>";
     $result=mysql_query($sql);	
     if (!$result) 
        {die(_ERR_INSERT_SUBJECT2_ESO . mysql_error());}									
     }
    }
   
 function intro_mat_PN ($resultatconsulta,$id_pla)
    {
     foreach ($resultatconsulta->materias->materia as $materia)
         {
         $nom_materia=$materia->nombreCompleto;
         $nom_materia=neteja_apostrofs($nom_materia);
         $codi_materia=$materia->nombre;
         $codi_materia=neteja_apostrofs($codi_materia);
         $nom_materia="(".$codi_materia.")".$nom_materia;
         //echo $nom_materia." >> ".$codi_materia."<br>";
         // Inserció a moduls_materies_ufs
         $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
         $sql.="VALUES ('".$id_pla."','".$codi_materia."','S');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) {die(_ERR_INSERT_SUBJECT1_CCFF . mysql_error());}	
         $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$codi_materia);
         // Inserció a la taula materies
         $sql="INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
         $sql.="VALUES ('".$id_taula_materies."','".$codi_materia."','".$nom_materia."');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) 
         {die(_ERR_INSERT_SUBJECT2_CCFF . mysql_error());}									
         }
    }
function intro_mat_KW ($resultatconsulta,$id_pla)
    {
     foreach ($resultatconsulta->NOMASIGT->NOMASIGF as $materia)
         {
         $nom_materia=$materia['NOMBRE'];
         $nom_materia=neteja_apostrofs($nom_materia);
         $codi_materia=$materia['ABREV'];
         $codi_materia=neteja_apostrofs($codi_materia);
         $nom_materia="(".$codi_materia.")".$nom_materia;
         //echo $nom_materia." >> ".$codi_materia."<br>";
         // Inserció a moduls_materies_ufs
         $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
         $sql.="VALUES ('".$id_pla."','".$codi_materia."','S');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) {die(_ERR_INSERT_SUBJECT1_CCFF . mysql_error());}	
         $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$codi_materia);
         // Inserció a la taula materies
         $sql="INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
         $sql.="VALUES ('".$id_taula_materies."','".$codi_materia."','".$nom_materia."');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) 
         {die(_ERR_INSERT_SUBJECT2_CCFF . mysql_error());}									
         }
    }     

 function intro_mat_HW ($resultatconsulta,$id_pla)
    {
     //echo "Hola";
     foreach ($resultatconsulta->DATOS->ASIGNATURAS->ASIGNATURA as $materia)
     //foreach ($resultatconsulta->materias->materia as $materia)
         {
         $nom_materia=$materia['nombre'];
         $nom_materia=neteja_apostrofs($nom_materia);
         $codi_materia=$materia['abreviatura'];
         $codi_materia=neteja_apostrofs($codi_materia);
         $nom_materia="(".$codi_materia.")".$nom_materia;
         echo $nom_materia." >> ".$codi_materia."<br>";
         // Inserció a moduls_materies_ufs
         $sql="INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
         $sql.="VALUES ('".$id_pla."','".$codi_materia."','S');";
         echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) {die(_ERR_INSERT_SUBJECT1_CCFF . mysql_error());}	
         $id_taula_materies=extreu_id(moduls_materies_ufs,codi_materia,id_mat_uf_pla,$codi_materia);
         // Inserció a la taula materies
         $sql="INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
         $sql.="VALUES ('".$id_taula_materies."','".$codi_materia."','".$nom_materia."');";
         //echo $sql."<br>";
         $result=mysql_query($sql);	
         if (!$result) 
         {die(_ERR_INSERT_SUBJECT2_CCFF . mysql_error());}									
         }         
     
     
    }   
    

   
   
   ?>
