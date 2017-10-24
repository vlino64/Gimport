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
include("../funcions/funcions_generals.php");
include("../funcions/func_prof_alum.php");

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
</head>

<body>
<?php

	require_once('../../bbdd/connect.php');

	$recompte=$_POST['recompte'];
	$noap=0;
	//echo $recompte;
	// Crearem el csv
	$myFile = "../uploads/professorat.csv";
	$fh = fopen($myFile, 'w') or die("can't open file");
	$stringData="nom_i_cognoms,usuari,login,password\n";		
	fwrite($fh, $stringData);
		
	for ($i=1;$i<=$recompte;$i++)
		{
		$nom=$_POST['nomprofsaga'.$i];
		$cognom1=$_POST['cog1profsaga'.$i];
		$cognom2=$_POST['cog2profsaga'.$i];
		$id_saga=$_POST['idprofsaga'.$i];
		$id_gassist=$_POST['id_gass'.$i];
		$id_gp=$_POST['id_gp'.$i];
		//echo $i."<br>";
		
		// *************************
		// Fem l'alta
		// *************************
		$alta="alta".$i;
		// Ha d'arribar alta activat i, 
      //    o bé no està definit gassist ja que es tracta de la primer càrrega i no està nipresent en el formulari
      //    o bé està definit però no té assignat 
		if($_POST[$alta]) 
			{
			//echo "Entra ALTA<br>";
			$user=$nom." ".$cognom1." ".$cognom2;
			$nom_complet=$user;
			$pass="";
			
			genera($user,$pass,$nom,$cognom1,$cognom2);
			// En aquest cas no utiltzem a generació del password. Agafem com password el nom d'usuari
			// i que actualitzen la password qun facin login
			$pass=md5($user);
			//echo $user.">>>".$pass;
			
			$sql="INSERT INTO `professors`(codi_professor,activat) ";
			$sql.="VALUES ('".$user."','S');";
			//echo $sql."<br>";
			$result=mysql_query($sql);
			if (!$result) 
				{
				die(_ERR_INSERT_TEACHERS . mysql_error());
				}
			//echo "S'ha insertat ".$nom;echo "<br>";
			
			$camps=array();
			recuperacampdedades($camps,$db);
			
			$id=extreu_id(professors,codi_professor,idprofessors,$user);
			//echo "<br>".$id;
						
			$sql="INSERT INTO `contacte_professor`(id_professor,id_tipus_contacte,Valor) ";
			$sql.="VALUES ('".$id."','".$camps[nom_complet]."','".$nom_complet."'),";
			$sql.="('".$id."','".$camps[login]."','".$user."'),";
			$sql.="('".$id."','".$camps[iden_ref]."','".$id_saga."'),";
			$sql.="('".$id."','".$camps[nom_profe]."','".$nom."'),";
			$cognoms=$cognom1." ".$cognom2;
			$sql.="('".$id."','".$camps[cognoms_profe]."','".$cognoms."'),";
			$sql.="('".$id."','".$camps[contrasenya]."','".$pass."');";
			//echo $sql."<br>";
			$result=mysql_query($sql);
			if (!$result) 
				{
				die(_ERR_INSERT_TEACHER_CONTACT . mysql_error());
				}
			else
				{
				//print("Professor donat d'alta: ".$nom." ".$cognoms.". Usuari d'accès: ".$user."<br>---<br>");
				//Escrivim en el csv
				$stringData=$nom.",".$cognoms.",".$user.",".$user."\n";		
				fwrite($fh, $stringData);
				}
			$id_gassist=$id;
			}
			
	
					
		// *************************
		// Fem l'actualització
		// *************************
		

		if($id_gassist!="") 
			{
			//echo $id_gassist."<br>";		
			$camps=array();
			recuperacampdedades($camps,$db);
									
			$sql="SELECT COUNT(*) FROM contacte_professor WHERE ((id_professor=\"".$id_gassist."\") AND(id_tipus_contacte='".$camps[iden_ref]."'));";
			//echo $sql;
			$result=mysql_query($sql);
			$present=mysql_result($result,0);
			if ($present == 0)
				{
				$sql="INSERT INTO contacte_professor(id_professor,id_tipus_contacte,Valor) VALUES ('".$id_gassist."','".$camps[iden_ref]."','".$id_saga."') ";
				}
			else
				{
				$sql="UPDATE `contacte_professor` SET `Valor`='".$id_saga."' WHERE ((id_professor=\"".$id_gassist."\") AND(id_tipus_contacte='".$camps[iden_ref]."'));";
				}
			$result=mysql_query($sql);
			//echo $sql;
			if (!$result) 
				{
				die(_ERR_INSERT_TEACHER_SAGA_ID . mysql_error());
				}
			//print("<br>El professor/a ".$nom." ".$cognom1." ".$cognom2." ".$id."ha estat actualitzat a gassist<br>");
			$id=$id_gassist;
			}
		
		//Crearem els emparellaments entre professor a gassist i al programa d'horaris sempre que no existeixi aquest emparellament
		$sql="SELECT prof_gp FROM equivalencies WHERE prof_ga='".$id."';";
		//echo $sql."<br>";
		$result=mysql_query($sql); if (!$result) {die(_ERROR1_.mysql_error());}			
		$prof_gp=mysql_result($result,0);
		
		if (($prof_gp=='') AND ($id_gp!=''))
			{
			$sql = "UPDATE equivalencies SET prof_ga='".$id_gassist."' WHERE codi_prof_gp = '".$id_gp."';";
                        //echo $sql."<br>";
			$result=mysql_query($sql); if (!$result) {die(_ERROR2_.mysql_error());}
			}


		}
		fclose($fh);

                // De la taula equivalencies, eliminem els professors als que nos se'la ha assignat identificador gassist
		$sql="DELETE FROM `equivalencies` WHERE prof_ga IS NULL AND nom_prof_gp != '' AND codi_prof_gp != '';";
                echo "<br>".$sql;
		$result=mysql_query($sql);
		if (!$result) {	die(_ERR_DELETE_PROF . mysql_error());}
		
		

		// Passem a historic tot el professorat que està desactivat
		$sql="UPDATE `professors` SET `historic`='S' WHERE activat='N';";
		$result=mysql_query($sql);
		if (!$result) {	die(_ERR_DEACT_PROF . mysql_error());}
		//echo "\nS'ha passat a històric tot el professorat que a hores d'ara estava desactivat !<br><br>";
		introduir_fase('professorat','1');
		die("<script>location.href = './menu.php'</script>");
		
?>
</body>

	




